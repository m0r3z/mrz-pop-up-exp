<?php
/**
 * Métabox « Apparence ».
 *
 * @var array $values Réglages de la pop-up.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<p>
	<label for="mrzpop_width"><strong><?php esc_html_e( 'Width (px)', 'mrz-pop-up-exp' ); ?></strong></label><br>
	<input type="number" id="mrzpop_width" name="mrzpop_width" min="200" max="2000" step="1" value="<?php echo esc_attr( $values['width'] ); ?>">
</p>
<p>
	<label for="mrzpop_overlay_color"><strong><?php esc_html_e( 'Overlay color', 'mrz-pop-up-exp' ); ?></strong></label><br>
	<input type="text" id="mrzpop_overlay_color" name="mrzpop_overlay_color" data-default-color="#000000" value="<?php echo esc_attr( $values['overlay_color'] ); ?>">
</p>
<p>
	<label for="mrzpop_overlay_opacity"><strong><?php esc_html_e( 'Overlay opacity (%)', 'mrz-pop-up-exp' ); ?></strong></label><br>
	<input type="number" id="mrzpop_overlay_opacity" name="mrzpop_overlay_opacity" min="0" max="100" step="1" value="<?php echo esc_attr( $values['overlay_opacity'] ); ?>">
</p>
