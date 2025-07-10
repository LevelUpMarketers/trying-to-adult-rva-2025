<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Handles outbound email notifications.
 */
class TTA_Email_Handler {
    /** @var self */
    protected static $instance;

    /**
     * Singleton accessor.
     *
     * @return self
     */
    public static function get_instance() {
        return self::$instance ?: ( self::$instance = new self() );
    }

    /** Initialize hooks. */
    private function __construct() {
        // Placeholder for future scheduling hooks.
    }

    /**
     * Send purchase confirmation emails for each event in the cart.
     *
     * @param array $items   Items array from TTA_Cart::get_items_with_discounts().
     * @param int   $user_id WordPress user ID who completed checkout.
     */
    public function send_purchase_emails( array $items, $user_id ) {
        $templates = tta_get_comm_templates();
        if ( empty( $templates['purchase'] ) ) {
            return;
        }
        $tpl = $templates['purchase'];

        $context = tta_get_current_user_context();
        if ( $context['wp_user_id'] !== intval( $user_id ) ) {
            $user = get_user_by( 'ID', $user_id );
            if ( $user ) {
                $context['wp_user_id'] = $user_id;
                $context['user_email'] = sanitize_email( $user->user_email );
                $context['first_name'] = sanitize_text_field( $user->first_name );
                $context['last_name']  = sanitize_text_field( $user->last_name );
            }
        }

        $by_event = [];
        foreach ( $items as $it ) {
            $by_event[ $it['event_ute_id'] ][] = $it;
        }

        foreach ( $by_event as $ute_id => $ev_items ) {
            $event = tta_get_event_for_email( $ute_id );
            if ( empty( $event ) ) {
                continue;
            }
            $attendees = [];
            foreach ( $ev_items as $it ) {
                foreach ( (array) ( $it['attendees'] ?? [] ) as $att ) {
                    $attendees[] = $att;
                }
            }

            $tokens = $this->build_tokens( $event, $context, $attendees );
            $subject = strtr( $tpl['email_subject'], $tokens );
            $body    = nl2br( strtr( $tpl['email_body'], $tokens ) );

            $recipients = array_unique( array_merge( [ $context['user_email'] ], array_column( $attendees, 'email' ) ) );
            $headers    = [ 'Content-Type: text/html; charset=UTF-8' ];
            foreach ( $recipients as $to ) {
                $to = sanitize_email( $to );
                if ( $to ) {
                    wp_mail( $to, $subject, $body, $headers );
                }
            }
        }
    }

    /**
     * Build a token replacement map for an event email.
     *
     * @param array $event     Event details from tta_get_event_for_email().
     * @param array $member    Current user context from tta_get_current_user_context().
     * @param array $attendees List of attendee arrays.
     * @return array
     */
    protected function build_tokens( array $event, array $member, array $attendees, array $refund = [] ) {
        $tokens = [
            '{event_name}'           => $event['name'] ?? '',
            '{event_address}'        => $event['address'] ?? '',
            '{event_link}'           => $event['page_url'] ?? '',
            '{dashboard_profile_url}'  => home_url( '/member-dashboard/?tab=profile', 'relative' ),
            '{dashboard_upcoming_url}' => home_url( '/member-dashboard/?tab=upcoming', 'relative' ),
            '{dashboard_past_url}'     => home_url( '/member-dashboard/?tab=past', 'relative' ),
            '{dashboard_billing_url}'  => home_url( '/member-dashboard/?tab=billing', 'relative' ),
            '{event_date}'           => isset( $event['date'] ) ? tta_format_event_date( $event['date'] ) : '',
            '{event_time}'           => isset( $event['time'] ) ? tta_format_event_time( $event['time'] ) : '',
            '{event_type}'           => $event['type'] ?? '',
            '{venue_name}'           => $event['venue_name'] ?? '',
            '{venue_url}'            => $event['venue_url'] ?? '',
            '{base_cost}'            => isset( $event['base_cost'] ) ? number_format( (float) $event['base_cost'], 2 ) : '',
            '{member_cost}'          => isset( $event['member_cost'] ) ? number_format( (float) $event['member_cost'], 2 ) : '',
            '{premium_cost}'         => isset( $event['premium_cost'] ) ? number_format( (float) $event['premium_cost'], 2 ) : '',
            '{first_name}'           => $member['first_name'] ?? '',
            '{last_name}'            => $member['last_name'] ?? '',
            '{email}'                => $member['user_email'] ?? '',
            '{phone}'                => $member['member']['phone'] ?? '',
            '{membership_level}'     => $member['membership_level'] ?? '',
            '{member_type}'          => $member['member']['member_type'] ?? '',
        ];

        for ( $i = 0; $i < 4; $i++ ) {
            $a = $attendees[ $i ] ?? [];
            $index = $i === 0 ? '' : ( $i + 1 );
            $tokens[ '{attendee' . $index . '_first_name}' ] = sanitize_text_field( $a['first_name'] ?? '' );
            $tokens[ '{attendee' . $index . '_last_name}' ]  = sanitize_text_field( $a['last_name'] ?? '' );
            $tokens[ '{attendee' . $index . '_email}' ]      = sanitize_email( $a['email'] ?? '' );
            $tokens[ '{attendee' . $index . '_phone}' ]      = sanitize_text_field( $a['phone'] ?? '' );
        }

        $att_ref = $refund['attendee'] ?? [];
        $tokens['{refund_first_name}'] = sanitize_text_field( $refund['first_name'] ?? $att_ref['first_name'] ?? '' );
        $tokens['{refund_last_name}']  = sanitize_text_field( $refund['last_name'] ?? $att_ref['last_name'] ?? '' );
        $tokens['{refund_email}']      = sanitize_email( $refund['email'] ?? $att_ref['email'] ?? '' );
        $tokens['{refund_amount}']     = isset( $refund['amount'] ) ? number_format( (float) $refund['amount'], 2 ) : '';
        $tokens['{refund_ticket}']     = sanitize_text_field( $refund['ticket_name'] ?? '' );
        $tokens['{refund_event_date}'] = isset( $event['date'] ) ? tta_format_event_date( $event['date'] ) : '';
        $tokens['{refund_event_time}'] = isset( $event['time'] ) ? tta_format_event_time( $event['time'] ) : '';

        return $tokens;
    }

    /**
     * Send a refund processed email to all attendees on the transaction.
     *
     * @param array $transaction Transaction row.
     * @param array $refund      Refund info (event_id, ticket_id, attendee, amount, ticket_name).
     */
    public function send_refund_emails( array $transaction, array $refund ) {
        $templates = tta_get_comm_templates();
        if ( empty( $templates['refund_processed'] ) ) {
            return;
        }
        $tpl = $templates['refund_processed'];

        $event_ute = tta_get_event_ute_id( intval( $refund['event_id'] ) );
        if ( ! $event_ute ) {
            return;
        }

        $event = tta_get_event_for_email( $event_ute );
        if ( empty( $event ) ) {
            return;
        }

        $context   = tta_get_user_context_by_id( intval( $transaction['wpuserid'] ) );
        $attendees = tta_get_transaction_event_attendees( $transaction['transaction_id'], $refund['event_id'] );

        $tokens  = $this->build_tokens( $event, $context, $attendees, $refund );
        $subject = strtr( $tpl['email_subject'], $tokens );
        $body    = nl2br( strtr( $tpl['email_body'], $tokens ) );

        $recipients = array_unique( array_merge( [ $context['user_email'] ], array_column( $attendees, 'email' ) ) );
        $headers    = [ 'Content-Type: text/html; charset=UTF-8' ];
        foreach ( $recipients as $to ) {
            $to = sanitize_email( $to );
            if ( $to ) {
                wp_mail( $to, $subject, $body, $headers );
            }
        }
    }
}
