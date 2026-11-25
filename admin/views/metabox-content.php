<?php
/**
 * Métabox « Contenu ».
 *
 * @var array $values Réglages de la pop-up.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<p>
	<label for="mrzpop_html"><strong><?php esc_html_e( 'Text (HTML)', 'mrz-pop-up-exp' ); ?></strong></label>
</p>
<textarea id="mrzpop_html" name="mrzpop_html" class="large-text code" rows="14"><?php echo esc_textarea( $values['html'] ); ?></textarea>
<p class="description"><?php esc_html_e( 'Shortcodes are allowed. Scripts, styles, iframes and raw forms are removed.', 'mrz-pop-up-exp' ); ?></p>

<div class="mrzpop-image-field">
	<p><strong><?php esc_html_e( 'Left image (optional)', 'mrz-pop-up-exp' ); ?></strong></p>
	<input type="hidden" id="mrzpop_image_id" name="mrzpop_image_id" value="<?php echo esc_attr( $values['image_id'] ); ?>">
	<div class="mrzpop-image-preview"><?php echo $values['image_id'] ? wp_get_attachment_image( $values['image_id'], 'medium' ) : ''; ?></div>
	<button type="button" class="button mrzpop-image-choose"><?php esc_html_e( 'Choose an image', 'mrz-pop-up-exp' ); ?></button>
	<button type="button" class="button-link button-link-delete mrzpop-image-remove"<?php echo $values['image_id'] ? '' : ' hidden'; ?>><?php esc_html_e( 'Remove image', 'mrz-pop-up-exp' ); ?></button>
</div>
