<?php
/**
 * Script Enqueue
 *
 * @package Leap Editor
 */

namespace Leap\Editor\Scripts;

use Leap\Editor\Block_Setup;
use Leap\Editor\Meta_Setup;
use Leap\Editor\Editor_Setup;

use function Leap\Editor\Block_Setup\xd_get_block_settings;

/**
 * Enqueue block editor only JavaScript and CSS.
 */
function editor_enqueue_editor_scripts() {

	$assets          = require realpath( __DIR__ . '/../build/index.asset.php' );
	$style           = '/build/index.css';
	$legacy_styles   = '/build/legacy-scss.css';
	$script          = '/build/index.js';
	$enqueued_editor = false;
	if ( 'widgets' === get_current_screen()->id || 'customize' === get_current_screen()->id ) {
		$assets  = require realpath( __DIR__ . '/../build/widget-editor.asset.php' );
		$style   = '/build/index.css';
		$script  = '/build/widget-editor.js';
		$indices = array_flip( array_values( $assets['dependencies'] ) );
		if ( isset( $indices['wp-editor'] ) ) {
			unset( $assets['dependencies'][ $indices['wp-editor'] ] );
			$enqueued_editor = true;
		}
	}

	if ( ! isset( $assets['version'] ) ) {
		return;
	}

	wp_enqueue_style(
		'xd-editor-blocks-styles',
		plugins_url( 'kicks-editor' ) . $style,
		array( 'wp-edit-blocks' ),
		$assets['version']
	);

	if ( xd_theme_version_compare( '<=', '2.4.3' ) ) {
		wp_enqueue_style(
			'xd-editor-legacy-blocks-styles',
			plugins_url( 'kicks-editor' ) . $legacy_styles,
			array( 'wp-edit-blocks' ),
			$assets['version']
		);
	}

	wp_enqueue_style(
		'google-material-symbols',
		'https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0',
		array( 'wp-edit-blocks' ),
		$assets['version']
	);

	//phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NotInFooter
	wp_enqueue_script(
		'xd-editor-blocks-js',
		plugins_url( 'kicks-editor' ) . $script,
		$assets['dependencies'],
		defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? time() : $assets['version']
	);

	if ( $enqueued_editor ) {
		wp_add_inline_script( 'xd-editor-blocks-js', 'console.warn("Editor plugin attempted to enqueue wp-editor script. Components that rely on wp-editor cannot be used here");', 'before' );
	}
	$default_block_settings = Block_Setup\xd_get_default_block_settings();
	$block_types            = get_block_editor_server_block_settings();
	$block_settings         = array();
	foreach ( $block_types as $block_name => $block_type ) {
		if ( str_starts_with( $block_name, 'xd/' ) || str_starts_with( $block_name, 'acf/' ) || str_starts_with( $block_name, 'core/' ) ) {
			$block_settings[ $block_name ] = $block_type;
		}
	}

	$block_editor_settings = Editor_Setup\xd_get_block_editor_settings();
	$module_settings       = Block_Setup\xd_get_module_json();
	$plugin_settings       = Meta_Setup\xd_get_plugin_json();

	$xd_settings = array(
		'module_settings'         => ! empty( $module_settings ) ? $module_settings : array(),
		'plugin_settings'         => ! empty( $plugin_settings ) ? $plugin_settings : array(),
		'module_default_settings' => ! empty( $default_block_settings ) ? $default_block_settings : (object) array(),
		'block_settings'          => ! empty( $block_settings ) ? $block_settings : (object) array(),
		'editor_settings'         => ! empty( $block_editor_settings ) ? $block_editor_settings : (object) array(),
	);

	wp_localize_script( 'xd-editor-blocks-js', 'xd_settings', $xd_settings );

}
add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\editor_enqueue_editor_scripts' );
