<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
class TTA_Ajax_Refund {
    public static function init() {
        add_action( 'wp_ajax_tta_request_refund', [ __CLASS__, 'request_refund' ] );
    }

    public static function request_refund() {
        check_ajax_referer( 'tta_frontend_nonce', 'nonce' );
        if ( ! is_user_logged_in() ) {
            wp_send_json_error( [ 'message' => __( 'You must be logged in.', 'tta' ) ] );
        }
        $tx_id   = tta_sanitize_text_field( $_POST['transaction_id'] ?? '' );
        $event_id= intval( $_POST['event_id'] ?? 0 );
        $reason  = tta_sanitize_textarea_field( $_POST['reason'] ?? '' );
        if ( ! $tx_id || ! $event_id ) {
            wp_send_json_error( [ 'message' => 'missing_data' ] );
        }
        global $wpdb;
        $tx_table   = $wpdb->prefix . 'tta_transactions';
        $hist_table = $wpdb->prefix . 'tta_memberhistory';
        $member_id  = (int) $wpdb->get_var( $wpdb->prepare( "SELECT member_id FROM {$tx_table} WHERE transaction_id = %s", $tx_id ) );
        if ( ! $member_id ) {
            wp_send_json_error( [ 'message' => 'not_found' ] );
        }
        $wpdb->insert( $hist_table, [
            'member_id'   => $member_id,
            'wpuserid'    => get_current_user_id(),
            'event_id'    => $event_id,
            'action_type' => 'refund_request',
            'action_data' => wp_json_encode( [ 'transaction_id' => $tx_id, 'reason' => $reason ] ),
        ], [ '%d','%d','%d','%s','%s' ] );
        TTA_Cache::delete( 'tta_refund_requests' );
        wp_send_json_success( [ 'message' => __( 'Refund request submitted.', 'tta' ) ] );
    }
}
TTA_Ajax_Refund::init();
