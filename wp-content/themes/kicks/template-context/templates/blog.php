<?php
/**
 * Twig template context.
 *
 * @package Kicks
 *
 * @var \XD\Types\XD_Template_Props $template_props
 * @var \XD\Types\XD_Post $post
 * @var \Timber\PostQuery<\XD\Types\XD_Post> $posts
 * @var \Timber\Theme $theme
 * @var \Timber\Site $site
 * @var \Timber\User $user
 * @var \Timber\Request $request
 * @phpcs:disable WordPress.WP.GlobalVariablesOverride.Prohibited
 */

use function Leap\Editor\Block_Render\xd_render_block;

	$categories       = get_categories();
	$category_options = array(
		array(
			'is_active' => is_home() ? 'active' : '',
			'url'       => get_post_type_archive_link( 'post' ),
			'selected'  => selected( is_home(), 'all', false ),
			'name'      => 'All',
			'slug'      => '/blog/',
		),
	);
	foreach ( $categories as $category ) {
		if ( 'uncategorized' === $category->slug ) {
			continue;
		}
		$category_options[] = array(
			'is_active' => is_category( $category->slug ) ? 'active' : '',
			'url'       => get_term_link( $category ),
			'selected'  => selected( is_category( $category->slug ), true, false ),
			'name'      => $category->name,
			'slug'      => $category->slug,
		);
	}
	$post->import( array( 'category_options' => $category_options ) );

	wp_enqueue_style( 'block-styles/post-cards' );

	$cards = array();

	foreach ( $posts as $post_card ) {
		$cards[] = xd_render_block( 'xd/post-card', array( 'postId' => $post_card->id ) );
	}

	$context['cards'] = $cards;
