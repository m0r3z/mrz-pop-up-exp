<?php
/**
 * Métabox « Affichage ».
 *
 * @var array $values  Réglages de la pop-up.
 * @var array $choices Choix de pages, valeur => libellé.
 * @var array $triggers    Déclencheurs, valeur => libellé.
 * @var array $units       Unités des déclencheurs chiffrés, valeur => libellé.
 * @var array $frequencies Fréquences, valeur => libellé.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<p>
	<label for="mrzpop_start_date"><strong><?php esc_html_e( 'Start date', 'mrz-pop-up-exp' ); ?></strong></label><br>
	<input type="date" id="mrzpop_start_date" name="mrzpop_start_date" value="<?php echo esc_attr( $values['start_date'] ); ?>">
</p>
<p>
	<label for="mrzpop_end_date"><strong><?php esc_html_e( 'End date', 'mrz-pop-up-exp' ); ?></strong></label><br>
	<input type="date" id="mrzpop_end_date" name="mrzpop_end_date" value="<?php echo esc_attr( $values['end_date'] ); ?>">
</p>
<p class="description"><?php esc_html_e( 'Both dates are optional. The end date is included.', 'mrz-pop-up-exp' ); ?></p>
<p>
	<label for="mrzpop_display"><strong><?php esc_html_e( 'Display popup on', 'mrz-pop-up-exp' ); ?></strong></label><br>
	<select id="mrzpop_display" name="mrzpop_display">
		<?php foreach ( $choices as $mrzpop_value => $mrzpop_label ) : ?>
			<option value="<?php echo esc_attr( $mrzpop_value ); ?>" <?php selected( $values['display'], $mrzpop_value ); ?>><?php echo esc_html( $mrzpop_label ); ?></option>
		<?php endforeach; ?>
	</select>
</p>
<div class="mrzpop-pages"<?php echo 'specific' === $values['display'] ? '' : ' hidden'; ?>>
	<input type="hidden" id="mrzpop_page_ids" name="mrzpop_page_ids" value="<?php echo esc_attr( implode( ',', $values['page_ids'] ) ); ?>">
	<ul class="mrzpop-pages-selected">
		<?php foreach ( $values['page_ids'] as $mrzpop_page_id ) : ?>
			<li data-id="<?php echo esc_attr( $mrzpop_page_id ); ?>">
				<span><?php echo esc_html( wp_strip_all_tags( get_the_title( $mrzpop_page_id ) ) ); ?></span>
				<button type="button" class="button-link button-link-delete"><?php esc_html_e( 'Remove', 'mrz-pop-up-exp' ); ?></button>
			</li>
		<?php endforeach; ?>
	</ul>
	<label for="mrzpop_page_search"><?php esc_html_e( 'Add a post or page', 'mrz-pop-up-exp' ); ?></label>
	<input type="search" id="mrzpop_page_search" class="widefat" autocomplete="off" placeholder="<?php esc_attr_e( 'Search…', 'mrz-pop-up-exp' ); ?>">
	<ul class="mrzpop-pages-results" aria-live="polite"></ul>
</div>
<p>
	<input type="hidden" name="mrzpop_hide_mobile" value="0">
	<label>
		<input type="checkbox" name="mrzpop_hide_mobile" value="1" <?php checked( $values['hide_mobile'], 1 ); ?>>
		<?php esc_html_e( 'Hide on mobile (under 768 px)', 'mrz-pop-up-exp' ); ?>
	</label>
</p>
<p>
	<label for="mrzpop_trigger"><strong><?php esc_html_e( 'Open popup', 'mrz-pop-up-exp' ); ?></strong></label><br>
	<select id="mrzpop_trigger" name="mrzpop_trigger" class="widefat">
		<?php foreach ( $triggers as $mrzpop_value => $mrzpop_label ) : ?>
			<option value="<?php echo esc_attr( $mrzpop_value ); ?>" <?php selected( $values['trigger'], $mrzpop_value ); ?>><?php echo esc_html( $mrzpop_label ); ?></option>
		<?php endforeach; ?>
	</select>
</p>
<p class="mrzpop-trigger-value"<?php echo isset( $units[ $values['trigger'] ] ) ? '' : ' hidden'; ?>>
	<input type="number" id="mrzpop_trigger_value" name="mrzpop_trigger_value" class="small-text" min="1" max="100000" value="<?php echo esc_attr( $values['trigger_value'] ); ?>" aria-label="<?php esc_attr_e( 'Trigger value', 'mrz-pop-up-exp' ); ?>">
	<?php foreach ( $units as $mrzpop_value => $mrzpop_label ) : ?>
		<span data-trigger="<?php echo esc_attr( $mrzpop_value ); ?>"<?php echo $values['trigger'] === $mrzpop_value ? '' : ' hidden'; ?>><?php echo esc_html( $mrzpop_label ); ?></span>
	<?php endforeach; ?>
</p>
<p>
	<label for="mrzpop_frequency"><strong><?php esc_html_e( 'Once seen', 'mrz-pop-up-exp' ); ?></strong></label><br>
	<select id="mrzpop_frequency" name="mrzpop_frequency" class="widefat">
		<?php foreach ( $frequencies as $mrzpop_value => $mrzpop_label ) : ?>
			<option value="<?php echo esc_attr( $mrzpop_value ); ?>" <?php selected( $values['frequency'], (string) $mrzpop_value ); ?>><?php echo esc_html( $mrzpop_label ); ?></option>
		<?php endforeach; ?>
	</select>
</p>
<p class="description"><?php esc_html_e( 'After a click on a link of the popup, it no longer opens by itself, except with "Always show it".', 'mrz-pop-up-exp' ); ?></p>
<p>
	<strong><?php esc_html_e( 'Open with a link', 'mrz-pop-up-exp' ); ?></strong><br>
	<code><?php echo esc_html( '<a href="#mrzpop-' . $post->ID . '">…</a>' ); ?></code>
</p>
<p class="description"><?php esc_html_e( 'On the pages chosen above, even if already seen, and on mobile too.', 'mrz-pop-up-exp' ); ?></p>
