<?php
/**
 * Nettoyage à la désinstallation : supprime les pop-ups et leurs metas.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$mrzpop_popup_ids = get_posts(
	array(
		'post_type'   => 'mrzpop_popup',
		// Liste explicite : « any » ignore la corbeille et les brouillons automatiques.
		'post_status' => array_keys( get_post_stati() ),
		'numberposts' => -1,
		'fields'      => 'ids',
	)
);

foreach ( $mrzpop_popup_ids as $mrzpop_popup_id ) {
	wp_delete_post( $mrzpop_popup_id, true );
}
