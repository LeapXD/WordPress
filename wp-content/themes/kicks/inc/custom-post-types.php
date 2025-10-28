<?php
/**
 * Register custom post types.
 *
 * @package Kicks.
 */

/**
 * Register team member post type.
 */
function register_team_post_type() {
	register_post_type(
		'team_member',
		array(
			'labels'             => array(
				'name'               => __( 'Team Members' ),
				'singular_name'      => __( 'Team Member' ),
				'add_new_item'       => __( 'Add Team Member' ),
				'edit_item'          => __( 'Edit Team Member' ),
				'new_item'           => __( 'New Team Member' ),
				'all_items'          => __( 'All Team Members' ),
				'view_item'          => __( 'View Team Member' ),
				'search_item'        => __( 'Search Team Members' ),
				'not_found'          => __( 'No Team Members found' ),
				'not_found_in_trash' => __( 'No Team Members found in Trash' ),
				'parent_item_colon'  => '',
				'menu_name'          => __( 'Team Members' ),
			),
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'menu_icon'          => 'dashicons-admin-users',
			'query_var'          => true,
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'rewrite'            => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'editor', 'custom-fields', 'thumbnail' ),
			'show_in_rest'       => true,
		)
	);
}
add_action( 'init', 'register_team_post_type' );


	/**
	 * Register map pin post type.
	 */
function register_map_pin_post_type() {

	if ( ! apply_filters( 'xd_map_pin_post_type_enabled', false ) ) {
		return;
	}

	register_post_type(
		'pin',
		array(
			'labels'             => array(
				'name'               => __( 'Map pins' ),
				'singular_name'      => __( 'Map pin' ),
				'add_new_item'       => __( 'Add New Map pin' ),
				'edit_item'          => __( 'Edit Map pin' ),
				'new_item'           => __( 'New Map pin' ),
				'all_items'          => __( 'All Map pins' ),
				'view_item'          => __( 'View Map pin' ),
				'search_item'        => __( 'Search Map pins' ),
				'not_found'          => __( 'No Map pins found' ),
				'not_found_in_trash' => __( 'No Map pins found in Trash' ),
				'parent_item_colon'  => '',
				'menu_name'          => __( 'Map pins' ),
			),
			'public'             => true,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'menu_icon'          => 'dashicons-location-alt',
			'query_var'          => false,
			'rewrite'            => false,
			'capability_type'    => 'page',
			'has_archive'        => false,
			'hierarchical'       => true,
			'show_in_rest'       => false,
			'menu_position'      => 3,
			'supports'           => array( 'title', 'custom-fields', 'revisions', 'thumbnail' ),
		)
	);

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
add_action( 'acf/init', 'register_map_pin_post_type' );


/**
 * Register  flyout post type.
 */
function register_flyout_post_type() {
	$labels = array(
		'name'                  => _x( 'Flyouts', 'post type general name' ),
		'singular_name'         => _x( 'Flyout', 'post type singular name' ),
		'add_new'               => _x( 'Add New', 'page' ),
		'add_new_item'          => __( 'Add New Flyout' ),
		'edit_item'             => __( 'Edit Flyout' ),
		'new_item'              => __( 'New Flyout' ),
		'view_item'             => __( 'View Flyout' ),
		'view_items'            => __( 'View Flyouts' ),
		'search_items'          => __( 'Search Flyouts' ),
		'not_found'             => __( 'No Flyouts found.' ),
		'not_found_in_trash'    => __( 'No Flyouts found in Trash.' ),
		'parent_item_colon'     => __( 'Parent Flyout:' ),
		'all_items'             => __( 'All Flyouts' ),
		'archives'              => __( 'Flyout Archives' ),
		'attributes'            => __( 'Flyout Attributes' ),
		'insert_into_item'      => __( 'Insert into Flyout' ),
		'uploaded_to_this_item' => __( 'Uploaded to this Flyout' ),
		'featured_image'        => _x( 'Featured image', 'Flyout' ),
		'set_featured_image'    => _x( 'Set featured image', 'Flyout' ),
		'remove_featured_image' => _x( 'Remove featured image', 'Flyout' ),
		'use_featured_image'    => _x( 'Use as featured image', 'Flyout' ),
	);

	$menu_icon = '
	<svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 -960 960 960" width="24" fill="#fff" ><path d="M200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H200Zm280-80h280v-560H480v560Z"/></svg>';

	register_post_type(
		'flyout',
		array(
			'labels'                => $labels,
			'description'           => '',
			'public'                => false,
			'hierarchical'          => false,
			'exclude_from_search'   => true,
			'publicly_queryable'    => false,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'show_in_nav_menus'     => true,
			'show_in_admin_bar'     => null,
			'menu_position'         => null,
			// phpcs:ignore 
			'menu_icon'             => 'data:image/svg+xml;base64,' . base64_encode( $menu_icon ),
			'capability_type'       => 'page',
			'capabilities'          => array(),
			'map_meta_cap'          => null,
			'supports'              => array( 'title', 'custom-fields', 'editor' ),
			'register_meta_box_cb'  => null,
			'taxonomies'            => array(),
			'has_archive'           => false,
			'rewrite'               => false,
			'query_var'             => false,
			'can_export'            => true,
			'delete_with_user'      => null,
			'show_in_rest'          => true,
			'rest_base'             => false,
			'rest_namespace'        => false,
			'rest_controller_class' => false,
		)
	);
}

add_action( 'init', 'register_flyout_post_type' );

/**
 * Register modal post type.
 */
function register_modal_post_type() {
	$labels = array(
		'name'                  => _x( 'Popups', 'post type general name' ),
		'singular_name'         => _x( 'Popup', 'post type singular name' ),
		'add_new'               => _x( 'Add New', 'page' ),
		'add_new_item'          => __( 'Add New Popup' ),
		'edit_item'             => __( 'Edit Popup' ),
		'new_item'              => __( 'New Popup' ),
		'view_item'             => __( 'View Popup' ),
		'view_items'            => __( 'View Modals' ),
		'search_items'          => __( 'Search Modals' ),
		'not_found'             => __( 'No Modals found.' ),
		'not_found_in_trash'    => __( 'No Modals found in Trash.' ),
		'parent_item_colon'     => __( 'Parent Popup:' ),
		'all_items'             => __( 'All Modals' ),
		'archives'              => __( 'Popup Archives' ),
		'attributes'            => __( 'Popup Attributes' ),
		'insert_into_item'      => __( 'Insert into Popup' ),
		'uploaded_to_this_item' => __( 'Uploaded to this Popup' ),
		'featured_image'        => _x( 'Featured image', 'Popup' ),
		'set_featured_image'    => _x( 'Set featured image', 'Popup' ),
		'remove_featured_image' => _x( 'Remove featured image', 'Popup' ),
		'use_featured_image'    => _x( 'Use as featured image', 'Popup' ),
	);

	$menu_icon = '
<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e8eaed"><path d="M320-320h320v-320H320v320ZM200-120q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120H200Zm0-80h560v-560H200v560Zm0-560v560-560Z"/></svg>';

	register_post_type(
		'modal',
		array(
			'labels'                => $labels,
			'description'           => '',
			'public'                => false,
			'hierarchical'          => false,
			'exclude_from_search'   => true,
			'publicly_queryable'    => false,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'show_in_nav_menus'     => true,
			'show_in_admin_bar'     => null,
			'menu_position'         => null,
			// phpcs:ignore 
			'menu_icon'             => 'data:image/svg+xml;base64,' . base64_encode( $menu_icon ),
			'capability_type'       => 'page',
			'capabilities'          => array(),
			'map_meta_cap'          => null,
			'supports'              => array( 'title', 'custom-fields', 'editor' ),
			'register_meta_box_cb'  => null,
			'taxonomies'            => array(),
			'has_archive'           => false,
			'rewrite'               => false,
			'query_var'             => false,
			'can_export'            => true,
			'delete_with_user'      => null,
			'show_in_rest'          => true,
			'rest_base'             => false,
			'rest_namespace'        => false,
			'rest_controller_class' => false,
		)
	);
}

add_action( 'init', 'register_modal_post_type' );

/**
 * Register Floorplan post type.
 */
function register_floorplan_post_type() {

	if ( ! apply_filters( 'xd_floorplan_post_type_enabled', false ) ) {
		return;
	}

	$labels = array(
		'name'                  => _x( 'Floorplans', 'post type general name' ),
		'singular_name'         => _x( 'Floorplan', 'post type singular name' ),
		'add_new'               => _x( 'Add New', 'page' ),
		'add_new_item'          => __( 'Add New Floorplan' ),
		'edit_item'             => __( 'Edit Floorplan' ),
		'new_item'              => __( 'New Floorplan' ),
		'view_item'             => __( 'View Floorplan' ),
		'view_items'            => __( 'View Floorplans' ),
		'search_items'          => __( 'Search Floorplans' ),
		'not_found'             => __( 'No floorplans found.' ),
		'not_found_in_trash'    => __( 'No floorplans found in Trash.' ),
		'parent_item_colon'     => __( 'Parent Floorplan:' ),
		'all_items'             => __( 'All Floorplans' ),
		'archives'              => __( 'Floorplan Archives' ),
		'attributes'            => __( 'Floorplan Attributes' ),
		'insert_into_item'      => __( 'Insert into floorplan' ),
		'uploaded_to_this_item' => __( 'Uploaded to this floorplan' ),
		'featured_image'        => _x( 'Featured image', 'floorplan' ),
		'set_featured_image'    => _x( 'Set featured image', 'floorplan' ),
		'remove_featured_image' => _x( 'Remove featured image', 'floorplan' ),
		'use_featured_image'    => _x( 'Use as featured image', 'floorplan' ),
	);

	register_post_type(
		'floorplan',
		array(
			'labels'                => $labels,
			'description'           => '',
			'public'                => false,
			'hierarchical'          => false,
			'exclude_from_search'   => false,
			'publicly_queryable'    => false,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'show_in_nav_menus'     => false,
			'show_in_admin_bar'     => null,
			'menu_position'         => null,
			'menu_icon'             => 'dashicons-admin-home',
			'capability_type'       => 'page',
			'capabilities'          => array(),
			'map_meta_cap'          => null,
			'supports'              => array( 'title', 'editor', 'excerpt', 'revisions', 'custom-fields' ),
			'register_meta_box_cb'  => null,
			'taxonomies'            => array(),
			'has_archive'           => false,
			'rewrite'               => false,
			'query_var'             => false,
			'can_export'            => true,
			'delete_with_user'      => null,
			'show_in_rest'          => true,
			'rest_base'             => false,
			'rest_namespace'        => false,
			'rest_controller_class' => false,
			'template'              => array( array( 'xd/floorplan-details' ) ),
			'template_lock'         => 'all',
		)
	);
}

add_action( 'init', 'register_floorplan_post_type' );




/**
 * Register project post type.
 */
function register_project_post_type() {

	if ( ! apply_filters( 'xd_project_post_type_enabled', false ) ) {
		return;
	}

	$labels = array(
		'name'                  => _x( 'Projects', 'post type general name' ),
		'singular_name'         => _x( 'Project', 'post type singular name' ),
		'add_new'               => _x( 'Add New', 'page' ),
		'add_new_item'          => __( 'Add New Project' ),
		'edit_item'             => __( 'Edit Project' ),
		'new_item'              => __( 'New Project' ),
		'view_item'             => __( 'View Project' ),
		'view_items'            => __( 'View Projects' ),
		'search_items'          => __( 'Search Projects' ),
		'not_found'             => __( 'No Projects found.' ),
		'not_found_in_trash'    => __( 'No Projects found in Trash.' ),
		'parent_item_colon'     => __( 'Parent Project:' ),
		'all_items'             => __( 'All Projects' ),
		'archives'              => __( 'Project Archives' ),
		'attributes'            => __( 'Project Attributes' ),
		'insert_into_item'      => __( 'Insert into project' ),
		'uploaded_to_this_item' => __( 'Uploaded to this project' ),
		'featured_image'        => _x( 'Featured image', 'project' ),
		'set_featured_image'    => _x( 'Set featured image', 'project' ),
		'remove_featured_image' => _x( 'Remove featured image', 'project' ),
		'use_featured_image'    => _x( 'Use as featured image', 'project' ),
	);

	register_post_type(
		'project',
		array(
			'labels'                => $labels,
			'description'           => '',
			'public'                => true,
			'hierarchical'          => false,
			'exclude_from_search'   => null,
			'publicly_queryable'    => null,
			'show_ui'               => null,
			'show_in_menu'          => null,
			'show_in_nav_menus'     => false,
			'show_in_admin_bar'     => null,
			'menu_position'         => null,
			'menu_icon'             => 'dashicons-portfolio',
			'capability_type'       => 'page',
			'capabilities'          => array(),
			'map_meta_cap'          => null,
			'supports'              => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'custom-fields' ),
			'register_meta_box_cb'  => null,
			'taxonomies'            => array(),
			'has_archive'           => false,
			'rewrite'               => array( 'slug' => 'projects' ),
			'query_var'             => true,
			'can_export'            => true,
			'delete_with_user'      => null,
			'show_in_rest'          => true,
			'rest_base'             => false,
			'rest_namespace'        => false,
			'rest_controller_class' => false,
			'template'              => array(),
			'template_lock'         => false,
		)
	);
}


add_action( 'init', 'register_project_post_type' );


/**
 * Register Accommodation post type.
 */
function register_accommodation_post_type() {

	if ( ! apply_filters( 'xd_accommodation_post_type_enabled', false ) ) {
		return;
	}

	$accommodation_labels = array(
		'name'                  => _x( 'Accommodations', 'post type general name' ),
		'singular_name'         => _x( 'Accommodation', 'post type singular name' ),
		'add_new'               => _x( 'Add New', 'page' ),
		'add_new_item'          => __( 'Add New Accommodation' ),
		'edit_item'             => __( 'Edit Accommodation' ),
		'new_item'              => __( 'New Accommodation' ),
		'view_item'             => __( 'View Accommodation' ),
		'view_items'            => __( 'View Accommodations' ),
		'search_items'          => __( 'Search Accommodations' ),
		'not_found'             => __( 'No Accommodations found.' ),
		'not_found_in_trash'    => __( 'No Accommodations found in Trash.' ),
		'parent_item_colon'     => __( 'Parent Accommodation:' ),
		'all_items'             => __( 'All Accommodations' ),
		'archives'              => __( 'Accommodation Archives' ),
		'attributes'            => __( 'Accommodation Attributes' ),
		'insert_into_item'      => __( 'Insert into Accommodation' ),
		'uploaded_to_this_item' => __( 'Uploaded to this Accommodation' ),
		'featured_image'        => _x( 'Featured image', 'Accommodation' ),
		'set_featured_image'    => _x( 'Set featured image', 'Accommodation' ),
		'remove_featured_image' => _x( 'Remove featured image', 'Accommodation' ),
		'use_featured_image'    => _x( 'Use as featured image', 'Accommodation' ),
	);

	register_post_type(
		'accommodation',
		array(
			'labels'                => $accommodation_labels,
			'description'           => '',
			'public'                => true,
			'hierarchical'          => false,
			'exclude_from_search'   => null,
			'publicly_queryable'    => null,
			'show_ui'               => null,
			'show_in_menu'          => null,
			'show_in_nav_menus'     => false,
			'show_in_admin_bar'     => null,
			'menu_position'         => null,
			'menu_icon'             => 'dashicons-admin-home',
			'capability_type'       => 'page',
			'capabilities'          => array(),
			'map_meta_cap'          => null,
			'supports'              => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'custom-fields' ),
			'register_meta_box_cb'  => null,
			'taxonomies'            => array(),
			'has_archive'           => false,
			'rewrite'               => array( 'slug' => 'accommodations' ),
			'query_var'             => true,
			'can_export'            => true,
			'delete_with_user'      => null,
			'show_in_rest'          => true,
			'rest_base'             => false,
			'rest_namespace'        => false,
			'rest_controller_class' => false,
			'template'              => array(),
			'template_lock'         => false,
		)
	);

}

add_action( 'init', 'register_accommodation_post_type' );


/**
 * Register Promotion post type.
 */
function register_promotion_post_type() {

	if ( ! apply_filters( 'xd_promotion_post_type_enabled', false ) ) {
		return;
	}

	$promotion_labels = array(
		'name'                  => _x( 'Promotions', 'post type general name' ),
		'singular_name'         => _x( 'Promotion', 'post type singular name' ),
		'add_new'               => _x( 'Add New', 'page' ),
		'add_new_item'          => __( 'Add New Promotion' ),
		'edit_item'             => __( 'Edit Promotion' ),
		'new_item'              => __( 'New Promotion' ),
		'view_item'             => __( 'View Promotion' ),
		'view_items'            => __( 'View Promotions' ),
		'search_items'          => __( 'Search Promotions' ),
		'not_found'             => __( 'No Promotions found.' ),
		'not_found_in_trash'    => __( 'No Promotions found in Trash.' ),
		'parent_item_colon'     => __( 'Parent Promotion:' ),
		'all_items'             => __( 'All Promotions' ),
		'archives'              => __( 'Promotion Archives' ),
		'attributes'            => __( 'Promotion Attributes' ),
		'insert_into_item'      => __( 'Insert into Promotion' ),
		'uploaded_to_this_item' => __( 'Uploaded to this Promotion' ),
		'featured_image'        => _x( 'Featured image', 'Promotion' ),
		'set_featured_image'    => _x( 'Set featured image', 'Promotion' ),
		'remove_featured_image' => _x( 'Remove featured image', 'Promotion' ),
		'use_featured_image'    => _x( 'Use as featured image', 'Promotion' ),
	);

	register_post_type(
		'promotion',
		array(
			'labels'                => $promotion_labels,
			'description'           => '',
			'public'                => true,
			'hierarchical'          => false,
			'exclude_from_search'   => null,
			'publicly_queryable'    => null,
			'show_ui'               => null,
			'show_in_menu'          => null,
			'show_in_nav_menus'     => false,
			'show_in_admin_bar'     => null,
			'menu_position'         => null,
			'menu_icon'             => 'dashicons-star-empty',
			'capability_type'       => 'page',
			'capabilities'          => array(),
			'map_meta_cap'          => null,
			'supports'              => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'custom-fields' ),
			'register_meta_box_cb'  => null,
			'taxonomies'            => array(),
			'has_archive'           => false,
			'rewrite'               => array( 'slug' => 'promotions' ),
			'query_var'             => true,
			'can_export'            => true,
			'delete_with_user'      => null,
			'show_in_rest'          => true,
			'rest_base'             => false,
			'rest_namespace'        => false,
			'rest_controller_class' => false,
			'template'              => array(),
			'template_lock'         => false,
		)
	);

}


add_action( 'init', 'register_promotion_post_type' );
