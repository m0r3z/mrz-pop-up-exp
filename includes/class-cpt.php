<?php
/**
 * Type de contenu des pop-ups.
 */

namespace Mrzpop;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class CPT {

	public function register() {
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_menu_icon_style' ) );
	}

	public function register_post_type() {
		$labels = array(
			'name'               => _x( 'MRZ Pop Up', 'post type general name', 'mrz-pop-up-exp' ),
			'singular_name'      => _x( 'Popup', 'post type singular name', 'mrz-pop-up-exp' ),
			'menu_name'          => _x( 'MRZ Pop Up', 'admin menu', 'mrz-pop-up-exp' ),
			'add_new'            => __( 'Add new popup', 'mrz-pop-up-exp' ),
			'add_new_item'       => __( 'Add new popup', 'mrz-pop-up-exp' ),
			'new_item'           => __( 'New popup', 'mrz-pop-up-exp' ),
			'edit_item'          => __( 'Edit popup', 'mrz-pop-up-exp' ),
			'all_items'          => __( 'All popups', 'mrz-pop-up-exp' ),
			'search_items'       => __( 'Search popups', 'mrz-pop-up-exp' ),
			'not_found'          => __( 'No popups found.', 'mrz-pop-up-exp' ),
			'not_found_in_trash' => __( 'No popups found in Trash.', 'mrz-pop-up-exp' ),
		);

		$args = array(
			'labels'            => $labels,
			'public'            => false,
			'show_ui'           => true,
			'show_in_menu'      => true,
			'show_in_admin_bar' => false,
			'show_in_rest'      => false,
			'menu_icon'         => 'none',
			'menu_position'     => 90,
			// Une pop-up touche tout le site : réservée aux éditeurs et administrateurs.
			'capability_type'   => 'page',
			'map_meta_cap'      => true,
			'hierarchical'      => false,
			'supports'          => array( 'title' ),
			'has_archive'       => false,
			'rewrite'           => false,
			'query_var'         => false,
		);

		register_post_type( MRZPOP_CPT, $args );
	}

	/**
	 * Icône du menu en CSS mask-image, pour suivre les couleurs de l'admin.
	 */
	public function enqueue_menu_icon_style() {
		$handle = 'mrz-pop-up-exp-menu-icon';
		wp_register_style( $handle, false, array(), MRZPOP_VERSION );
		wp_enqueue_style( $handle );

		$url      = esc_url( MRZPOP_URL . 'assets/menu-icon.svg?ver=' . MRZPOP_VERSION );
		$selector = '#adminmenu li.menu-top[id*="' . MRZPOP_CPT . '"]';

		$css  = $selector . ' .wp-menu-image{background:none!important;background-color:rgba(240,246,252,.6)!important;';
		$css .= '-webkit-mask:url(\'' . $url . '\') no-repeat 9px 7px/20px;mask:url(\'' . $url . '\') no-repeat 9px 7px/20px;}';
		$css .= $selector . ' .wp-menu-image::before{display:none;}';
		$css .= $selector . ':hover .wp-menu-image,';
		$css .= $selector . '.wp-has-current-submenu .wp-menu-image,';
		$css .= $selector . '.current .wp-menu-image{background-color:#fff!important;}';

		wp_add_inline_style( $handle, $css );
	}
}
