<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class TTA_Refund_Processor {
    /** Schedule cron event. */
    public static function schedule_event() {
        if ( ! wp_next_scheduled( 'tta_refund_request_cron' ) ) {
            wp_schedule_event( time(), 'tta_ten_minutes', 'tta_refund_request_cron' );
        }
    }

    /** Clear cron event. */
    public static function clear_event() {
        wp_clear_scheduled_hook( 'tta_refund_request_cron' );
    }

    /** Initialize hooks. */
    public static function init() {
        add_action( 'tta_after_purchase_logged', [ __CLASS__, 'handle_purchase' ], 10, 2 );
        add_action( 'tta_refund_request_cron', [ __CLASS__, 'expire_requests' ] );
        self::schedule_event();
    }

    /**
     * Process pending refund requests when tickets are purchased.
     *
     * @param array $items   Items purchased.
     * @param int   $user_id Buyer user ID.
     */
    public static function handle_purchase( array $items, $user_id ) {
        foreach ( $items as $it ) {
            $tid = intval( $it['ticket_id'] );
            $qty = intval( $it['quantity'] ?? 1 );
            for ( $i = 0; $i < $qty; $i++ ) {
                $req = tta_get_next_refund_request_for_ticket( $tid );
                if ( ! $req ) {
                    break;
                }
                self::process_refund_request( $req );
            }
        }
    }

    /**
     * Process a single refund request.
     *
     * @param array $req Refund request data.
     */
    public static function process_refund_request( array $req, $amount_override = null ) {
        $tx = tta_get_transaction_by_gateway_id( $req['transaction_id'] );
        if ( ! $tx ) {
            tta_delete_refund_request( $req['transaction_id'], $req['ticket_id'] );
            return;
        }

        $amount = null === $amount_override ? tta_get_ticket_price_from_transaction( $tx, $req['ticket_id'] ) : floatval( $amount_override );
        if ( $amount <= 0 ) {
            $amount = floatval( $tx['amount'] );
        }

        $api         = new TTA_AuthorizeNet_API();
        $status_res  = $api->get_transaction_status( $tx['transaction_id'] );
        $status_str  = strtolower( $status_res['status'] ?? '' );
        $should_void = ( false !== strpos( $status_str, 'pending' ) );

        if ( $should_void ) {
            $res = $api->void( $tx['transaction_id'] );
        } else {
            $res = $api->refund( $amount, $tx['transaction_id'], $tx['card_last4'] );
            if ( ! $res['success'] ) {
                $msg = strtolower( $res['error'] );
                if ( false !== strpos( $msg, 'not meet the criteria' ) || false !== strpos( $msg, 'not settled' ) || false !== strpos( $msg, 'unsuccessful' ) ) {
                    $res = $api->void( $tx['transaction_id'] );
                }
            }
        }

        if ( ! $res['success'] ) {
            return;
        }

        global $wpdb;
        $txn_table  = $wpdb->prefix . 'tta_transactions';
        $hist_table = $wpdb->prefix . 'tta_memberhistory';

        $wpdb->update(
            $txn_table,
            [ 'refunded' => floatval( $tx['refunded'] ) + $amount ],
            [ 'id' => intval( $tx['id'] ) ],
            [ '%f' ],
            [ '%d' ]
        );

        $attendee = $req['attendee'] ?? [];
        $wpdb->insert(
            $hist_table,
            [
                'member_id'   => intval( $tx['member_id'] ),
                'wpuserid'    => intval( $tx['wpuserid'] ),
                'event_id'    => intval( $req['event_id'] ),
                'action_type' => 'refund',
                'action_data' => wp_json_encode([
                    'amount'         => $amount,
                    'transaction_id' => $tx['transaction_id'],
                    'ticket_id'      => intval( $req['ticket_id'] ),
                    'attendee_id'    => 0,
                    'cancel'         => 1,
                    'attendee'       => $attendee,
                ]),
            ],
            [ '%d','%d','%d','%s','%s' ]
        );

        $ticket_info = tta_get_ticket_basic_info( $req['ticket_id'] );
        $refund_data = [
            'event_id'    => intval( $req['event_id'] ),
            'ticket_id'   => intval( $req['ticket_id'] ),
            'ticket_name' => $ticket_info['ticket_name'] ?? '',
            'attendee'    => $attendee,
            'amount'      => $amount,
            'first_name'  => $attendee['first_name'] ?? '',
            'last_name'   => $attendee['last_name'] ?? '',
            'email'       => $attendee['email'] ?? '',
        ];
        TTA_Email_Handler::get_instance()->send_refund_emails( $tx, $refund_data );

        tta_delete_refund_request( $req['transaction_id'], $req['ticket_id'] );
        TTA_Cache::flush();
    }

    /** Expire refund requests less than two hours before the event. */
    public static function expire_requests() {
        $requests = tta_get_refund_requests();
        $now = current_time( 'timestamp' );
        foreach ( $requests as $req ) {
            $ts = tta_get_event_start_timestamp( $req['event_id'] );
            if ( $ts && ( $ts - 7200 ) <= $now ) {
                tta_delete_refund_request( $req['transaction_id'], $req['ticket_id'] );
            }
        }
    }
}
