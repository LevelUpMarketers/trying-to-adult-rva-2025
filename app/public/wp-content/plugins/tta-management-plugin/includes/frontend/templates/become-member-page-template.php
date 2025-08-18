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

<?php
  $tiers = array(
    'non_member' => __( 'Non-member', 'tta' ),
    'basic'      => __( 'Standard Member', 'tta' ),
    'premium'    => __( 'Premium Member', 'tta' ),
  );

  $features = array(
    'monthly_cost'            => array(
      'label'      => __( 'Monthly Cost', 'tta' ),
      'non_member' => '$0',
      'basic'      => '$10',
      'premium'    => '$17',
    ),
    'monthly_new_friend_social' => array(
      'label'      => __( 'Monthly New Friend Social', 'tta' ),
      'non_member' => __( 'N/A', 'tta' ),
      'basic'      => __( 'N/A', 'tta' ),
      'premium'    => __( 'N/A', 'tta' ),
    ),
    'classic_events' => array(
      'label'      => __( '3+ Classic Events Monthly', 'tta' ),
      'non_member' => '$5 ' . __( 'access pass', 'tta' ),
      'basic'      => __( 'Free access pass', 'tta' ),
      'premium'    => __( 'Free access pass', 'tta' ),
    ),
    'special_events' => array(
      'label'      => __( '3+ Special Events Monthly', 'tta' ),
      'non_member' => '$7 ' . __( 'access pass', 'tta' ),
      'basic'      => '$5 ' . __( 'access pass', 'tta' ),
      'premium'    => __( 'Free access pass', 'tta' ),
    ),
    'guess_pass' => array(
      'label'      => __( 'Guest Pass', 'tta' ),
      'non_member' => __( 'N/A', 'tta' ),
      'basic'      => __( 'N/A', 'tta' ),
      'premium'    => __( 'N/A', 'tta' ),
    ),
    'waitlist_notice' => array(
      'label'      => __( 'Advanced Notice on Waitlist Openings', 'tta' ),
      'non_member' => __( 'N/A', 'tta' ),
      'basic'      => __( 'N/A', 'tta' ),
      'premium'    => __( 'N/A', 'tta' ),
    ),
    'special_rates' => array(
      'label'      => __( 'Special Rates for Select Events', 'tta' ),
      'non_member' => __( 'N/A', 'tta' ),
      'basic'      => __( 'N/A', 'tta' ),
      'premium'    => __( 'N/A', 'tta' ),
    ),
  );
?>

  <table class="tta-membership-table">
    <thead>
      <tr>
        <th><?php esc_html_e( 'Benefits', 'tta' ); ?></th>
        <?php foreach ( $tiers as $tier_label ) : ?>
          <th><?php echo esc_html( $tier_label ); ?></th>
        <?php endforeach; ?>
      </tr>
    </thead>
    <tbody>
      <?php foreach ( $features as $feature ) : ?>
        <tr>
          <td><?php echo esc_html( $feature['label'] ); ?></td>
          <?php foreach ( $tiers as $tier_key => $tier_label ) : ?>
            <td><?php echo esc_html( $feature[ $tier_key ] ); ?></td>
          <?php endforeach; ?>
        </tr>
      <?php endforeach; ?>
      <tr class="tta-membership-actions">
        <td></td>
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

  <div class="tta-membership-mobile">
    <?php foreach ( $tiers as $tier_key => $tier_label ) : ?>
      <div class="tta-tier-card">
        <h2><?php echo esc_html( $tier_label ); ?></h2>
        <ul>
          <?php foreach ( $features as $feature ) : ?>
            <li>
              <span class="tta-feature-label"><?php echo esc_html( $feature['label'] ); ?></span>
              <span class="tta-feature-value"><?php echo esc_html( $feature[ $tier_key ] ); ?></span>
            </li>
          <?php endforeach; ?>
        </ul>
        <?php if ( 'basic' === $tier_key ) : ?>
          <button type="button" id="tta-basic-signup" class="tta-button tta-button-primary">
            <?php esc_html_e( 'Sign Up', 'tta' ); ?>
          </button>
        <?php elseif ( 'premium' === $tier_key ) : ?>
          <button type="button" id="tta-premium-signup" class="tta-button tta-button-primary">
            <?php esc_html_e( 'Sign Up', 'tta' ); ?>
          </button>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
  </div>
</div>
<?php
get_footer();
