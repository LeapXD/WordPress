<?php
/**
 * Plugin Name: Leap Editor
 * Plugin URI: https://leapxd.net
 * Description: A suite of blocks and customizations for the WordPress Gutenberg Editor.
 * Version: 2.2.0
 * Author: Leap XD
 * Author URI: https://leapxd.com
 * Text Domain: xd
 * Update URI:  https://leapxd.net/kicks/plugins/
 *
 * @package Leap Editor
 */

namespace leap\editor;

require_once __DIR__ . '/inc/admin-timber-selection.php';

$timber_loader_version = get_option( 'timber_loader_version', 'v1' );
if ( 'v1' === $timber_loader_version ) {
		require_once __DIR__ . '/timber/v1/vendor/autoload.php';
} else {
		require_once __DIR__ . '/timber/v2/vendor/autoload.php';
}

add_action(
	'admin_init',
	function() {

		if ( \is_plugin_active( 'timber-library/timber.php' ) ) {
			\deactivate_plugins( 'timber-library/timber.php' );
		}
	}
);

require_once __DIR__ . '/inc/class-xd-block-metadata-registry.php';
require_once __DIR__ . '/inc/types/types.php';
require_once __DIR__ . '/inc/functions.php';
require_once __DIR__ . '/inc/timber-filters.php';
require_once __DIR__ . '/inc/editor-setup.php';
require_once __DIR__ . '/inc/meta-setup.php';
require_once __DIR__ . '/inc/block-setup.php';
require_once __DIR__ . '/inc/block-render.php';
require_once __DIR__ . '/inc/scripts.php';
require_once __DIR__ . '/inc/update.php';


add_action(
	'admin_init',
	function() {

		if ( \is_plugin_active( 'timber-library/timber.php' ) ) {
			\deactivate_plugins( 'timber-library/timber.php' );
		}
	}
);
