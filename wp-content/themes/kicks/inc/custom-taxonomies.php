<?php
/**
 * Register custom taxonomies.
 *
 * @package Kicks.
 */

/**
 * Registers map taxonomies.
 */
function register_map_taxonomies() {

	if ( ! apply_filters( 'xd_map_pin_taxonomies_enabled', false ) ) {
		return;
	}

	register_taxonomy(
		'pin_category',
		'pin',
		array(
			'label'        => __( 'Category' ),
			'public'       => true,
			'rewrite'      => false,
			'hierarchical' => false,
		)
	);
}
add_action( 'init', 'register_map_taxonomies' );

/**
 * Registers floorplan taxonomies.
 *
 * @return void
 */
function register_floorplan_taxonomies() {

	if ( ! apply_filters( 'xd_floorplan_taxonomies_enabled', false ) ) {
		return;
	}

	$labels = array(
		'name'              => _x( 'Bedroom Options', 'taxonomy general name' ),
		'singular_name'     => _x( 'Bedroom Option', 'taxonomy singular name' ),
		'search_items'      => __( 'Search Bedroom Options' ),
		'all_items'         => __( 'All Bedroom Options' ),
		'parent_item'       => __( 'Parent Bedroom Option' ),
		'parent_item_colon' => __( 'Parent Bedroom Option:' ),
		'edit_item'         => __( 'Edit Bedroom Option' ),
		'update_item'       => __( 'Update Bedroom Option' ),
		'add_new_item'      => __( 'Add New Bedroom Option' ),
		'new_item_name'     => __( 'New Bedroom Option Name' ),
		'menu_name'         => __( 'Bedroom Options' ),
	);

	register_taxonomy(
		'bedroom',
		array( 'floorplan' ),
		array(
			'hierarchical'       => true,
			'labels'             => $labels,
			'show_ui'            => true,
			'show_in_quick_edit' => false,
			'show_admin_column'  => false,
			'query_var'          => false,
			'publicly_queryable' => false,
			'show_in_rest'       => true,
		)
	);
	$labels = array(
		'name'              => _x( 'Bathroom Options', 'taxonomy general name' ),
		'singular_name'     => _x( 'Bathroom Option', 'taxonomy singular name' ),
		'search_items'      => __( 'Search Bathroom Options' ),
		'all_items'         => __( 'All Bathroom Options' ),
		'parent_item'       => __( 'Parent Bathroom Option' ),
		'parent_item_colon' => __( 'Parent Bathroom Option:' ),
		'edit_item'         => __( 'Edit Bathroom Option' ),
		'update_item'       => __( 'Update Bathroom Option' ),
		'add_new_item'      => __( 'Add New Bathroom Option' ),
		'new_item_name'     => __( 'New Bathroom Option Name' ),
		'menu_name'         => __( 'Bathroom Options' ),
	);

	register_taxonomy(
		'bathroom',
		array( 'floorplan' ),
		array(
			'hierarchical'       => true,
			'labels'             => $labels,
			'show_ui'            => true,
			'show_in_quick_edit' => false,
			'show_admin_column'  => false,
			'query_var'          => false,
			'publicly_queryable' => false,
			'show_in_rest'       => true,
		)
	);

}
add_action( 'init', 'register_floorplan_taxonomies' );


/**
 * Registers project taxonomies.
 *
 * @return void
 */
function register_project_taxonomies() {

	if ( ! apply_filters( 'xd_project_taxonomies_enabled', false ) ) {
		return;
	}

	$labels = array(
		'name'              => 'Project Type',
		'singular_name'     => 'Project Type',
		'search_items'      => 'Search Project Types',
		'all_items'         => 'All Project Types',
		'parent_item'       => 'Parent Project Type',
		'parent_item_colon' => 'Parent Project Type:',
		'edit_item'         => 'Edit Project Type',
		'update_item'       => 'Update Project Type',
		'add_new_item'      => 'Add New Project Type',
		'new_item_name'     => 'New Project Type Name',
		'menu_name'         => 'Project Type',

	);

	register_taxonomy(
		'project_type',
		array( 'project' ),
		array(
			'hierarchical'       => true,
			'labels'             => $labels,
			'show_ui'            => true,
			'show_in_quick_edit' => false,
			'show_admin_column'  => false,
			'query_var'          => false,
			'publicly_queryable' => false,
			'show_in_rest'       => true,
		)
	);

	$labels = array(
		'name'              => 'Location',
		'singular_name'     => 'Location',
		'search_items'      => 'Search Locations',
		'all_items'         => 'All Locations',
		'parent_item'       => 'Parent Location',
		'parent_item_colon' => 'Parent Location:',
		'edit_item'         => 'Edit Location',
		'update_item'       => 'Update Location',
		'add_new_item'      => 'Add New Location',
		'new_item_name'     => 'New Location Name',
		'menu_name'         => 'Location',
	);

	register_taxonomy(
		'project_location',
		array( 'project' ),
		array(
			'hierarchical'       => true,
			'labels'             => $labels,
			'show_ui'            => true,
			'show_in_quick_edit' => false,
			'show_admin_column'  => false,
			'query_var'          => false,
			'publicly_queryable' => false,
			'show_in_rest'       => true,
		)
	);

}
add_action( 'init', 'register_project_taxonomies' );
