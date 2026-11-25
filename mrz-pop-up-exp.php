<?php
/**
 * Plugin Name:       MRZ Pop Up Exp
 * Plugin URI:        https://github.com/m0r3z/mrz-pop-up-exp
 * Description:       A very simple popup: HTML text, optional image on the left, and basic display rules.
 * Version:           0.1.0
 * Requires at least: 6.3
 * Requires PHP:      7.4
 * Author:            Morez.co
 * Author URI:        https://morez.co
 * License:           GPLv3 or later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       mrz-pop-up-exp
 * Domain Path:       /languages
 *
 * MRZ Pop Up Exp — Copyright (C) 2026 Morez.co <hello@morez.co>
 * "MRZ Pop Up Exp" is a trademark of Morez.co. See LICENSE for full terms.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License v3 as published by
 * the Free Software Foundation. See LICENSE for the full license text.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MRZPOP_VERSION', '0.1.0' );
define( 'MRZPOP_FILE', __FILE__ );
define( 'MRZPOP_DIR', plugin_dir_path( __FILE__ ) );
define( 'MRZPOP_URL', plugin_dir_url( __FILE__ ) );
define( 'MRZPOP_BASENAME', plugin_basename( __FILE__ ) );
define( 'MRZPOP_CPT', 'mrzpop_popup' );

require_once MRZPOP_DIR . 'includes/class-cpt.php';
require_once MRZPOP_DIR . 'includes/class-popup-config.php';
require_once MRZPOP_DIR . 'includes/class-frontend.php';
require_once MRZPOP_DIR . 'includes/class-plugin.php';

add_action(
	'plugins_loaded',
	static function () {
		( new Mrzpop\Plugin() )->boot();
	}
);
