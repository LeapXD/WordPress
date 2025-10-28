<?php
/**
 * Theme Rest API.
 *
 * @package kicks.
 */

/**
 * Register a custom rest route for gravity forms.
 */
function xd_register_gravity_forms_route() {

	register_rest_route(
		'xd/v1',
		'/forms',
		array(
			'methods'             => 'GET',
			'callback'            => 'xd_gravity_forms_callback',
			'permission_callback' => '__return_true',
		)
	);

}

/**
 * Gravity forms callback.
 *
 * @param WP_REST_Request $request the request object.
 */
function xd_gravity_forms_callback( $request ) {

	if ( ! class_exists( 'GFAPI' ) ) {
		return rest_ensure_response( array() );
	}

	$search = $request->get_param( 'search' );
	$forms  = GFAPI::get_forms();
	$forms  = array_map(
		function( $form ) {
			return array(
				'id'    => $form['id'],
				'title' => $form['title'],
			);
		},
		$forms
	);
	$forms  = array_filter(
		$forms,
		function( $form ) use ( $search ) {
			return empty( $search ) || false !== stripos( $form['title'], $search );
		}
	);

	return rest_ensure_response( $forms );
}

add_action( 'rest_api_init', 'xd_register_gravity_forms_route' );
