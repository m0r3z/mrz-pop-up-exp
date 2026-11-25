<?php
/**
 * Classe principale : démarrage des modules.
 */

namespace Mrzpop;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Plugin {

	public function boot() {
		// Traductions livrées dans languages/. Un pack de translate.wordpress.org
		// reste prioritaire s'il existe.
		add_action( 'init', array( $this, 'load_textdomain' ), 1 );

		( new CPT() )->register();
		( new PopupConfig() )->register();
		( new Frontend() )->register();
	}

	public function load_textdomain() {
		load_plugin_textdomain( 'mrz-pop-up-exp', false, dirname( MRZPOP_BASENAME ) . '/languages' );
	}
}
