<?php
/**
 * Balisage d'une pop-up.
 *
 * @var int    $popup_id Identifiant.
 * @var array  $values   Réglages.
 * @var string $title    Titre, nom accessible de la fenêtre.
 * @var bool   $has_image Image à afficher à gauche.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$mrzpop_style = sprintf(
	'--mrzpop-width:%1$dpx;--mrzpop-overlay:%2$s;--mrzpop-image-width:%3$d%%;--mrzpop-background:%4$s',
	$values['width'],
	self::overlay_css( $values['overlay_color'], $values['overlay_opacity'] ),
	$values['image_width'],
	$values['background']
);
if ( '' !== $values['text_color'] ) {
	$mrzpop_style .= ';--mrzpop-color:' . $values['text_color'];
}
?>
<div class="mrzpop<?php echo $has_image ? ' mrzpop--has-image' : ''; ?><?php echo '' !== $values['text_color'] ? ' mrzpop--has-color' : ''; ?>" id="mrzpop-<?php echo esc_attr( $popup_id ); ?>" style="<?php echo esc_attr( $mrzpop_style ); ?>"<?php echo $popup_id === $this->preview_id ? ' data-mrzpop-preview' : ''; ?> data-mrzpop-start="<?php echo esc_attr( self::start_timestamp( $values['start_date'] ) ); ?>" data-mrzpop-end="<?php echo esc_attr( self::end_timestamp( $values['end_date'] ) ); ?>" data-mrzpop-hide-mobile="<?php echo esc_attr( $values['hide_mobile'] ); ?>" data-mrzpop-trigger="<?php echo esc_attr( $values['trigger'] ); ?>" data-mrzpop-value="<?php echo esc_attr( $values['trigger_value'] ); ?>" data-mrzpop-frequency="<?php echo esc_attr( $values['frequency'] ); ?>" hidden>
	<div class="mrzpop__dialog" role="dialog" aria-modal="true" aria-label="<?php echo esc_attr( '' !== $title ? wp_strip_all_tags( $title ) : __( 'Popup', 'mrz-pop-up-exp' ) ); ?>" tabindex="-1">
		<button type="button" class="mrzpop__close" aria-label="<?php esc_attr_e( 'Close', 'mrz-pop-up-exp' ); ?>"><span aria-hidden="true">&times;</span></button>
		<div class="mrzpop__body">
			<?php if ( $has_image ) : ?>
				<div class="mrzpop__image"><?php echo wp_get_attachment_image( $values['image_id'], 'large' ); ?></div>
			<?php endif; ?>
			<div class="mrzpop__content">
				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML filtré par wp_kses_post(), puis sortie des shortcodes (formulaires compris).
				echo do_shortcode( wp_kses_post( $values['html'] ) );
				?>
			</div>
		</div>
	</div>
</div>
