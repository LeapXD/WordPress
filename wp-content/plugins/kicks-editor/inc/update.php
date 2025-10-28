<?php
/**
 * Script Enqueue
 *
 * @package Leap Editor
 */

namespace Leap\Editor\Update;

/**
 * Checks for plugin updates.
 *
 * @param bool   $update update available.
 * @param array  $plugin_data      Plugin headers.
 * @param string $plugin_file      Plugin filename.
 */
function xd_check_for_plugin_updates( $update, $plugin_data, $plugin_file ) {

	static $response = false;

	if ( empty( $plugin_data['UpdateURI'] ) || ! empty( $update ) ) {
		return $update;
	}

	if ( false === $response ) {
		$response = wp_remote_get( $plugin_data['UpdateURI'], );
	}

	if ( empty( $response['body'] ) ) {
		return $update;
	}

	$custom_plugins_data = json_decode( $response['body'], true );

	if ( ! empty( $custom_plugins_data[ $plugin_file ] ) ) {
		return $custom_plugins_data[ $plugin_file ];
	} else {
		return $update;
	}

}

add_filter( 'update_plugins_leapxd.net', __NAMESPACE__ . '\xd_check_for_plugin_updates', 10, 3 );


