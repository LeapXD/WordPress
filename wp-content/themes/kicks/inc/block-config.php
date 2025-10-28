<?php
/**
 * Block configuration.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/filters/block-filters/#allowed_block_types_all
 * @package Kicks
 */

/**
 * Register meta fields for a given post type.
 *
 * @see https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/
 */
function xd_register_custom_meta_fields() {

	foreach ( array( 'custom-post-type' ) as $post_type ) {
		register_post_meta(
			$post_type,
			'page__color',
			array(
				'show_in_rest' => true,
				'single'       => true,
				'type'         => 'string',
			)
		);
	}
}


/**
 * Filter block categories.
 *
 * @param Array                   $allowed_block_types allowed block types.
 * @param WP_Block_Editor_Context $block_editor_context block editor context.
 */
function xd_filter_allowed_blocks_all( $allowed_block_types, $block_editor_context ) {

	$allowed_blocks = array(
		'none'        => array(
			'xd/example-block',

			'acf/amenities-map',

			'xd/accommodation-cards',
			'xd/accommodation-card',

			'xd/floorplan-accordion',
			'xd/floorplan-accordion-element',

			'xd/project-cards',
			'xd/project-card',

			'xd/promotion-cards',
			'xd/promotion-card',

			'xd/tab-section',
			'xd/tab-tabbed-menu',
			'xd/menu-item',

			'xd/timeline',

			// These are deprecated in favour of the card slider testimonial variant.
			// Sites that have them will still be able to edit them, but they won't be available to add.
			'xd/testimonial-slider',
			'xd/testimonial',

			// the form block is not allowed in older themes, this is enabled in kick-start theme.
			'xd/form',
		),
		'parts'       => array(
			'xd/social-link',
			'xd/social-links',
			'xd/alert-bar',
			'xd/popup',
			'xd/global-buttons',
			'xd/company-details',
			'xd/footer',
			'xd/map-options',
			'core/paragraph',
			'core/heading',
			'xd/buttons',
			'xd/button',
		),
		'widgets'     => array(
			'core/legacy-widget',
			'core/paragraph',
			'core/heading',
			'core/list',
			'core/list-item',
			'xd/button',
			'xd/image',
			'core/site-logo',
			'core/navigation',
			'core/navigation-link',
			'core/navigation-submenu',
			'gravityforms/form',
			'xd/two-tile',
		),
		'common'      => array(
			'core/paragraph',
			'core/heading',
			'core/list',
			'core/list-item',
			'core/quote',
			'xd/buttons',
			'xd/button',
			'core/separator',
			'core/block',
			'core/embed',
			'core-embed/youtube',
			'core-embed/vimeo',
			'xd/accordion',
			'xd/accordionelement',
			'gravityforms/form',
			'xd/image',
			'xd/two-tile',
			'xd/two-tile-inner',
			'xd/two-column-text',
		),
		'post'        => array(
			'xd/container',
		),
		'page'        => array(
			'xd/cta',
			'xd/card',
			'acf/instagram-feed',
			'acf/single-location-map',
			'xd/container',
			'xd/four-column',
			'xd/three-column',
			'xd/test-block',
			'xd/list',
			'xd/list-column',
			'xd/list-item',
			'xd/grid-layout',
			'xd/grid-cell',
			'xd/content',
		),
		'team_member' => array(
			'core/paragraph',
			'core/heading',
			'core/separator',
		),
		'floorplan'   => array(
			'xd/floorplan-details',
		),
		'flyout'      => array(
			'core/heading',
			'core/paragraph',
			'core/separator',
			'core/list',
			'xd/container',
			'xd/buttons',
			'xd/button',
			'gravityforms/form',
			'xd/form',
		),
		'modal'       => array(
			'core/heading',
			'core/paragraph',
			'core/separator',
			'core/list',
			'xd/container',
			'xd/buttons',
			'xd/button',
			'xd/form',
			'core/embed',
		),
	);

	// Add blocks in xd or acf namespace to common group if not already in any groups.
	$registered_blocks = array_filter(
		array_keys( WP_Block_Type_Registry::get_instance()->get_all_registered() ),
		function( $block_name ) {
			return strpos( $block_name, 'xd/' ) === 0 || strpos( $block_name, 'acf/' ) === 0;
		}
	);

	$all_allowed_blocks = array();
	foreach ( $allowed_blocks as $group ) {
		$all_allowed_blocks = array_merge( $all_allowed_blocks, $group );
	}

	foreach ( $registered_blocks as $registered_block ) {
		if ( ! in_array( $registered_block, $all_allowed_blocks, true ) ) {
			$allowed_blocks['common'][] = $registered_block;
		}
	}

	if ( 'core/edit-widgets' === $block_editor_context->name || 'core/customize-widgets' === $block_editor_context->name ) {
		return $allowed_blocks['widgets'];
	}

	if ( 'core/edit-site' === $block_editor_context->name ) {
		return $allowed_blocks['parts'];
	}

	if ( empty( $block_editor_context->post ) ) {
		return array();
	}

		$post = $block_editor_context->post;

	if ( 'post' === $post->post_type ) {
		$allowed_block_types = array_merge( $allowed_blocks['common'], $allowed_blocks['post'] );
	} elseif ( 'page' === $post->post_type ) {
		$allowed_block_types = array_merge( $allowed_blocks['common'], $allowed_blocks['page'] );
	} elseif ( 'team_member' === $post->post_type ) {
		$allowed_block_types = $allowed_blocks['team_member'];
	} elseif ( 'flyout' === $post->post_type ) {
		$allowed_block_types = $allowed_blocks['flyout'];
	} elseif ( 'modal' === $post->post_type ) {
		$allowed_block_types = $allowed_blocks['modal'];
	} elseif ( 'floorplan' === $post->post_type ) {
		$allowed_block_types = $allowed_blocks['floorplan'];

		// Update your custom post type and add additional conditions as needed.
	} else {

		// By default, common and page blocks will be allowed on any custom post type
		// To restrict blocks on custom post types, comment out this line and modify line above.
		$allowed_block_types = array_merge( $allowed_blocks['common'], $allowed_blocks['page'] );
	}
		return $allowed_block_types;
}

add_filter( 'allowed_block_types_all', 'xd_filter_allowed_blocks_all', 10, 2 );



/**
 * Remove the safe-svg block (so that it's scripts and styles are not enqueued).
 */
function xd_remove_safe_svg_block() {
	$registry = \WP_Block_Type_Registry::get_instance();
	if ( $registry->is_registered( 'safe-svg/svg-icon' ) ) {
		$registry->unregister( 'safe-svg/svg-icon' );
	}
};

add_action( 'init', 'xd_remove_safe_svg_block', 9999 );


