<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class TTA_Notice_Bar {
    public static function init() {
        add_action( 'wp_enqueue_scripts', [ __CLASS__, 'enqueue_assets' ] );
        add_action( 'wp_body_open', [ __CLASS__, 'render' ] );
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
        $msg = self::determine_message( apply_filters( 'tta_notice_bar_messages', [] ) );
        ?>
        <div id="tta-notice-bar" class="tta-notice-bar container wpex-relative wpex-py-15 wpex-md-flex wpex-justify-between wpex-items-center wpex-text-center wpex-md-text-initial wpex-flex-row-reverse">
            <div class="tta-notice-links">
                <a href="/the-tta-partner-program/">Partner With Us</a>
                <span>-</span>
                <a href="/rules-policies">Rules &amp; Policies</a>
                <div id="top-bar-social" class="top-bar-right wpex-mt-10 wpex-md-mt-0 social-style-flat-color">
                    <ul id="top-bar-social-list" class="wpex-inline-block wpex-list-none wpex-align-bottom wpex-m-0 wpex-last-mr-0">
                        <li class="wpex-inline-block wpex-mr-5 jre-top-bar-meetup-icon">
                            <a style="background:none!important" href="/" title="Meetup" class="wpex-meetup wpex-social-btn wpex-social-btn-flat wpex-social-bg" rel="noopener noreferrer">
                                <img src="https://trying-to-adult-rva-2025.local/wp-content/uploads/2022/12/cropped-TTA2_Full-1.png" alt="">
                                <span class="screen-reader-text">Trying to Adult Logo</span>
                            </a>
                        </li>
                        <li class="wpex-inline-block wpex-mr-5 jre-top-bar-meetup-icon">
                            <a href="https://www.meetup.com/trying-to-adult/" title="Meetup" target="_blank" class="wpex-meetup wpex-social-btn wpex-social-btn-flat wpex-social-bg" rel="noopener noreferrer">
                                <img src="https://trying-to-adult-rva-2025.local/wp-content/uploads/2023/01/Background.png" alt="">
                                <span class="screen-reader-text">Meetup</span>
                            </a>
                        </li>
                        <li class="wpex-inline-block wpex-mr-5">
                            <a href="https://www.facebook.com/groups/tryingtoadultrva" title="Facebook" target="_blank" class="wpex-facebook wpex-social-btn wpex-social-btn-flat wpex-social-bg" rel="noopener noreferrer">
                                <span class="ticon ticon-facebook" aria-hidden="true"></span>
                                <span class="screen-reader-text">Facebook</span>
                            </a>
                        </li>
                        <li class="wpex-inline-block wpex-mr-5">
                            <a href="https://www.instagram.com/tryingtoadultrva/" title="Instagram" target="_blank" class="wpex-instagram wpex-social-btn wpex-social-btn-flat wpex-social-bg customize-unpreviewable" rel="noopener noreferrer">
                                <span class="ticon ticon-instagram" aria-hidden="true"></span>
                                <span class="screen-reader-text">Instagram</span>
                            </a>
                        </li>
                        <li class="wpex-inline-block wpex-mr-5">
                            <a href="mailto:contact@tryingtoadultrva.com" title="Email" class="wpex-email wpex-social-btn wpex-social-btn-flat wpex-social-bg">
                                <span class="ticon ticon-envelope" aria-hidden="true"></span>
                                <span class="screen-reader-text">Email</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <a href="<?php echo esc_url( home_url( '/cart' ) ); ?>" class="tta-cart-link"><img src="/wp-content/uploads/2025/07/cart-1.png" alt=""></a>
            </div>
            <div id="tta-notice-message" class="tta-notice-message" data-expires="<?php echo intval( $msg['expires'] ); ?>">
                <?php echo wp_kses_post( $msg['html'] ); ?>
            </div>
        </div>
        <?php
    }
}
