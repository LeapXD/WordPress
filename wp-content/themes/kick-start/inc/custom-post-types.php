<?php
/**
 * Plugin post types.
 *
 * @package Kicks
 * @phpcs:disable Squiz.PHP.CommentedOutCode.Found
 * @phpcs:disable Squiz.Commenting.InlineComment.InvalidEndChar
 */

/**
 * Registers custom post types
 */
function xdc_register_post_types() {

	// example of a custom post type.
	$labels = array(
		'name'                  => _x( 'Examples', 'post type general name' ),
		'singular_name'         => _x( 'Example', 'post type singular name' ),
		'add_new'               => _x( 'Add New', 'page' ),
		'add_new_item'          => __( 'Add New Example' ),
		'edit_item'             => __( 'Edit Example' ),
		'new_item'              => __( 'New Example' ),
		'view_item'             => __( 'View Example' ),
		'view_items'            => __( 'View Examples' ),
		'search_items'          => __( 'Search Examples' ),
		'not_found'             => __( 'No Examples found.' ),
		'not_found_in_trash'    => __( 'No Examples found in Trash.' ),
		'parent_item_colon'     => __( 'Parent Example:' ),
		'all_items'             => __( 'All Examples' ),
		'archives'              => __( 'Example Archives' ),
		'attributes'            => __( 'Example Attributes' ),
		'insert_into_item'      => __( 'Insert into example' ),
		'uploaded_to_this_item' => __( 'Uploaded to this example' ),
		'featured_image'        => _x( 'Featured image', 'example' ),
		'set_featured_image'    => _x( 'Set featured image', 'example' ),
		'remove_featured_image' => _x( 'Remove featured image', 'example' ),
		'use_featured_image'    => _x( 'Use as featured image', 'example' ),
	);

	register_post_type(
		'example',
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
			'rewrite'               => array( 'slug' => 'examples' ),
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

// add_action( 'init', __NAMESPACE__ . '\xdc_register_post_types' );


/**
 * Filters the arguments for registering a post type.
 * Use this to modify the post types registered by the parent theme or plugins.
 *
 * @param array  $args      Array of arguments for registering a post type.
 * @param string $post_type Post type key.
 */
function xdc_filter_register_post_types( $args, $post_type ) {

	if ( 'example' === $post_type ) {
		$args['rewrite'] = array(
			'slug'       => 'examples',
			'with_front' => false,
		);
	}

	return $args;
}

// add_filter( 'register_post_type_args', 'xdc_filter_register_post_types', 10, 2 );

