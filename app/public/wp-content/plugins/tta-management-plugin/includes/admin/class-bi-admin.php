<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
class TTA_BI_Admin {
    public static function get_instance() {
        static $inst; return $inst ?: $inst = new self();
    }
    private function __construct() {
        add_action( 'admin_menu', [ $this, 'register_menu' ] );
    }
    public function register_menu() {
        add_menu_page(
            'TTA BI Dashboard',
            'TTA BI Dashboard',
            'manage_options',
            'tta-bi-dashboard',
            [ $this, 'render_page' ],
            'dashicons-chart-bar',
            56
        );
    }
    public function render_page() {
        echo '<div class="wrap"><h1>TTA BI Dashboard</h1>';
        include plugin_dir_path( __FILE__ ) . 'views/bi-dashboard.php';
        echo '</div>';
    }
}
TTA_BI_Admin::get_instance();
