<?php
/**
 * Affichage des pop-ups sur le site.
 */

namespace Mrzpop;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Frontend {

	const PREVIEW_ARG = 'mrzpop_preview';

	/**
	 * Identifiant de la pop-up en aperçu, 0 sinon.
	 *
	 * @var int
	 */
	private $preview_id = 0;

	/**
	 * @var array|null
	 */
	private $active_popups = null;

	public function register() {
		add_action( 'template_redirect', array( $this, 'detect_preview' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
		// Avant l'impression des scripts du pied de page (20), pour ceux des shortcodes.
		add_action( 'wp_footer', array( $this, 'render' ), 5 );
	}

	public static function preview_url( $post_id ) {
		return add_query_arg(
			array(
				self::PREVIEW_ARG => $post_id,
				'_wpnonce'        => wp_create_nonce( self::PREVIEW_ARG . '_' . $post_id ),
			),
			home_url( '/' )
		);
	}

	public function detect_preview() {
		if ( ! isset( $_GET[ self::PREVIEW_ARG ], $_GET['_wpnonce'] ) ) {
			return;
		}

		$post_id = absint( wp_unslash( $_GET[ self::PREVIEW_ARG ] ) );
		$nonce   = sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) );

		if (
			wp_verify_nonce( $nonce, self::PREVIEW_ARG . '_' . $post_id )
			&& MRZPOP_CPT === get_post_type( $post_id )
			&& current_user_can( 'edit_post', $post_id )
		) {
			$this->preview_id = $post_id;
			nocache_headers();
		}
	}

	/**
	 * Pop-ups publiées et encore d'actualité, les plus récentes d'abord. Date de
	 * début, mobile et ouverture sont vérifiés dans le navigateur, pour rester
	 * justes sur une page en cache.
	 *
	 * @return array Identifiant => réglages.
	 */
	private function active_popups() {
		if ( null === $this->active_popups ) {
			$this->active_popups = array();

			$ids = get_posts(
				array(
					'post_type'   => MRZPOP_CPT,
					'post_status' => 'publish',
					'numberposts' => -1,
					'fields'      => 'ids',
				)
			);
			// Une seule requête pour tous les réglages.
			update_meta_cache( 'post', $ids );

			foreach ( $ids as $id ) {
				$values = PopupConfig::get_values( $id );
				$end    = self::end_timestamp( $values['end_date'] );

				// Une date de fin passée le reste : inutile d'envoyer la pop-up.
				if ( 'none' !== $values['display'] && ( ! $end || $end > time() ) ) {
					$this->active_popups[ $id ] = $values;
				}
			}
		}
		return $this->active_popups;
	}

	/**
	 * Pop-ups à charger sur la page.
	 */
	private function popup_ids() {
		return $this->preview_id
			? array( $this->preview_id )
			: array_keys( array_filter( $this->active_popups(), array( $this, 'is_for_current_page' ) ) );
	}

	/**
	 * Compteurs utilisés par au moins une pop-up du site : ils tournent sur toutes
	 * les pages, même sans pop-up.
	 */
	private function counters() {
		$triggers = $this->preview_id ? array() : wp_list_pluck( $this->active_popups(), 'trigger' );

		return array_values( array_intersect( array( 'pageviews', 'minutes' ), $triggers ) );
	}

	private function is_for_current_page( $values ) {
		switch ( $values['display'] ) {
			case 'home':
				return is_front_page();
			case 'all':
				return true;
			case 'not_home':
				return ! is_front_page();
			case 'specific':
				return in_array( self::current_content_id(), $values['page_ids'], true );
		}
		return false;
	}

	/**
	 * Contenu affiché, 0 pour une archive. La page des articles et la boutique
	 * WooCommerce sont des archives rattachées à une page.
	 */
	private static function current_content_id() {
		if ( function_exists( 'is_shop' ) && is_shop() ) {
			return (int) wc_get_page_id( 'shop' );
		}
		return is_singular() || is_home() ? get_queried_object_id() : 0;
	}

	private static function start_timestamp( $date ) {
		return $date ? ( new \DateTimeImmutable( $date, wp_timezone() ) )->getTimestamp() : 0;
	}

	/**
	 * Le jour de fin est inclus : la pop-up s'arrête au début du lendemain.
	 */
	private static function end_timestamp( $date ) {
		return $date ? ( new \DateTimeImmutable( $date . ' +1 day', wp_timezone() ) )->getTimestamp() : 0;
	}

	public function enqueue() {
		$deps     = array();
		$counters = $this->counters();

		if ( $counters ) {
			$deps[] = 'mrz-pop-up-exp-counter';
			wp_enqueue_script( 'mrz-pop-up-exp-counter', MRZPOP_URL . 'public/js/counter.js', array(), MRZPOP_VERSION, true );
			wp_localize_script( 'mrz-pop-up-exp-counter', 'mrzpopCounters', $counters );
		}

		if ( ! $this->popup_ids() ) {
			return;
		}
		wp_enqueue_style( 'mrz-pop-up-exp', MRZPOP_URL . 'public/css/public.css', array(), MRZPOP_VERSION );
		wp_enqueue_script( 'mrz-pop-up-exp', MRZPOP_URL . 'public/js/mrz-pop-up-exp.js', $deps, MRZPOP_VERSION, true );
	}

	public function render() {
		foreach ( $this->popup_ids() as $popup_id ) {
			$this->render_popup( $popup_id );
		}
	}

	private function render_popup( $popup_id ) {
		$values    = PopupConfig::get_values( $popup_id );
		$title     = get_the_title( $popup_id );
		// Une image supprimée de la médiathèque ne laisse pas de colonne vide.
		$has_image = $values['image_id'] && wp_attachment_is_image( $values['image_id'] );

		require MRZPOP_DIR . 'public/views/popup.php';
	}

	/**
	 * Couleur de l'overlay avec son opacité, en CSS.
	 */
	private static function overlay_css( $hex, $opacity ) {
		$hex = ltrim( $hex, '#' );
		if ( 3 === strlen( $hex ) ) {
			$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
		}
		list( $red, $green, $blue ) = array_map( 'hexdec', str_split( $hex, 2 ) );

		return sprintf( 'rgb(%1$d %2$d %3$d / %4$d%%)', $red, $green, $blue, $opacity );
	}
}
