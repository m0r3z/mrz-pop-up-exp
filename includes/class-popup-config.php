<?php
/**
 * Réglages d'une pop-up : métaboxes, sauvegarde et lecture.
 */

namespace Mrzpop;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class PopupConfig {

	const NONCE_ACTION = 'mrzpop_save';
	const NONCE_NAME   = 'mrzpop_nonce';
	const META_PREFIX  = '_mrzpop_';

	public function register() {
		add_action( 'add_meta_boxes_' . MRZPOP_CPT, array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post_' . MRZPOP_CPT, array( $this, 'save' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
		add_action( 'post_submitbox_misc_actions', array( $this, 'render_preview_link' ) );
	}

	public static function defaults() {
		return array(
			'html'            => '',
			'image_id'        => 0,
			'width'           => 600,
			'image_width'     => 40,
			'background'      => '#ffffff',
			'overlay_color'   => '#000000',
			'overlay_opacity' => 60,
			'start_date'      => '',
			'end_date'        => '',
			'display'         => 'all',
			'page_ids'        => array(),
			'hide_mobile'     => 1,
			'trigger'         => 'immediate',
			'trigger_value'   => 5,
			'frequency'       => 'session',
		);
	}

	private static function display_choices() {
		return array(
			'home'     => __( 'Homepage', 'mrz-pop-up-exp' ),
			'all'      => __( 'All pages', 'mrz-pop-up-exp' ),
			'not_home' => __( 'All but homepage', 'mrz-pop-up-exp' ),
			'specific' => __( 'Specific post(s)/page(s)', 'mrz-pop-up-exp' ),
			'none'     => __( 'Nowhere (disabled)', 'mrz-pop-up-exp' ),
		);
	}

	private static function trigger_choices() {
		return array(
			'immediate' => __( 'Immediately', 'mrz-pop-up-exp' ),
			'delay'     => __( 'After some seconds on the page', 'mrz-pop-up-exp' ),
			'scroll'    => __( 'After scrolling some pixels', 'mrz-pop-up-exp' ),
			'half'      => __( 'At half of the page', 'mrz-pop-up-exp' ),
			'bottom'    => __( 'At the bottom of the page', 'mrz-pop-up-exp' ),
			'minutes'   => __( 'After some minutes on the site', 'mrz-pop-up-exp' ),
			'pageviews' => __( 'After some pages viewed in 90 days', 'mrz-pop-up-exp' ),
			'exit'      => __( 'On exit intent (not on touch screens)', 'mrz-pop-up-exp' ),
		);
	}

	/**
	 * Unité de la valeur, pour les déclencheurs qui en ont une.
	 */
	private static function trigger_units() {
		return array(
			'delay'     => __( 'seconds', 'mrz-pop-up-exp' ),
			'scroll'    => __( 'pixels', 'mrz-pop-up-exp' ),
			'minutes'   => __( 'minutes', 'mrz-pop-up-exp' ),
			'pageviews' => __( 'pages', 'mrz-pop-up-exp' ),
		);
	}

	private static function frequency_choices() {
		return array(
			'once'    => __( 'Never again', 'mrz-pop-up-exp' ),
			'session' => __( 'Not again during the session', 'mrz-pop-up-exp' ),
			'7'       => __( 'Not again for 7 days', 'mrz-pop-up-exp' ),
			'30'      => __( 'Not again for 30 days', 'mrz-pop-up-exp' ),
			'90'      => __( 'Not again for 90 days', 'mrz-pop-up-exp' ),
			'always'  => __( 'Always show it', 'mrz-pop-up-exp' ),
		);
	}

	/**
	 * Valeur si elle fait partie des choix, sinon la valeur par défaut.
	 */
	private static function sanitize_choice( $value, $choices, $default ) {
		return array_key_exists( (string) $value, $choices ) ? (string) $value : $default;
	}

	/**
	 * Date AAAA-MM-JJ valide, ou chaîne vide.
	 */
	private static function sanitize_date( $date ) {
		return preg_match( '/^(\d{4})-(\d{2})-(\d{2})$/', (string) $date, $parts ) && checkdate( (int) $parts[2], (int) $parts[3], (int) $parts[1] ) ? $parts[0] : '';
	}

	/**
	 * Réglages d'une pop-up, complétés par les valeurs par défaut.
	 */
	public static function get_values( $post_id ) {
		$values = self::defaults();
		foreach ( $values as $key => $default ) {
			if ( metadata_exists( 'post', $post_id, self::META_PREFIX . $key ) ) {
				$values[ $key ] = get_post_meta( $post_id, self::META_PREFIX . $key, true );
			}
		}
		return self::sanitize( $values );
	}

	private static function sanitize( $raw ) {
		$defaults = self::defaults();

		// wp_kses retire les balises mais garde leur contenu : le code est supprimé avant.
		$html = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', (string) $raw['html'] );

		$width = absint( $raw['width'] );
		$color = sanitize_hex_color( (string) $raw['overlay_color'] );

		$background = sanitize_hex_color( (string) $raw['background'] );

		$image_width   = absint( $raw['image_width'] );
		$trigger_value = absint( $raw['trigger_value'] );

		return array(
			'html'            => wp_kses_post( (string) $html ),
			'image_id'        => absint( $raw['image_id'] ),
			'width'           => $width ? min( 2000, max( 200, $width ) ) : $defaults['width'],
			'image_width'     => $image_width ? min( 90, max( 10, $image_width ) ) : $defaults['image_width'],
			'background'      => $background ? $background : $defaults['background'],
			'overlay_color'   => $color ? $color : $defaults['overlay_color'],
			'overlay_opacity' => min( 100, max( 0, (int) $raw['overlay_opacity'] ) ),
			'start_date'      => self::sanitize_date( $raw['start_date'] ),
			'end_date'        => self::sanitize_date( $raw['end_date'] ),
			'display'         => self::sanitize_choice( $raw['display'], self::display_choices(), $defaults['display'] ),
			'page_ids'        => array_values( array_filter( wp_parse_id_list( $raw['page_ids'] ) ) ),
			'hide_mobile'     => $raw['hide_mobile'] ? 1 : 0,
			'trigger'         => self::sanitize_choice( $raw['trigger'], self::trigger_choices(), $defaults['trigger'] ),
			'trigger_value'   => $trigger_value ? min( 100000, $trigger_value ) : $defaults['trigger_value'],
			'frequency'       => self::sanitize_choice( $raw['frequency'], self::frequency_choices(), $defaults['frequency'] ),
		);
	}

	public function add_meta_boxes() {
		add_meta_box( 'mrzpop-content', __( 'Content', 'mrz-pop-up-exp' ), array( $this, 'render_content' ), MRZPOP_CPT, 'normal', 'high' );
		add_meta_box( 'mrzpop-appearance', __( 'Appearance', 'mrz-pop-up-exp' ), array( $this, 'render_appearance' ), MRZPOP_CPT, 'side' );
		add_meta_box( 'mrzpop-display', __( 'Display', 'mrz-pop-up-exp' ), array( $this, 'render_display' ), MRZPOP_CPT, 'side' );
	}

	public function render_content( $post ) {
		$values = self::get_values( $post->ID );
		wp_nonce_field( self::NONCE_ACTION, self::NONCE_NAME );
		require MRZPOP_DIR . 'admin/views/metabox-content.php';
	}

	public function render_appearance( $post ) {
		$values = self::get_values( $post->ID );
		require MRZPOP_DIR . 'admin/views/metabox-appearance.php';
	}

	public function render_display( $post ) {
		$values      = self::get_values( $post->ID );
		$choices     = self::display_choices();
		$triggers    = self::trigger_choices();
		$units       = self::trigger_units();
		$frequencies = self::frequency_choices();
		require MRZPOP_DIR . 'admin/views/metabox-display.php';
	}

	public function render_preview_link( $post ) {
		if ( MRZPOP_CPT !== $post->post_type || 'auto-draft' === $post->post_status ) {
			return;
		}
		printf(
			'<div class="misc-pub-section"><a class="button" href="%1$s" target="_blank">%2$s</a><p class="description">%3$s</p></div>',
			esc_url( Frontend::preview_url( $post->ID ) ),
			esc_html__( 'Preview popup', 'mrz-pop-up-exp' ),
			esc_html__( 'Shows the last saved version.', 'mrz-pop-up-exp' )
		);
	}

	public function enqueue( $hook ) {
		if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) || MRZPOP_CPT !== get_current_screen()->post_type ) {
			return;
		}

		wp_enqueue_media();
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style( 'mrz-pop-up-exp-admin', MRZPOP_URL . 'admin/css/admin.css', array(), MRZPOP_VERSION );
		wp_enqueue_script( 'mrz-pop-up-exp-admin', MRZPOP_URL . 'admin/js/admin.js', array( 'jquery', 'wp-color-picker', 'wp-api-fetch', 'wp-html-entities' ), MRZPOP_VERSION, true );
		wp_localize_script(
			'mrz-pop-up-exp-admin',
			'mrzpopAdmin',
			array(
				// false si l'utilisateur a désactivé la coloration syntaxique.
				'codeEditor' => wp_enqueue_code_editor( array( 'type' => 'text/html' ) ),
				'mediaTitle' => __( 'Choose an image', 'mrz-pop-up-exp' ),
				'remove'     => __( 'Remove', 'mrz-pop-up-exp' ),
				'noResult'   => __( 'No results.', 'mrz-pop-up-exp' ),
			)
		);
	}

	public function save( $post_id ) {
		if (
			! isset( $_POST[ self::NONCE_NAME ] )
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ self::NONCE_NAME ] ) ), self::NONCE_ACTION )
			|| wp_is_post_autosave( $post_id )
			|| wp_is_post_revision( $post_id )
			|| ! current_user_can( 'edit_post', $post_id )
		) {
			return;
		}

		$raw = array();
		foreach ( self::defaults() as $key => $default ) {
			$field = 'mrzpop_' . $key;
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- nettoyé champ par champ dans sanitize().
			$raw[ $key ] = isset( $_POST[ $field ] ) && is_scalar( $_POST[ $field ] ) ? wp_unslash( $_POST[ $field ] ) : $default;
		}

		foreach ( self::sanitize( $raw ) as $key => $value ) {
			// update_post_meta() retire une couche d'échappement.
			update_post_meta( $post_id, self::META_PREFIX . $key, wp_slash( $value ) );
		}
	}
}
