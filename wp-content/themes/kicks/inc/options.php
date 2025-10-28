<?php
/**
 * Theme options pages
 *
 * @package kicks.
 */

/**
 * Register an acf options page
 */
function create_option_page() {
	\acf_add_options_page(
		array(
			'page_title' => 'Scripts',
			'menu_title' => 'Scripts',
			'menu_slug'  => 'xd-admin',
			'capability' => 'edit_posts',
			'redirect'   => false,
		)
	);
}

/**
 * Register an afc sub options page
 *
 * @param string $label the options page  label.
 */
function create_option_subpage( $label ) {
	\acf_add_options_sub_page(
		array(
			'page_title'  => $label,
			'menu_title'  => $label,
			'parent_slug' => 'xd-admin',
		)
	);
}

if ( function_exists( 'acf_add_options_page' ) ) {
	add_action( 'acf/init', 'create_option_page' );
}

/**
 * Display option page in admin menu
 */
function template_parts() {
	add_menu_page(
		__( 'template_parts' ),
		__( 'Theme Options' ),
		'edit_posts',
		'site-editor.php?path=%2Fwp_template_part%2Fall',
		'',
		'dashicons-admin-generic',
		90
	);

	add_submenu_page(
		'site-editor.php?path=%2Fwp_template_part%2Fall',
		__( 'Alert Bar' ),
		__( 'Alert Bar' ),
		'edit_posts',
		add_query_arg(
			array(
				'postType' => 'wp_template_part',
				'canvas'   => 'edit',
				'postId'   => get_stylesheet() . '%2F%2Falert-bar',
			),
			'site-editor.php'
		),
		''
	);

	add_submenu_page(
		'site-editor.php?path=%2Fwp_template_part%2Fall',
		__( 'Company Details' ),
		__( 'Company Details' ),
		'edit_posts',
		add_query_arg(
			array(
				'postType' => 'wp_template_part',
				'canvas'   => 'edit',
				'postId'   => get_stylesheet() . '%2F%2Fcompany-details',
			),
			'site-editor.php'
		),
		''
	);

	add_submenu_page(
		'site-editor.php?path=%2Fwp_template_part%2Fall',
		__( 'Footer' ),
		__( 'Footer' ),
		'edit_posts',
		add_query_arg(
			array(
				'postType' => 'wp_template_part',
				'canvas'   => 'edit',
				'postId'   => get_stylesheet() . '%2F%2Ffooter',
			),
			'site-editor.php'
		),
		''
	);

	add_submenu_page(
		'site-editor.php?path=%2Fwp_template_part%2Fall',
		__( 'Global Buttons' ),
		__( 'Global Buttons' ),
		'edit_posts',
		add_query_arg(
			array(
				'postType' => 'wp_template_part',
				'canvas'   => 'edit',
				'postId'   => get_stylesheet() . '%2F%2Fglobal-buttons',
			),
			'site-editor.php'
		),
		''
	);

	add_submenu_page(
		'site-editor.php?path=%2Fwp_template_part%2Fall',
		__( 'Map Options' ),
		__( 'Map Options' ),
		'edit_posts',
		add_query_arg(
			array(
				'postType' => 'wp_template_part',
				'canvas'   => 'edit',
				'postId'   => get_stylesheet() . '%2F%2Fmap-options',
			),
			'site-editor.php'
		),
		''
	);

	add_submenu_page(
		'site-editor.php?path=%2Fwp_template_part%2Fall',
		__( 'Popup' ),
		__( 'Popup' ),
		'edit_posts',
		add_query_arg(
			array(
				'postType' => 'wp_template_part',
				'canvas'   => 'edit',
				'postId'   => get_stylesheet() . '%2F%2Fpopup',
			),
			'site-editor.php'
		),
		''
	);

	add_submenu_page(
		'site-editor.php?path=%2Fwp_template_part%2Fall',
		__( 'Social Links' ),
		__( 'Social Links' ),
		'edit_posts',
		add_query_arg(
			array(
				'postType' => 'wp_template_part',
				'canvas'   => 'edit',
				'postId'   => get_stylesheet() . '%2F%2Fsocial-links',
			),
			'site-editor.php'
		),
		''
	);
}
add_action( 'admin_menu', 'template_parts' );
