<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
class TTA_Ajax_BI {
    public static function init() {
        add_action( 'wp_ajax_tta_bi_data', [ __CLASS__, 'bi_data' ] );
    }
    public static function bi_data() {
        global $wpdb;
        $table = $wpdb->prefix . 'tta_members';
        $active = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$table} WHERE subscription_status = 'active'");
        $cancelled = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$table} WHERE subscription_status = 'cancelled'");
        $problem = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$table} WHERE subscription_status = 'paymentproblem'");
        $month_signups = (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$table} WHERE joined_at >= %s", gmdate('Y-m-01 00:00:00')));
        $subs = [
            ['label'=>'Active','count'=>$active],
            ['label'=>'Cancelled','count'=>$cancelled],
            ['label'=>'Payment Issues','count'=>$problem],
        ];
        $signups = [ ['label'=> gmdate('M'), 'count'=> $month_signups ] ];
        wp_send_json([ 'subs'=>$subs, 'signups'=>$signups ]);
    }
}
TTA_Ajax_BI::init();
