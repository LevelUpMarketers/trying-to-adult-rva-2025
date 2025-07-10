<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
class TTA_Ajax_Refund {
    public static function init() {
        add_action( 'wp_ajax_tta_request_refund', [ __CLASS__, 'request_refund' ] );
        add_action( 'wp_ajax_tta_process_refund_request', [ __CLASS__, 'process_request' ] );
        add_action( 'wp_ajax_tta_delete_refund_request', [ __CLASS__, 'delete_request' ] );
    }

    public static function request_refund() {
        check_ajax_referer( 'tta_frontend_nonce', 'nonce' );
        if ( ! is_user_logged_in() ) {
            wp_send_json_error( [ 'message' => __( 'You must be logged in.', 'tta' ) ] );
        }
        $tx_id     = tta_sanitize_text_field( $_POST['transaction_id'] ?? '' );
        $event_id  = intval( $_POST['event_id'] ?? 0 );
        $ticket_id = intval( $_POST['ticket_id'] ?? 0 );
        $att_id    = intval( $_POST['attendee_id'] ?? 0 );
        $reason  = tta_sanitize_textarea_field( $_POST['reason'] ?? '' );
        if ( ! $tx_id || ! $event_id || ! $att_id ) {
            wp_send_json_error( [ 'message' => 'missing_data' ] );
        }
        global $wpdb;
        $tx_table   = $wpdb->prefix . 'tta_transactions';
        $hist_table = $wpdb->prefix . 'tta_memberhistory';
        $member_id  = (int) $wpdb->get_var( $wpdb->prepare( "SELECT member_id FROM {$tx_table} WHERE transaction_id = %s", $tx_id ) );
        if ( ! $member_id ) {
            wp_send_json_error( [ 'message' => 'not_found' ] );
        }

        // Gather attendee details before cancelling.
        $att         = tta_get_attendee_by_tx_ticket( $tx_id, $ticket_id, $att_id );
        $att_details = [];
        $amount      = 0;
        if ( $att ) {
            $att_details = [
                'first_name'  => $att['first_name'],
                'last_name'   => $att['last_name'],
                'email'       => $att['email'],
                'phone'       => $att['phone'],
            ];
            $tx_row  = tta_get_transaction_by_gateway_id( $tx_id );
            if ( $tx_row ) {
                $amount = tta_get_ticket_price_from_transaction( $tx_row, $ticket_id );
            }
            tta_cancel_attendance_internal( intval( $att['id'] ) );
        }

        $action_data = [
            'transaction_id' => $tx_id,
            'ticket_id'     => $ticket_id,
            'reason'        => $reason,
            'attendee'      => array_merge( $att_details, [ 'amount_paid' => $amount ] ),
        ];

        $wpdb->insert( $hist_table, [
            'member_id'   => $member_id,
            'wpuserid'    => get_current_user_id(),
            'event_id'    => $event_id,
            'action_type' => 'refund_request',
            'action_data' => wp_json_encode( $action_data ),
        ], [ '%d','%d','%d','%s','%s' ] );
        TTA_Cache::delete( 'tta_refund_requests' );
        wp_send_json_success( [ 'message' => __( 'Refund request submitted.', 'tta' ) ] );
    }

    /**
     * Process a pending refund request via AJAX.
     */
    public static function process_request() {
        check_ajax_referer( 'tta_attendee_admin_action', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( [ 'message' => 'forbidden' ] );
        }

        $tx_id     = tta_sanitize_text_field( $_POST['tx'] ?? '' );
        $ticket_id = intval( $_POST['ticket'] ?? 0 );
        if ( ! $tx_id || ! $ticket_id ) {
            wp_send_json_error( [ 'message' => 'missing_data' ] );
        }

        $req = tta_get_refund_request( $tx_id, $ticket_id );
        if ( ! $req ) {
            wp_send_json_error( [ 'message' => 'not_found' ] );
        }

        TTA_Refund_Processor::process_refund_request( $req );
        wp_send_json_success( [ 'message' => __( 'Refund processed.', 'tta' ) ] );
    }

    /**
     * Delete a pending refund request without issuing a refund.
     */
    public static function delete_request() {
        check_ajax_referer( 'tta_attendee_admin_action', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( [ 'message' => 'forbidden' ] );
        }

        $tx_id     = tta_sanitize_text_field( $_POST['tx'] ?? '' );
        $ticket_id = intval( $_POST['ticket'] ?? 0 );
        if ( ! $tx_id || ! $ticket_id ) {
            wp_send_json_error( [ 'message' => 'missing_data' ] );
        }

        tta_delete_refund_request( $tx_id, $ticket_id );
        wp_send_json_success( [ 'message' => __( 'Request cancelled.', 'tta' ) ] );
    }
}
TTA_Ajax_Refund::init();
