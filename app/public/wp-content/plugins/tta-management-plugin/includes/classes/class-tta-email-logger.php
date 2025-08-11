<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Log email send outcomes for review on the settings page.
 */
class TTA_Email_Logger {
    /**
     * Hook into wp_mail actions.
     */
    public static function init() {
        add_action( 'wp_mail_succeeded', [ __CLASS__, 'log_success' ] );
        add_action( 'wp_mail_failed', [ __CLASS__, 'log_failure' ] );
    }

    /**
     * Record a log entry.
     *
     * @param string $status Status label.
     * @param array  $data   Mail data array.
     */
    protected static function log( $status, $data ) {
        $msgs    = get_option( 'tta_email_log', [] );
        $to      = isset( $data['to'] ) ? ( is_array( $data['to'] ) ? implode( ',', $data['to'] ) : $data['to'] ) : '';
        $subject = $data['subject'] ?? '';
        $msgs[]  = '[' . gmdate( 'Y-m-d H:i:s' ) . '] ' . $status . ' | ' . $to . ' | ' . $subject;
        if ( count( $msgs ) > 200 ) {
            $msgs = array_slice( $msgs, -200 );
        }
        update_option( 'tta_email_log', $msgs, false );
    }

    /**
     * Log successful send.
     */
    public static function log_success( $mail_data ) {
        self::log( 'Sent', $mail_data );
    }

    /**
     * Log failed send.
     */
    public static function log_failure( $wp_error ) {
        $data = is_object( $wp_error ) ? $wp_error->get_error_data() : [];
        $msg  = is_object( $wp_error ) ? $wp_error->get_error_message() : 'Unknown error';
        if ( ! is_array( $data ) ) {
            $data = [];
        }
        $data['subject'] = $data['subject'] ?? '';
        $data['to']      = $data['to'] ?? '';
        self::log( 'Failed: ' . $msg, $data );
    }

    /**
     * Retrieve log messages.
     *
     * @return array
     */
    public static function get_messages() {
        return get_option( 'tta_email_log', [] );
    }

    /**
     * Clear stored messages.
     */
    public static function clear() {
        delete_option( 'tta_email_log' );
    }
}
