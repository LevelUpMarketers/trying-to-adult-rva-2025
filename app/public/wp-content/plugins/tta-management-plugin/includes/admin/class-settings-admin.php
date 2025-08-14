<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class TTA_Settings_Admin {
    public static function get_instance() {
        static $inst;
        return $inst ?: $inst = new self();
    }

    private function __construct() {
        add_action( 'admin_menu', [ $this, 'register_menu' ] );
    }

    public function register_menu() {
        add_menu_page(
            'TTA Settings',
            'TTA Settings',
            'manage_options',
            'tta-settings',
            [ $this, 'render_page' ],
            'dashicons-admin-generic',
            11
        );
    }

    public function render_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $active_tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'general';
        echo '<div class="wrap"><h1>TTA Settings</h1>';
        echo '<h2 class="nav-tab-wrapper">';
        echo '<a href="?page=tta-settings&tab=general" class="nav-tab ' . ( 'general' === $active_tab ? 'nav-tab-active' : '' ) . '">' . esc_html__( 'General Settings', 'tta' ) . '</a>';
        echo '<a href="?page=tta-settings&tab=logging" class="nav-tab ' . ( 'logging' === $active_tab ? 'nav-tab-active' : '' ) . '">' . esc_html__( 'Logging', 'tta' ) . '</a>';
        echo '<a href="?page=tta-settings&tab=api" class="nav-tab ' . ( 'api' === $active_tab ? 'nav-tab-active' : '' ) . '">' . esc_html__( 'API Settings', 'tta' ) . '</a>';
        echo '</h2>';

        if ( 'logging' === $active_tab ) {
            if ( isset( $_POST['tta_clear_log'] ) && check_admin_referer( 'tta_clear_log_action', 'tta_clear_log_nonce' ) ) {
                TTA_Debug_Logger::clear();
                echo '<div class="updated"><p>' . esc_html__( 'Debug log cleared.', 'tta' ) . '</p></div>';
            }

            $log = TTA_Debug_Logger::get_messages();
            if ( $log ) {
                echo '<pre class="tta-debug-log" style="max-height:400px;overflow:auto;background:#fff;border:1px solid #ccc;padding:10px;">' . esc_html( implode( "\n", $log ) ) . '</pre>';
                echo '<form method="post" action="?page=tta-settings&tab=logging">';
                wp_nonce_field( 'tta_clear_log_action', 'tta_clear_log_nonce' );
                echo '<p><input type="submit" name="tta_clear_log" class="button" value="' . esc_attr__( 'Clear Log', 'tta' ) . '"></p>';
                echo '</form>';
            } else {
                echo '<p>' . esc_html__( 'No debug messages logged yet.', 'tta' ) . '</p>';
            }
        } elseif ( 'api' === $active_tab ) {
            $import_results = [];
            if ( isset( $_POST['tta_save_api_settings'] ) && check_admin_referer( 'tta_save_api_settings_action', 'tta_save_api_settings_nonce' ) ) {
                $login = isset( $_POST['tta_authnet_login_id'] ) ? sanitize_text_field( wp_unslash( $_POST['tta_authnet_login_id'] ) ) : '';
                $trans = isset( $_POST['tta_authnet_transaction_key'] ) ? sanitize_text_field( wp_unslash( $_POST['tta_authnet_transaction_key'] ) ) : '';
                $send  = isset( $_POST['tta_sendgrid_api_key'] ) ? sanitize_text_field( wp_unslash( $_POST['tta_sendgrid_api_key'] ) ) : '';
                update_option( 'tta_authnet_login_id', $login, false );
                update_option( 'tta_authnet_transaction_key', $trans, false );
                update_option( 'tta_sendgrid_api_key', $send, false );
                echo '<div class="updated"><p>' . esc_html__( 'API settings saved.', 'tta' ) . '</p></div>';
            }

            if ( isset( $_POST['tta_process_csv'] ) && check_admin_referer( 'tta_process_csv_action', 'tta_process_csv_nonce' ) ) {
                if ( ! empty( $_FILES['tta_arb_csv']['tmp_name'] ) ) {
                    $dry            = ! empty( $_POST['tta_dry_run'] );
                    $api            = new TTA_AuthorizeNet_API();
                    $import_results = TTA_Subscription_Importer::process_csv( $_FILES['tta_arb_csv']['tmp_name'], $dry, $api );
                    echo '<div class="updated"><p>' . esc_html__( 'CSV processed.', 'tta' ) . '</p></div>';
                } else {
                    echo '<div class="error"><p>' . esc_html__( 'No CSV file uploaded.', 'tta' ) . '</p></div>';
                }
            }

            $login = get_option( 'tta_authnet_login_id', '' );
            $trans = get_option( 'tta_authnet_transaction_key', '' );
            $send  = get_option( 'tta_sendgrid_api_key', '' );

            echo '<form method="post" action="?page=tta-settings&tab=api">';
            wp_nonce_field( 'tta_save_api_settings_action', 'tta_save_api_settings_nonce' );
            echo '<table class="form-table"><tbody>';
            echo '<tr><th scope="row"><label for="tta_authnet_login_id">' . esc_html__( 'Authorize.Net Login ID', 'tta' ) . '</label></th><td><input type="password" id="tta_authnet_login_id" name="tta_authnet_login_id" value="' . esc_attr( $login ) . '" /> <button type="button" class="button tta-reveal" data-target="tta_authnet_login_id">' . esc_html__( 'Reveal', 'tta' ) . '</button></td></tr>';
            echo '<tr><th scope="row"><label for="tta_authnet_transaction_key">' . esc_html__( 'Authorize.Net Transaction Key', 'tta' ) . '</label></th><td><input type="password" id="tta_authnet_transaction_key" name="tta_authnet_transaction_key" value="' . esc_attr( $trans ) . '" /> <button type="button" class="button tta-reveal" data-target="tta_authnet_transaction_key">' . esc_html__( 'Reveal', 'tta' ) . '</button></td></tr>';
            echo '<tr><th scope="row"><label for="tta_sendgrid_api_key">' . esc_html__( 'SendGrid API Key', 'tta' ) . '</label></th><td><input type="password" id="tta_sendgrid_api_key" name="tta_sendgrid_api_key" value="' . esc_attr( $send ) . '" /> <button type="button" class="button tta-reveal" data-target="tta_sendgrid_api_key">' . esc_html__( 'Reveal', 'tta' ) . '</button></td></tr>';
            echo '</tbody></table>';
            echo '<p><input type="submit" name="tta_save_api_settings" class="button button-primary" value="' . esc_attr__( 'Save API Settings', 'tta' ) . '"></p>';
            echo '</form>';

            echo '<hr><h2>' . esc_html__( 'Import Subscriptions from CSV', 'tta' ) . '</h2>';
            echo '<form method="post" enctype="multipart/form-data" action="?page=tta-settings&tab=api">';
            wp_nonce_field( 'tta_process_csv_action', 'tta_process_csv_nonce' );
            echo '<p><input type="file" name="tta_arb_csv" accept=".csv" /></p>';
            echo '<p><label><input type="checkbox" name="tta_dry_run" value="1" checked> ' . esc_html__( 'Dry run (no subscriptions created)', 'tta' ) . '</label></p>';
            echo '<p><input type="submit" name="tta_process_csv" class="button button-secondary" value="' . esc_attr__( 'Process CSV', 'tta' ) . '"></p>';
            echo '</form>';

            if ( $import_results ) {
                echo '<h3>' . esc_html__( 'Results', 'tta' ) . '</h3>';
                echo '<table class="widefat"><thead><tr><th>' . esc_html__( 'Email', 'tta' ) . '</th><th>' . esc_html__( 'Level', 'tta' ) . '</th><th>' . esc_html__( 'Status', 'tta' ) . '</th><th>' . esc_html__( 'Transaction', 'tta' ) . '</th><th>' . esc_html__( 'Subscription', 'tta' ) . '</th><th>' . esc_html__( 'Error', 'tta' ) . '</th></tr></thead><tbody>';
                foreach ( $import_results as $row ) {
                    echo '<tr>';
                    echo '<td>' . esc_html( $row['email'] ) . '</td>';
                    echo '<td>' . esc_html( $row['level'] ) . '</td>';
                    echo '<td>' . esc_html( $row['status'] ) . '</td>';
                    echo '<td>' . esc_html( $row['transaction_id'] ?? '' ) . '</td>';
                    echo '<td>' . esc_html( $row['subscription_id'] ?? '' ) . '</td>';
                    echo '<td>' . esc_html( $row['error'] ?? '' ) . '</td>';
                    echo '</tr>';
                }
                echo '</tbody></table>';
            }

            echo '<script>document.querySelectorAll(".tta-reveal").forEach(function(btn){btn.addEventListener("click",function(){var t=document.getElementById(btn.dataset.target);if(t.type==="password"){t.type="text";btn.textContent="' . esc_js( __( 'Hide', 'tta' ) ) . '";}else{t.type="password";btn.textContent="' . esc_js( __( 'Reveal', 'tta' ) ) . '";}});});</script>';
        } else {
            if ( isset( $_POST['tta_flush_cache'] ) && check_admin_referer( 'tta_flush_cache_action', 'tta_flush_cache_nonce' ) ) {
                TTA_Cache::flush();
                echo '<div class="updated"><p>' . esc_html__( 'All caches cleared.', 'tta' ) . '</p></div>';
            }

            if ( isset( $_POST['tta_load_sample_data'] ) && check_admin_referer( 'tta_load_sample_data_action', 'tta_load_sample_data_nonce' ) ) {
                TTA_Sample_Data::load();
                echo '<div class="updated"><p>' . esc_html__( 'Sample data loaded.', 'tta' ) . '</p></div>';
            }

            if ( isset( $_POST['tta_delete_sample_data'] ) && check_admin_referer( 'tta_delete_sample_data_action', 'tta_delete_sample_data_nonce' ) ) {
                TTA_Sample_Data::clear();
                echo '<div class="updated"><p>' . esc_html__( 'Sample data deleted.', 'tta' ) . '</p></div>';
            }

            echo '<form method="post" action="?page=tta-settings&tab=general">';
            wp_nonce_field( 'tta_flush_cache_action', 'tta_flush_cache_nonce' );
            echo '<p><input type="submit" name="tta_flush_cache" class="button button-secondary" value="' . esc_attr__( 'Clear Cache', 'tta' ) . '"></p>';
            echo '</form>';

            echo '<form method="post" action="?page=tta-settings&tab=general">';
            wp_nonce_field( 'tta_load_sample_data_action', 'tta_load_sample_data_nonce' );
            echo '<p><input type="submit" name="tta_load_sample_data" class="button button-secondary" value="' . esc_attr__( 'Load Sample Data', 'tta' ) . '"></p>';
            echo '</form>';

            echo '<form method="post" action="?page=tta-settings&tab=general">';
            wp_nonce_field( 'tta_delete_sample_data_action', 'tta_delete_sample_data_nonce' );
            echo '<p><input type="submit" name="tta_delete_sample_data" class="button button-secondary" value="' . esc_attr__( 'Delete Sample Data', 'tta' ) . '"></p>';
            echo '</form>';

            echo '<div id="tta-authnet-test-wrapper">';
            echo '<p>';
            echo '<button id="tta-authnet-test-button" class="button button-secondary">Authorize.net testing</button>';
            echo '<span class="tta-admin-progress-spinner-div"><img class="tta-admin-progress-spinner-svg" src="' . esc_url( TTA_PLUGIN_URL . 'assets/images/admin/loading.svg' ) . '" alt="" style="display:none;"></span>';
            echo '</p>';
            echo '<p class="tta-admin-progress-response-p"></p>';
            echo '</div>';
        }

        echo '</div>';
    }
}

TTA_Settings_Admin::get_instance();
