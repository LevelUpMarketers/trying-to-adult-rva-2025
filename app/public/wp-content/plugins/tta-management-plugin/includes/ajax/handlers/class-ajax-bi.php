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

        $months = isset( $_GET['months'] ) ? max( 1, min( 24, absint( $_GET['months'] ) ) ) : 6;
        $chart  = isset( $_GET['chart'] ) ? sanitize_key( $_GET['chart'] ) : 'all';
        $start  = gmdate( 'Y-m-01 00:00:00', strtotime( "-$months months" ) );

        $data = [];

        if ( 'all' === $chart || 'subs' === $chart ) {
            $active    = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$members} WHERE subscription_status = 'active'" );
            $cancelled = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$members} WHERE subscription_status = 'cancelled'" );
            $problem   = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$members} WHERE subscription_status = 'paymentproblem'" );
            $data['subs'] = [
                [ 'label' => 'Active', 'count' => $active ],
                [ 'label' => 'Cancelled', 'count' => $cancelled ],
                [ 'label' => 'Payment Issues', 'count' => $problem ],
            ];
        }

        if ( 'all' === $chart || 'signups' === $chart ) {
            $signup_rows = $wpdb->get_results( $wpdb->prepare( "SELECT DATE_FORMAT(joined_at,'%Y-%m') m, COUNT(*) c FROM {$members} WHERE joined_at >= %s GROUP BY m ORDER BY m", $start ), ARRAY_A );
            $data['signups'] = array_map( function( $r ) {
                return [ 'label' => $r['m'], 'count' => (int) $r['c'] ];
            }, $signup_rows );
        }

        if ( 'all' === $chart || 'revenue' === $chart || 'prediction' === $chart ) {
            $rev_rows = $wpdb->get_results( $wpdb->prepare( "SELECT DATE_FORMAT(created_at,'%Y-%m') as m, SUM(amount - refunded) as total FROM {$tx} WHERE created_at >= %s GROUP BY m ORDER BY m", $start ), ARRAY_A );
            $data['revenue'] = array_map( function( $r ) {
                return [ 'label' => $r['m'], 'amount' => (float) $r['total'] ];
            }, $rev_rows );
        }

        if ( 'all' === $chart || 'ticket_sales' === $chart ) {
            $ticket_rows = $wpdb->get_results( "SELECT YEAR(created_at) as y, SUM(amount-refunded) as total FROM {$tx} GROUP BY y ORDER BY y", ARRAY_A );
            $data['ticket_sales'] = array_map( function( $r ){ return [ 'label'=>$r['y'], 'amount'=>(float)$r['total'] ]; }, $ticket_rows );
        }

        if ( 'all' === $chart || 'avg_tickets' === $chart ) {
            $year = gmdate('Y');
            $avg_rows = $wpdb->get_results( $wpdb->prepare("SELECT MONTH(e.date) m, AVG(a.count) avg_tix FROM {$events} e LEFT JOIN (SELECT ticket_id, COUNT(*) count FROM {$att} GROUP BY ticket_id) a ON a.ticket_id=e.id WHERE YEAR(e.date)=%d GROUP BY MONTH(e.date)", $year), ARRAY_A );
            $data['avg_tickets'] = array_map(function($r){ return ['label'=>sprintf('%02d',$r['m']), 'count'=>round($r['avg_tix'],2)]; }, $avg_rows );
        }

        if ( 'all' === $chart || 'by_level' === $chart ) {
            $level_rows = $wpdb->get_results( "SELECT membership_level, COUNT(*) c FROM {$members} GROUP BY membership_level", ARRAY_A );
            $data['by_level'] = array_map(function($r){ return ['label'=>$r['membership_level'], 'count'=>(int)$r['c']]; }, $level_rows );
        }

        if ( 'all' === $chart || 'prediction' === $chart ) {
            $rev_vals = isset( $data['revenue'] ) ? wp_list_pluck( $data['revenue'], 'amount' ) : [];
            $pred = 0;
            if ( $rev_vals ) {
                $pred = array_sum( array_slice( $rev_vals, -3 ) ) / min( 3, count( $rev_vals ) );
            }
            $data['prediction'] = [ 'label' => gmdate('Y-m', strtotime('+1 month')), 'amount' => round( $pred, 2 ) ];
        }

        wp_send_json( $data );
    }
}
TTA_Ajax_BI::init();
