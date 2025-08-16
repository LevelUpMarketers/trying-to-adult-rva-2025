<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class TTA_Alert_Bar {
    public static function init() {
        add_action( 'wp_enqueue_scripts', [ __CLASS__, 'enqueue_assets' ] );
        add_action( 'wp_footer', [ __CLASS__, 'render' ] );
    }

    public static function enqueue_assets() {
        wp_enqueue_style(
            'tta-alert-bar-css',
            TTA_PLUGIN_URL . 'assets/css/frontend/alert-bar.css',
            [ 'tta-frontend-css' ],
            TTA_PLUGIN_VERSION
        );
        wp_enqueue_script(
            'tta-alert-bar-js',
            TTA_PLUGIN_URL . 'assets/js/frontend/alert-bar.js',
            [ 'jquery' ],
            TTA_PLUGIN_VERSION,
            true
        );
    }

    public static function render() {
        if ( tta_user_is_banned( get_current_user_id() ) ) {
            $url = add_query_arg( 'reentry', '1', home_url( '/checkout' ) );
            ?>
            <div id="tta-alert-bar" class="tta-alert-bar tta-alert-banned">
                <span class="tta-alert-message"><?php echo esc_html__( "You are banned due to excessive event no-shows or other reasons and must purchase a re-entry ticket to attend events again.", 'tta' ); ?></span>
                <a class="tta-alert-button" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html__( 'Purchase Re-entry Ticket', 'tta' ); ?></a>
            </div>
            <?php
            return;
        }
        $cart = new TTA_Cart();
        $items = $cart->get_items();
        if ( empty( $items ) ) {
            return;
        }
        $expires = 0;
        foreach ( $items as $it ) {
            $ts = strtotime( $it['expires_at'] );
            if ( ! $expires || $ts < $expires ) {
                $expires = $ts;
            }
        }
        ?>
        <div id="tta-alert-bar" class="tta-alert-bar tta-alert-cart" data-expires="<?php echo esc_attr( $expires ); ?>">
            <span class="tta-alert-message"><?php echo esc_html__( 'Tickets reserved for', 'tta' ); ?> <span class="tta-countdown"></span></span>
            <a class="tta-alert-button" href="<?php echo esc_url( home_url( '/checkout' ) ); ?>"><?php echo esc_html__( 'Go to Checkout', 'tta' ); ?></a>
        </div>
        <?php
    }
}
