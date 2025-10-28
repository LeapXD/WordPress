<?php

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( defined( 'GOOGLE_MAPS_API_KEY' ) ) {
	add_filter(
		'acf/settings/google_api_key',
		function() {
			return GOOGLE_MAPS_API_KEY;
		}
	);
}

function get_js_object() {
	return array(
		'google_maps_api_key' => GOOGLE_MAPS_API_KEY,
	);
}

