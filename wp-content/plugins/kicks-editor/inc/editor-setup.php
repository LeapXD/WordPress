<?php
/**
 * Editor setup.
 *
 * @package Leap Editor
 */

namespace Leap\Editor\Editor_Setup;

/**
 * Get the default block category.
 *
 * @return array block_editor_settings.
 */
function xd_get_block_editor_settings() {

	$post = get_post();

	$settings = array(
		'theme_version'      => wp_get_theme( get_template() )->get( 'Version' ),
		'stylesheet_version' => wp_get_theme( get_stylesheet() )->get( 'Version' ),
		'theme_slug'         => get_stylesheet(),
		'theme_dir'          => get_template_directory_uri(),
		'current_screen'     => \get_current_screen(),
		'post'               => $post,
		'default_category'   => apply_filters( 'xd_block_default_category', get_stylesheet() ),
		'post_state'         => false,
		'page_template'      => get_page_template_slug(),
	);

	if ( $post ) {

		if ( (int) get_option( 'page_for_posts' ) === $post->ID ) {
			$settings['post_state'] = 'page_for_posts';
		}
		if ( (int) get_option( 'page_on_front' ) === $post->ID ) {
			$settings['post_state'] = 'page_on_front';
		}

		$settings['post_state'] = apply_filters( 'xd_post_state', $settings['post_state'] );
	}

	return apply_filters( 'xd_editor_settings', $settings );
}
