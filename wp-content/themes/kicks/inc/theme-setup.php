<?php
/**
 * Theme setup.
 *
 * @package Kicks
 */

// phpcs:disable Squiz.PHP.CommentedOutCode.Found
// phpcs:disable Squiz.Commenting.InlineComment.InvalidEndChar

/**
 * Configures the features supported by the theme.
 *
 * @return void
 */
function xd_setup_theme_supported_features() {

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'customize-selective-refresh-widgets' );

	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);

	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		)
	);

	add_theme_support( 'align-wide' );
	register_nav_menu( 'primary-menu', __( 'Primary' ) );

	$nav_menus = wp_get_nav_menus();
	if ( ! empty( $nav_menus ) ) {
		$nav_menus = wp_list_pluck( $nav_menus, 'slug' );
		if ( in_array( 'navbar-menu', $nav_menus, true ) ) {
			register_nav_menu( 'navbar-menu', __( 'Navbar' ) );
		}
	}

	register_nav_menu( 'primary-menu', __( 'Primary' ) );

	remove_theme_support( 'core-block-patterns' );
	remove_theme_support( 'widgets-block-editor' );
	remove_theme_support( 'block-templates' );

	add_post_type_support( 'page', 'excerpt' );
	add_theme_support( 'block-template-parts' );

}
add_action( 'after_setup_theme', 'xd_setup_theme_supported_features' );

/**
 * Theme customiser
 *
 * @param WP_Customize_Manager $wp_customize wp customiser.
 * @return void
 */
function xd_customize_nav_register( $wp_customize ) {

	/**
	 * Built in sections:
	 * title_tagline - Site Title & Tagline (and Site Icon in WP 4.3+)
	 * colors - Colors
	 * header_image - Header Image
	 * background_image - Background Image
	 * nav - Navigation
	 * static_front_page - Static Front Page
	 */

	$wp_customize->add_setting( 'xd_alt_logo' );
	$wp_customize->add_control(
		new WP_Customize_Cropped_Image_Control(
			$wp_customize,
			'xd_alt_logo',
			array(
				'label'         => __( 'Alternate Logo', 'xd' ),
				'section'       => 'title_tagline',
				'priority'      => 9,
				'height'        => 250,
				'width'         => 250,
				'flex_width'    => true,
				'flex_height'   => true,
				'button_labels' => array(
					'select'       => __( 'Select logo' ),
					'change'       => __( 'Change logo' ),
					'remove'       => __( 'Remove' ),
					'default'      => __( 'Default' ),
					'placeholder'  => __( 'No logo selected' ),
					'frame_title'  => __( 'Select logo' ),
					'frame_button' => __( 'Choose logo' ),
				),
			)
		)
	);
	// $wp_customize->remove_control( 'blogdescription' );
}

add_action( 'customize_register', 'xd_customize_nav_register' );

/**
 * Adds a developer capability to user ID 1.
 */
function xd_add_user_caps() {
	if ( is_user_logged_in() ) {
		$current_user = wp_get_current_user();
		if ( 1 === $current_user->ID ) {
			$current_user->allcaps['developer'] = true;
		}
	}
}

add_action( 'init', 'xd_add_user_caps' );
xd_add_user_caps();

/**
 * Theme options install
 */
function xd_setup() {
	if ( current_user_can( 'developer' ) ) {
		wp_safe_redirect( '/wp-admin/admin.php?page=xd-theme-options' );
		// No exit here. Additional work is done after 'after_switch_theme' hook.
	}
}
add_action( 'after_switch_theme', 'xd_setup' );

if ( current_user_can( 'developer' ) ) {
	Xd_Theme_Installer::init();
}

remove_action( 'shutdown', 'wp_ob_end_flush_all', 1 );

/**
 * Display errors in admin
 */
function xd_display_admin_errors() {
	if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		$error_get_last = error_get_last();
		if ( $error_get_last ) {
			$error_msg = sprintf( "%s\nIn %s on line %s", $error_get_last['message'], $error_get_last['file'], $error_get_last['line'] );
			if ( defined( 'WP_DEBUG_DISPLAY' ) && WP_DEBUG_DISPLAY ) {
				echo esc_html( $error_msg );
			}
			if ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
				//phpcs:ignore
				error_log( $error_msg );
			}
		}
	}
}

add_action( 'admin_head', 'xd_display_admin_errors' );

/**
 * Add custom page templates to thank you page, and contact page.
 */
function xd_page_templates() {

	add_settings_section( 'xd_contact_page', '', function(){}, 'reading' );

	$render_contact_field = function() {
		printf(
			// PHPCS:ignore,
			__(   'Apply contact page template to: %s' ),
			// PHPCS:ignore,
			wp_dropdown_pages(
				array(
					'name'              => 'xd_contact_page',
					'echo'              => 0,
					'show_option_none'  => __( '&mdash; Select &mdash;' ),
					'option_none_value' => '0',
					'selected'          => get_option( 'xd_contact_page' ),
				)
			)
		);
	};

	add_settings_field(
		'xd_contact_page',
		'Template For contact page',
		$render_contact_field,
		'reading',
		'xd_contact_page',
		array(
			'label_for' => 'xd_contact_page',
		)
	);

	register_setting( 'reading', 'xd_contact_page' );

	add_settings_section( 'xd_thank_you_page', '', function(){}, 'reading' );

	$render_thank_you_field = function() {
		printf(
			// PHPCS:ignore,
			__(   'Apply thank you page template to: %s' ),
			// PHPCS:ignore,
			wp_dropdown_pages(
				array(
					'name'              => 'xd_thank_you_page',
					'echo'              => 0,
					'show_option_none'  => __( '&mdash; Select &mdash;' ),
					'option_none_value' => '0',
					'selected'          => get_option( 'xd_thank_you_page' ),
				)
			)
		);
	};

	add_settings_field(
		'xd_thank_you_page',
		'Template For thank you page',
		$render_thank_you_field,
		'reading',
		'xd_thank_you_page',
		array(
			'label_for' => 'xd_thank_you_page',
		)
	);
	register_setting( 'reading', 'xd_thank_you_page' );

}

/**
 * Apply page template to thank you page / contact page.
 *
 * @param string $template the OG.
 */
function xd_apply_page_template( $template ) {
	$id = get_the_ID();
	if ( ! $id ) {
		return $template;
	}
	if ( (int) get_option( 'xd_thank_you_page' ) === $id ) {
		return 'template-thank-you.twig';
	}
	if ( (int) get_option( 'xd_contact_page' ) === $id ) {
		return 'template-contact.twig';
	}
	return $template;
}
add_action( 'template_include', 'xd_apply_page_template', 5 );

add_action( 'admin_init', 'xd_page_templates' );




/**
 * Filter the search query performed for a specific post type.
 *
 * @param array $query_args The search query.
 */
function xd_show_flyouts_in_rest_search( $query_args ) {
	$query_args['post_type'][] = 'flyout';
	$query_args['post_type'][] = 'modal';
	return $query_args;
}

add_filter( 'rest_post_search_query', 'xd_show_flyouts_in_rest_search' );

/**
 * Register tour flyout type.
 *
 * @param string  $permalink permalink.
 * @param WP_Post $post WP_Post.
 */
function xd_remove_flyout_permalink( $permalink, $post ) {
	if ( 'flyout' === $post->post_type ) {
		return '#' . $post->post_name;
	}
	if ( 'modal' === $post->post_type ) {
		return '#' . $post->post_name;
	}
	return $permalink;
}

add_filter( 'post_type_link', 'xd_remove_flyout_permalink', 10, 2 );


/**
 * Generate a unique slug for flyouts and modals on save.
 *
 * @param int $post_id The post ID being saved.
 */
function xd_unique_flyout_slug( $post_id ) {

	remove_action( 'save_post', 'xd_unique_flyout_slug' );

	$post = get_post( $post_id );
	if ( ! $post || ( 'flyout' !== $post->post_type && 'modal' !== $post->post_type ) ) {
		return;
	}

	$slug = wp_unique_post_slug(
		sanitize_title( $post->post_title ),
		$post_id,
		$post->post_status,
		$post->post_type,
		$post->post_parent
	);

	// Update the post slug.
	wp_update_post(
		array(
			'ID'        => $post_id,
			'post_name' => $slug,
		)
	);

};


add_action( 'save_post', 'xd_unique_flyout_slug' );
