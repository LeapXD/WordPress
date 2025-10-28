<?php
/**
 * Block configuration.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/filters/block-filters/#allowed_block_types_all
 * @package Kicks
 */

/**
 * Filter block categories.
 *
 * @param Array                   $allowed_block_types allowed block types.
 * @param WP_Block_Editor_Context $block_editor_context block editor context.
 */
function xdc_filter_allowed_blocks_all( $allowed_block_types, $block_editor_context ) {

	if ( ! is_array( $allowed_block_types ) ) {
		$allowed_block_types = array();
	}

	$allowed_blocks = array(
		'none'                  => array(),
		'parts'                 => array(),
		'widgets'               => array(),
		'common'                => array(),
		'post'                  => array(),
		'page'                  => array(

			//phpcs:disable

			// 'acf/amenities-map',

			// 'xd/accommodation-cards',
			// 'xd/accommodation-card',

			// 'xd/promotion-cards',
			// 'xd/promotion-card',

			// 'xd/floorplan-accordion',
			// 'xd/floorplan-accordion-element',

			// 'xd/project-cards',
			// 'xd/project-card',

			// 'xd/tab-section',
			// 'xd/tab-tabbed-menu',
			// 'xd/menu-item',

			// 'xd/timeline',

			'xd/form',

			//phpcs:enable
		),
		'team_member'           => array(),
		'flyout'                => array(),
		'your_custom_post_type' => array(),
	);

	if ( 'core/edit-widgets' === $block_editor_context->name || 'core/customize-widgets' === $block_editor_context->name ) {
		return array_merge( $allowed_block_types, $allowed_blocks['widgets'] );
	}

	if ( 'core/edit-site' === $block_editor_context->name ) {
		return array_merge( $allowed_block_types, $allowed_blocks['parts'] );
	} else {
		foreach ( $allowed_blocks['parts'] as $block ) {
			unset( $allowed_block_types[ array_search( $block, $allowed_block_types, true ) ] );
		}
	}

	if ( empty( $block_editor_context->post ) ) {
		return array();
	}

	$post = $block_editor_context->post;

	$additional_allowed_blocks = array();

	if ( 'post' === $post->post_type ) {
		$additional_allowed_blocks = array_merge( $allowed_blocks['common'], $allowed_blocks['post'] );
	} elseif ( 'page' === $post->post_type ) {
		$additional_allowed_blocks = array_merge( $allowed_blocks['common'], $allowed_blocks['page'] );

	} elseif ( 'team_member' === $post->post_type ) {
		$additional_allowed_blocks = $allowed_blocks['team_member'];

	} elseif ( 'flyout' === $post->post_type ) {
		$additional_allowed_blocks = $allowed_blocks['flyout'];

	} elseif ( 'your_custom_post_type' === $post->post_type ) {
		$additional_allowed_blocks = array_merge( $allowed_blocks['common'], $allowed_blocks['your-custom-post-type'] );
	} else {

		// By default, common and page blocks will be allowed on any custom post type
		// To restrict blocks on custom post types, comment out this line and modify line above.
		$additional_allowed_blocks = array_merge( $allowed_blocks['common'], $allowed_blocks['page'] );
	}

	$allowed_block_types = array_merge( $allowed_block_types, $additional_allowed_blocks );
	$allowed_block_types = array_diff( $allowed_block_types, $allowed_blocks['none'] );
	// ensure sequential keys.
	$allowed_block_types = array_values( $allowed_block_types );
	return $allowed_block_types;
}

add_filter( 'allowed_block_types_all', 'xdc_filter_allowed_blocks_all', 20, 2 );

