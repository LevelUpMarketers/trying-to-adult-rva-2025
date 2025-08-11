<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Route wp_mail() calls through the SendGrid API.
 */
class TTA_SendGrid_Mailer {
    /**
     * Register the mail filter.
     */
    public static function init() {
        add_filter( 'pre_wp_mail', [ __CLASS__, 'send' ], 10, 2 );
    }

    /**
     * Send the email via SendGrid.
     *
     * @param mixed $null    Unused.
     * @param array $atts    wp_mail() arguments.
     * @return bool|WP_Error
     */
    public static function send( $null, $atts ) {
        if ( ! defined( 'TTA_SENDGRID_API_KEY' ) || ! TTA_SENDGRID_API_KEY ) {
            return null; // fall back to default mailer
        }

        $to          = $atts['to'];
        $subject     = $atts['subject'];
        $message     = $atts['message'];
        $headers     = $atts['headers'];
        $attachments = $atts['attachments'];

        try {
            $mail = new \SendGrid\Mail\Mail();

            list( $from_email, $from_name ) = self::parse_from( $headers );
            if ( ! $from_email ) {
                $from_email = get_bloginfo( 'admin_email' );
            }
            $from_name = $from_name ?: get_bloginfo( 'name' );
            $mail->setFrom( $from_email, $from_name );

            foreach ( (array) $to as $addr ) {
                $mail->addTo( $addr );
            }

            $mail->setSubject( $subject );

            $content_type = self::parse_content_type( $headers );
            if ( 'text/plain' === $content_type ) {
                $mail->addContent( 'text/plain', $message );
            } else {
                $mail->addContent( 'text/plain', wp_strip_all_tags( $message ) );
                $mail->addContent( 'text/html', $message );
            }

            $sg   = new \SendGrid( TTA_SENDGRID_API_KEY );
            $resp = $sg->send( $mail );

            $mail_data = compact( 'to', 'subject', 'message', 'headers', 'attachments' );

            if ( $resp->statusCode() >= 200 && $resp->statusCode() < 300 ) {
                do_action( 'wp_mail_succeeded', $mail_data );
                return true;
            }

            $error = new WP_Error( 'sendgrid_error', $resp->body(), $mail_data );
            do_action( 'wp_mail_failed', $error );
            return $error;
        } catch ( Exception $e ) {
            $mail_data = compact( 'to', 'subject', 'message', 'headers', 'attachments' );
            $error     = new WP_Error( 'sendgrid_exception', $e->getMessage(), $mail_data );
            do_action( 'wp_mail_failed', $error );
            return $error;
        }
    }

    /**
     * Extract the From header.
     */
    protected static function parse_from( $headers ) {
        $from_email = '';
        $from_name  = '';

        $lines = [];
        if ( is_array( $headers ) ) {
            $lines = $headers;
        } elseif ( is_string( $headers ) ) {
            $lines = explode( "\n", str_replace( "\r", "\n", $headers ) );
        }

        foreach ( $lines as $header ) {
            if ( 0 === stripos( $header, 'From:' ) ) {
                $parts = trim( substr( $header, 5 ) );
                if ( preg_match( '/(.*)<(.+)>/', $parts, $matches ) ) {
                    $from_name  = trim( $matches[1], '" ' );
                    $from_email = trim( $matches[2] );
                } else {
                    $from_email = trim( $parts );
                }
                break;
            }
        }

        return [ $from_email, $from_name ];
    }

    /**
     * Determine the message content type.
     */
    protected static function parse_content_type( $headers ) {
        $type  = 'text/html';
        $lines = [];
        if ( is_array( $headers ) ) {
            $lines = $headers;
        } elseif ( is_string( $headers ) ) {
            $lines = explode( "\n", str_replace( "\r", "\n", $headers ) );
        }
        foreach ( $lines as $header ) {
            if ( 0 === stripos( $header, 'Content-Type:' ) && false !== stripos( $header, 'text/plain' ) ) {
                $type = 'text/plain';
                break;
            }
        }
        return $type;
    }
}
