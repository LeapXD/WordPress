<?php
/**
 * Archive setup.
 *
 * @package Kicks
 */

/**
 * Register Archive pages
 */
function xd_register_archive_pages() {

	if ( apply_filters( 'xd_project_archive_enabled', true ) ) {
		xd_register_archive_page( 'project' );
	}

	if ( apply_filters( 'xd_promotion_archive_enabled', true ) ) {
		xd_register_archive_page( 'promotion' );
	}

}

add_action( 'after_setup_theme', 'xd_register_archive_pages' );

/**
 * Register Archive pages
 *
 * @param array $query_vars the original query vars.
 */
function xd_filter_query_vars( $query_vars ) {

	if ( apply_filters( 'xd_project_archive_enabled', true ) ) {

		$query_vars = array_merge(
			$query_vars,
			array(
				'project-type',
			)
		);
	}
	return $query_vars;
}

add_filter( 'query_vars', 'xd_filter_query_vars' );
