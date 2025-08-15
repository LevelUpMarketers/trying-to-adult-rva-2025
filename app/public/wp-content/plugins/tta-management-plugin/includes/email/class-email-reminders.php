<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Schedule and send event reminder emails.
 */
class TTA_Email_Reminders {
    /** Initialize hooks. */
    public static function init() {
        add_action( 'tta_attendee_reminder_email', [ __CLASS__, 'send_attendee_reminder' ], 10, 2 );
        add_action( 'tta_host_reminder_email', [ __CLASS__, 'send_host_reminder' ], 10, 2 );
        add_action( 'tta_volunteer_reminder_email', [ __CLASS__, 'send_volunteer_reminder' ], 10, 2 );
        add_action( 'tta_post_event_thanks_email', [ __CLASS__, 'send_post_event_thanks' ], 10, 1 );
    }

    /**
     * Schedule reminder emails for an event.
     *
     * @param int $event_id Event ID.
     */
    public static function schedule_event_emails( $event_id ) {
        $event_id = intval( $event_id );
        if ( ! $event_id ) {
            return;
        }

        $ute_id = tta_get_event_ute_id( $event_id );
        if ( ! $ute_id ) {
            return;
        }

        $event = tta_get_event_for_email( $ute_id );
        if ( empty( $event['date'] ) ) {
            return;
        }

        $start = explode( '|', $event['time'] ?? '' )[0] ?? '00:00';
        $tz    = new DateTimeZone( 'America/New_York' );
        $dt    = DateTime::createFromFormat( 'Y-m-d H:i', $event['date'] . ' ' . $start, $tz );
        if ( ! $dt ) {
            return;
        }
        $start_ts = $dt->getTimestamp();

        self::schedule_single( $start_ts - DAY_IN_SECONDS, 'tta_attendee_reminder_email', [ $event_id, 'reminder_24hr' ] );
        self::schedule_single( $start_ts - 3 * HOUR_IN_SECONDS, 'tta_attendee_reminder_email', [ $event_id, 'reminder_2hr' ] );
        self::schedule_single( $start_ts - DAY_IN_SECONDS, 'tta_host_reminder_email', [ $event_id, 'host_reminder_24hr' ] );
        self::schedule_single( $start_ts - 3 * HOUR_IN_SECONDS, 'tta_host_reminder_email', [ $event_id, 'host_reminder_2hr' ] );
        self::schedule_single( $start_ts - DAY_IN_SECONDS, 'tta_volunteer_reminder_email', [ $event_id, 'volunteer_reminder_24hr' ] );
        self::schedule_single( $start_ts - 3 * HOUR_IN_SECONDS, 'tta_volunteer_reminder_email', [ $event_id, 'volunteer_reminder_2hr' ] );
    }

    /**
     * Schedule a post-event thank you email 24 hours after archiving.
     *
     * @param int $event_id Event ID.
     */
    public static function schedule_post_event_thanks( $event_id ) {
        $event_id = intval( $event_id );
        if ( ! $event_id ) {
            return;
        }
        $timestamp = time() + DAY_IN_SECONDS;
        if ( wp_next_scheduled( 'tta_post_event_thanks_email', [ $event_id ] ) ) {
            return;
        }
        wp_schedule_single_event( $timestamp, 'tta_post_event_thanks_email', [ $event_id ] );
    }

    /**
     * Clear any scheduled reminder emails for an event.
     *
     * @param int $event_id Event ID.
     */
    public static function clear_event_emails( $event_id ) {
        $event_id = intval( $event_id );
        if ( ! $event_id ) {
            return;
        }

        $hooks = [
            'tta_attendee_reminder_email'  => [ 'reminder_24hr', 'reminder_2hr' ],
            'tta_host_reminder_email'      => [ 'host_reminder_24hr', 'host_reminder_2hr' ],
            'tta_volunteer_reminder_email' => [ 'volunteer_reminder_24hr', 'volunteer_reminder_2hr' ],
            'tta_post_event_thanks_email'  => [ null ],
        ];

        foreach ( $hooks as $hook => $keys ) {
            foreach ( $keys as $key ) {
                $args = null === $key ? [ $event_id ] : [ $event_id, $key ];
                wp_clear_scheduled_hook( $hook, $args );
            }
        }
    }

    /**
     * Schedule a single cron event if future and not already scheduled.
     */
    protected static function schedule_single( $timestamp, $hook, array $args ) {
        if ( $timestamp <= time() ) {
            return;
        }
        if ( wp_next_scheduled( $hook, $args ) ) {
            return;
        }
        wp_schedule_single_event( $timestamp, $hook, $args );
    }

    /**
     * Send attendee reminder emails.
     */
    public static function send_attendee_reminder( $event_id, $template_key ) {
        $event_id = intval( $event_id );
        $templates = tta_get_comm_templates();
        if ( empty( $templates[ $template_key ] ) ) {
            return;
        }
        $ute_id = tta_get_event_ute_id( $event_id );
        if ( ! $ute_id ) {
            return;
        }
        $event     = tta_get_event_for_email( $ute_id );
        $attendees = tta_get_event_attendees_with_status( $ute_id );
        if ( empty( $event ) || empty( $attendees ) ) {
            return;
        }
        $tpl     = $templates[ $template_key ];
        $headers = [ 'Content-Type: text/html; charset=UTF-8' ];
        foreach ( $attendees as $att ) {
            $context = [
                'user_email' => sanitize_email( $att['email'] ),
                'first_name' => sanitize_text_field( $att['first_name'] ),
                'last_name'  => sanitize_text_field( $att['last_name'] ),
                'member'     => [],
            ];
            $tokens      = self::build_tokens( $event, $context, [ $att ] );
            $subject_raw = tta_expand_anchor_tokens( $tpl['email_subject'], $tokens );
            $subject     = tta_strip_bold( strtr( $subject_raw, $tokens ) );
            $body_raw    = tta_expand_anchor_tokens( $tpl['email_body'], $tokens );
            $body_txt    = tta_convert_bold( tta_convert_links( strtr( $body_raw, $tokens ) ) );
            $body        = nl2br( $body_txt );
            wp_mail( $context['user_email'], $subject, $body, $headers );
        }
    }

    /** Send host reminder emails. */
    public static function send_host_reminder( $event_id, $template_key ) {
        $emails = tta_get_event_host_volunteer_emails( $event_id, 'host' );
        self::send_generic_reminder( $event_id, $template_key, $emails );
    }

    /** Send volunteer reminder emails. */
    public static function send_volunteer_reminder( $event_id, $template_key ) {
        $emails = tta_get_event_host_volunteer_emails( $event_id, 'volunteer' );
        self::send_generic_reminder( $event_id, $template_key, $emails );
    }

    /**
     * Send thank you emails to attendees who checked in.
     *
     * @param int $event_id Event ID.
     */
    public static function send_post_event_thanks( $event_id ) {
        $event_id  = intval( $event_id );
        $templates = tta_get_comm_templates();
        if ( empty( $templates['post_event_review'] ) ) {
            return;
        }
        $ute_id = tta_get_event_ute_id( $event_id );
        if ( ! $ute_id ) {
            return;
        }
        $event = tta_get_event_for_email( $ute_id );
        $all   = tta_get_event_attendees_with_status( $ute_id );
        $attendees = array_filter( $all, function ( $a ) {
            return isset( $a['status'] ) && 'checked_in' === $a['status'];
        } );
        if ( empty( $event ) || empty( $attendees ) ) {
            return;
        }
        $tpl     = $templates['post_event_review'];
        $headers = [ 'Content-Type: text/html; charset=UTF-8' ];
        foreach ( $attendees as $att ) {
            $context = [
                'user_email' => sanitize_email( $att['email'] ),
                'first_name' => sanitize_text_field( $att['first_name'] ),
                'last_name'  => sanitize_text_field( $att['last_name'] ),
                'member'     => [],
            ];
            $tokens      = self::build_tokens( $event, $context, [ $att ] );
            $subject_raw = tta_expand_anchor_tokens( $tpl['email_subject'], $tokens );
            $subject     = tta_strip_bold( strtr( $subject_raw, $tokens ) );
            $body_raw    = tta_expand_anchor_tokens( $tpl['email_body'], $tokens );
            $body_txt    = tta_convert_bold( tta_convert_links( strtr( $body_raw, $tokens ) ) );
            $body        = nl2br( $body_txt );
            wp_mail( $context['user_email'], $subject, $body, $headers );
        }
    }

    /**
     * Generic reminder sender for hosts and volunteers.
     *
     * @param int      $event_id     Event ID.
     * @param string   $template_key Template key.
     * @param string[] $emails       Recipient list.
     */
    protected static function send_generic_reminder( $event_id, $template_key, array $emails ) {
        if ( empty( $emails ) ) {
            return;
        }
        $templates = tta_get_comm_templates();
        if ( empty( $templates[ $template_key ] ) ) {
            return;
        }
        $ute_id = tta_get_event_ute_id( $event_id );
        if ( ! $ute_id ) {
            return;
        }
        $event = tta_get_event_for_email( $ute_id );
        if ( empty( $event ) ) {
            return;
        }
        $tpl     = $templates[ $template_key ];
        $headers = [ 'Content-Type: text/html; charset=UTF-8' ];
        foreach ( $emails as $email ) {
            $context = [ 'user_email' => sanitize_email( $email ), 'first_name' => '', 'last_name' => '', 'member' => [] ];
            $tokens  = self::build_tokens( $event, $context, [] );
            $subject_raw = tta_expand_anchor_tokens( $tpl['email_subject'], $tokens );
            $subject     = tta_strip_bold( strtr( $subject_raw, $tokens ) );
            $body_raw    = tta_expand_anchor_tokens( $tpl['email_body'], $tokens );
            $body_txt    = tta_convert_bold( tta_convert_links( strtr( $body_raw, $tokens ) ) );
            $body        = nl2br( $body_txt );
            wp_mail( $email, $subject, $body, $headers );
        }
    }

    /**
     * Proxy to the email handler's token builder.
     */
    protected static function build_tokens( array $event, array $member, array $attendees, array $refund = [] ) {
        $email = TTA_Email_Handler::get_instance();
        $ref   = new ReflectionClass( $email );
        $m     = $ref->getMethod( 'build_tokens' );
        $m->setAccessible( true );
        return $m->invoke( $email, $event, $member, $attendees, $refund );
    }
}
