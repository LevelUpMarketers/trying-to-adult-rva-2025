<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class TTA_Notice_Bar {
    public static function init() {
        add_action( 'wp_body_open', [ __CLASS__, 'render' ] );
        add_action( 'wp_enqueue_scripts', [ __CLASS__, 'enqueue_assets' ] );
    }

    public static function enqueue_assets() {
        wp_enqueue_style(
            'tta-notice-bar-css',
            TTA_PLUGIN_URL . 'assets/css/frontend/notice-bar.css',
            [ 'tta-frontend-css' ],
            TTA_PLUGIN_VERSION
        );
        wp_enqueue_script(
            'tta-notice-bar-js',
            TTA_PLUGIN_URL . 'assets/js/frontend/notice-bar.js',
            [ 'jquery' ],
            TTA_PLUGIN_VERSION,
            true
        );
        wp_localize_script(
            'tta-notice-bar-js',
            'ttaNoticeBar',
            [
                'cart_url' => home_url( '/cart' ),
            ]
        );
    }

    /**
     * Choose the first valid message from the provided list.
     *
     * @param array $messages
     * @return array { html => string, expires => int }
     */
    public static function determine_message( array $messages ) {
        foreach ( $messages as $msg ) {
            if ( ! empty( $msg['html'] ) ) {
                return [
                    'html'    => $msg['html'],
                    'expires' => isset( $msg['expires'] ) ? intval( $msg['expires'] ) : 0,
                ];
            }
        }
        return [ 'html' => '', 'expires' => 0 ];
    }

    public static function render() {
        $result  = self::determine_message( apply_filters( 'tta_notice_bar_messages', [] ) );
        $message = $result['html'];
        $expires = $result['expires'];
        $data    = $expires ? ' data-expires="' . esc_attr( $expires ) . '"' : '';
        include TTA_PLUGIN_DIR . 'includes/frontend/templates/notice-bar.php';
    }
}

TTA_Notice_Bar::init();
