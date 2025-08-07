<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="wrap">
<h1><?php esc_html_e( 'Manage Ads', 'tta' ); ?></h1>
<form method="post">
<table class="form-table" id="tta-ads-table">
<tbody>
<?php
if ( ! empty( $ads ) ) {
    foreach ( $ads as $index => $ad ) {
        $img_id = intval( $ad['image_id'] );
        $url    = esc_url( $ad['url'] );
        $name   = sanitize_text_field( $ad['business_name'] ?? '' );
        $phone  = sanitize_text_field( $ad['business_phone'] ?? '' );
        $address= sanitize_text_field( $ad['business_address'] ?? '' );
        $preview = $img_id ? wp_get_attachment_image( $img_id, 'thumbnail' ) : '';
        ?>
        <tr class="tta-ad-row" data-index="<?php echo esc_attr( $index ); ?>">
            <th scope="row"><?php esc_html_e( 'Ad Image', 'tta' ); ?></th>
            <td>
                <button class="button tta-upload-single" data-target="#ad_image_<?php echo esc_attr( $index ); ?>"><?php esc_html_e( 'Select Image', 'tta' ); ?></button>
                <input type="hidden" id="ad_image_<?php echo esc_attr( $index ); ?>" name="ads[<?php echo esc_attr( $index ); ?>][image_id]" value="<?php echo esc_attr( $img_id ); ?>">
                <div id="ad_image_preview_<?php echo esc_attr( $index ); ?>"><?php echo $preview; ?></div>
            </td>
        </tr>
        <tr class="tta-ad-row" data-index="<?php echo esc_attr( $index ); ?>">
            <th scope="row"><?php esc_html_e( 'Link URL', 'tta' ); ?></th>
            <td>
                <input type="text" name="ads[<?php echo esc_attr( $index ); ?>][url]" value="<?php echo esc_attr( $url ); ?>" class="regular-text" />
                <button class="button tta-remove-ad">&times;</button>
            </td>
        </tr>
        <tr class="tta-ad-row" data-index="<?php echo esc_attr( $index ); ?>">
            <th scope="row"><?php esc_html_e( 'Business Name', 'tta' ); ?></th>
            <td><input type="text" name="ads[<?php echo esc_attr( $index ); ?>][business_name]" value="<?php echo esc_attr( $name ); ?>" class="regular-text" /></td>
        </tr>
        <tr class="tta-ad-row" data-index="<?php echo esc_attr( $index ); ?>">
            <th scope="row"><?php esc_html_e( 'Business Telephone', 'tta' ); ?></th>
            <td><input type="text" name="ads[<?php echo esc_attr( $index ); ?>][business_phone]" value="<?php echo esc_attr( $phone ); ?>" class="regular-text" /></td>
        </tr>
        <tr class="tta-ad-row" data-index="<?php echo esc_attr( $index ); ?>">
            <th scope="row"><?php esc_html_e( 'Business Address', 'tta' ); ?></th>
            <td><input type="text" name="ads[<?php echo esc_attr( $index ); ?>][business_address]" value="<?php echo esc_attr( $address ); ?>" class="regular-text" /></td>
        </tr>
        <?php
    }
}
?>
</tbody>
</table>
<p>
    <button type="button" class="button" id="tta-add-ad"><?php esc_html_e( 'Add Ad', 'tta' ); ?></button>
</p>
<?php wp_nonce_field( 'tta_ads_save', 'tta_ads_nonce' ); ?>
<p class="submit"><input type="submit" class="button-primary" value="<?php esc_attr_e( 'Save Ads', 'tta' ); ?>"></p>
</form>
</div>
<script>
jQuery(function($){
    var index = <?php echo isset( $index ) ? intval( $index + 1 ) : 0; ?>;
    $('#tta-add-ad').on('click', function(e){
        e.preventDefault();
        var rowImg = '<tr class="tta-ad-row" data-index="'+index+'"><th scope="row"><?php esc_html_e( 'Ad Image', 'tta' ); ?></th><td><button class="button tta-upload-single" data-target="#ad_image_'+index+'">Select Image</button><input type="hidden" id="ad_image_'+index+'" name="ads['+index+'][image_id]" value=""><div id="ad_image_preview_'+index+'"></div></td></tr>';
        var rowUrl = '<tr class="tta-ad-row" data-index="'+index+'"><th scope="row"><?php esc_html_e( 'Link URL', 'tta' ); ?></th><td><input type="text" name="ads['+index+'][url]" value="" class="regular-text" /> <button class="button tta-remove-ad">&times;</button></td></tr>';
        var rowName = '<tr class="tta-ad-row" data-index="'+index+'"><th scope="row"><?php esc_html_e( 'Business Name', 'tta' ); ?></th><td><input type="text" name="ads['+index+'][business_name]" value="" class="regular-text" /></td></tr>';
        var rowPhone = '<tr class="tta-ad-row" data-index="'+index+'"><th scope="row"><?php esc_html_e( 'Business Telephone', 'tta' ); ?></th><td><input type="text" name="ads['+index+'][business_phone]" value="" class="regular-text" /></td></tr>';
        var rowAddress = '<tr class="tta-ad-row" data-index="'+index+'"><th scope="row"><?php esc_html_e( 'Business Address', 'tta' ); ?></th><td><input type="text" name="ads['+index+'][business_address]" value="" class="regular-text" /></td></tr>';
        $('#tta-ads-table tbody').append(rowImg + rowUrl + rowName + rowPhone + rowAddress);
        index++;
    });
    $('#tta-ads-table').on('click','.tta-remove-ad',function(e){
        e.preventDefault();
        var idx = $(this).closest('tr').data('index');
        $('#tta-ads-table').find('tr[data-index="'+idx+'"]').remove();
    });
});
</script>
