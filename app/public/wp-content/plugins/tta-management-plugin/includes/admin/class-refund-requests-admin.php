<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
class TTA_Refund_Requests_Admin {
    public static function get_instance(){ static $inst; return $inst ?: $inst = new self(); }
    private function __construct(){ add_action('admin_menu',[ $this,'register_menu' ]); }
    public function register_menu(){
        add_menu_page('TTA Refund Requests','TTA Refund Requests','manage_options','tta-refund-requests',[ $this,'render_page' ],'dashicons-money-alt',9);
    }
    public function render_page(){
        echo '<div class="wrap"><h1>' . esc_html__( 'Refund Requests', 'tta' ) . '</h1>';
        $rows = tta_get_refund_requests();
        if ( $rows ) {
            echo '<table class="widefat striped"><thead><tr>';
            echo '<th>' . esc_html__( 'Date', 'tta' ) . '</th><th>' . esc_html__( 'Member', 'tta' ) . '</th><th>' . esc_html__( 'Event', 'tta' ) . '</th><th>' . esc_html__( 'Reason', 'tta' ) . '</th></tr></thead><tbody>';
            foreach ( $rows as $r ) {
                $member = esc_html( $r['member_name'] );
                $event  = esc_html( $r['event_name'] );
                $url    = $r['event_url'] ? '<a href="'.esc_url( $r['event_url'] ).'">'.$event.'</a>' : $event;
                echo '<tr><td>' . esc_html( date_i18n( 'F j, Y g:i a', strtotime( $r['date'] ) ) ) . '</td><td>' . $member . '</td><td>' . $url . '</td><td>' . esc_html( $r['reason'] ) . '</td></tr>';
            }
            echo '</tbody></table>';
        } else {
            echo '<p>' . esc_html__( 'No refund requests found.', 'tta' ) . '</p>';
        }
        echo '</div>';
    }
}
TTA_Refund_Requests_Admin::get_instance();
