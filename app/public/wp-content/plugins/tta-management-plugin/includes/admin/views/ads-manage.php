<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$ads = get_option( 'tta_ads', [] );

if ( isset( $_GET['action'], $_GET['ad_id'] ) && 'delete' === $_GET['action'] && check_admin_referer( 'tta_ads_delete' ) ) {
    $idx = intval( $_GET['ad_id'] );
    if ( isset( $ads[ $idx ] ) ) {
        unset( $ads[ $idx ] );
        $ads = array_values( $ads );
        update_option( 'tta_ads', $ads, false );
        TTA_Cache::delete( 'tta_ads_all' );
        echo '<div class="updated"><p>' . esc_html__( 'Ad deleted.', 'tta' ) . '</p></div>';
    }
}
?>
<div class="wrap">
<table class="widefat striped">
    <thead>
        <tr>
            <th><?php esc_html_e( 'Image', 'tta' ); ?></th>
            <th><?php esc_html_e( 'Business Name', 'tta' ); ?></th>
            <th><?php esc_html_e( 'URL', 'tta' ); ?></th>
            <th><?php esc_html_e( 'Actions', 'tta' ); ?></th>
        </tr>
    </thead>
    <tbody>
    <?php if ( ! empty( $ads ) ) : ?>
        <?php foreach ( $ads as $i => $ad ) : ?>
            <tr>
                <td><?php echo ! empty( $ad['image_id'] ) ? wp_get_attachment_image( intval( $ad['image_id'] ), 'thumbnail' ) : ''; ?></td>
                <td><?php echo esc_html( $ad['business_name'] ?? '' ); ?></td>
                <td><?php echo esc_html( $ad['url'] ?? '' ); ?></td>
                <td>
                    <a href="<?php echo esc_url( add_query_arg( [ 'page' => 'tta-ads', 'tab' => 'create', 'ad_id' => $i ], admin_url( 'admin.php' ) ) ); ?>"><?php esc_html_e( 'Edit', 'tta' ); ?></a> |
                    <a href="<?php echo esc_url( wp_nonce_url( add_query_arg( [ 'page' => 'tta-ads', 'tab' => 'manage', 'action' => 'delete', 'ad_id' => $i ], admin_url( 'admin.php' ) ), 'tta_ads_delete' ) ); ?>" onclick="return confirm('<?php esc_attr_e( 'Delete this ad?', 'tta' ); ?>');"><?php esc_html_e( 'Delete', 'tta' ); ?></a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else : ?>
        <tr><td colspan="4"><?php esc_html_e( 'No ads found.', 'tta' ); ?></td></tr>
    <?php endif; ?>
    </tbody>
</table>
</div>
