<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class TTA_Ajax_BI {
    public static function init() {
        add_action( 'wp_ajax_tta_bi_data', [ __CLASS__, 'bi_data' ] );
    }

    public static function bi_data() {
        global $wpdb;
        $members = $wpdb->prefix . 'tta_members';
        $tx      = $wpdb->prefix . 'tta_transactions';
        $att     = $wpdb->prefix . 'tta_attendees';
        $events  = $wpdb->prefix . 'tta_events';

        $active    = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$members} WHERE subscription_status = 'active'" );
        $cancelled = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$members} WHERE subscription_status = 'cancelled'" );
        $problem   = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$members} WHERE subscription_status = 'paymentproblem'" );
        $month_signups = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$members} WHERE joined_at >= %s", gmdate( 'Y-m-01 00:00:00' ) ) );

        $subs = [
            [ 'label' => 'Active', 'count' => $active ],
            [ 'label' => 'Cancelled', 'count' => $cancelled ],
            [ 'label' => 'Payment Issues', 'count' => $problem ],
        ];
        $signups = [ [ 'label' => gmdate( 'M' ), 'count' => $month_signups ] ];

        // Monthly revenue for last 6 months
        $rev_rows = $wpdb->get_results( "SELECT DATE_FORMAT(created_at,'%Y-%m') as m, SUM(amount - refunded) as total FROM {$tx} GROUP BY m ORDER BY m DESC LIMIT 6", ARRAY_A );
        $revenue = array_map( function( $r ) {
            return [ 'label' => $r['m'], 'amount' => (float) $r['total'] ];
        }, array_reverse( $rev_rows ) );

        // Ticket sales per year
        $ticket_rows = $wpdb->get_results( "SELECT YEAR(created_at) as y, SUM(amount-refunded) as total FROM {$tx} GROUP BY y ORDER BY y", ARRAY_A );
        $ticket_sales = array_map( function( $r ){ return [ 'label'=>$r['y'], 'amount'=>(float)$r['total'] ]; }, $ticket_rows );

        // Avg tickets per event this year
        $year = gmdate('Y');
        $avg_rows = $wpdb->get_results( $wpdb->prepare("SELECT MONTH(e.date) m, AVG(a.count) avg_tix FROM {$events} e LEFT JOIN (SELECT ticket_id, COUNT(*) count FROM {$att} GROUP BY ticket_id) a ON a.ticket_id=e.id WHERE YEAR(e.date)=%d GROUP BY MONTH(e.date)", $year), ARRAY_A );
        $avg_tickets = array_map(function($r){ return ['label'=>sprintf('%02d',$r['m']), 'count'=>round($r['avg_tix'],2)]; }, $avg_rows );

        // Subscriptions by level
        $level_rows = $wpdb->get_results( "SELECT membership_level, COUNT(*) c FROM {$members} GROUP BY membership_level", ARRAY_A );
        $by_level = array_map(function($r){ return ['label'=>$r['membership_level'], 'count'=>(int)$r['c']]; }, $level_rows );

        // Predict next month's revenue with simple average of last 3 months
        $rev_vals = wp_list_pluck( $revenue, 'amount' );
        $pred = 0;
        if ( $rev_vals ) {
            $pred = array_sum( array_slice( $rev_vals, -3 ) ) / min( 3, count( $rev_vals ) );
        }
        $prediction = [ 'label' => gmdate('Y-m', strtotime('+1 month')), 'amount' => round( $pred, 2 ) ];

        wp_send_json( [
            'subs'           => $subs,
            'signups'        => $signups,
            'revenue'        => $revenue,
            'ticket_sales'   => $ticket_sales,
            'avg_tickets'    => $avg_tickets,
            'by_level'       => $by_level,
            'prediction'     => $prediction,
        ] );
    }
}
TTA_Ajax_BI::init();
