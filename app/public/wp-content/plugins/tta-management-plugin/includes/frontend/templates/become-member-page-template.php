<?php
/**
 * Template Name: Become a Member
 *
 * Displays membership options and benefits.
 *
 * @package TTA
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

get_header();

$header_shortcode = '[vc_row full_width="stretch_row_content_no_spaces" css=".vc_custom_1670382516702{background-image: url(https://trying-to-adult-rva-2025.local/wp-content/uploads/2022/12/IMG-4418.png?id=70) !important;background-position: center !important;background-repeat: no-repeat !important;background-size: cover !important;}"][vc_column][vc_empty_space height="300px" el_id="jre-header-title-empty"][vc_column_text css_animation="slideInLeft" el_id="jre-homepage-id-1" css=".vc_custom_1671885403487{margin-left: 50px !important;padding-left: 50px !important;}"]<p id="jre-homepage-id-3">BECOME A MEMBER</p>[/vc_column_text][/vc_column][/vc_row]';
echo do_shortcode( $header_shortcode );
?>
<div class="tta-become-member-wrap">
  <h1><?php esc_html_e( 'Become a Trying to Adult Member', 'tta' ); ?></h1>
  <p><?php esc_html_e( 'Join our community and unlock special perks at local events.', 'tta' ); ?></p>

  <table class="tta-membership-table">
    <thead>
      <tr>
        <th><?php esc_html_e( 'Benefits', 'tta' ); ?></th>
        <th><?php esc_html_e( 'Basic', 'tta' ); ?></th>
        <th><?php esc_html_e( 'Premium', 'tta' ); ?></th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><?php esc_html_e( 'Access to free events every month', 'tta' ); ?></td>
        <td class="check">&#10003;</td>
        <td class="check">&#10003;</td>
      </tr>
      <tr>
        <td><?php esc_html_e( 'Early access to new events', 'tta' ); ?></td>
        <td></td>
        <td class="check">&#10003;</td>
      </tr>
      <tr>
        <td><?php esc_html_e( 'Discounts on paid events', 'tta' ); ?></td>
        <td></td>
        <td class="check">&#10003;</td>
      </tr>
      <tr>
        <td><?php esc_html_e( 'Local discounts (coming soon)', 'tta' ); ?></td>
        <td></td>
        <td class="check">&#10003;</td>
      </tr>
      <tr class="tta-pricing-row">
        <td><strong><?php esc_html_e( 'Price per month', 'tta' ); ?></strong></td>
        <td>$5</td>
        <td>$10</td>
      </tr>
      <tr class="tta-membership-actions">
        <td></td>
        <td>
          <button type="button" id="tta-basic-signup" class="tta-button tta-button-primary">
            <?php esc_html_e( 'Sign Up', 'tta' ); ?>
          </button>
        </td>
        <td>
          <button type="button" id="tta-premium-signup" class="tta-button tta-button-primary">
            <?php esc_html_e( 'Sign Up', 'tta' ); ?>
          </button>
        </td>
      </tr>
    </tbody>
  </table>
</div>
<?php
get_footer();
