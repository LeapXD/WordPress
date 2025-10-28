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

$project_types = $post->terms(
	array(
		'query' => array(
			'taxonomy' => 'project_type',
		),
	)
);

if ( ! is_wp_error( $project_types ) && ! empty( $project_types ) ) {
	$project_types = array_map(
		function( $term ) {
			return $term->name;
		},
		$project_types
	);
	$post->import(
		array(
			'project_types' => implode( ', ', $project_types ),
		)
	);

}

$locations = $post->terms(
	array(
		'query' => array(
			'taxonomy' => 'project_location',
		),
	)
);
if ( ! is_wp_error( $locations ) && ! empty( $locations ) ) {
	$post->import(
		array(
			'location' => $locations[0]->name,
		)
	);
}

// This style is needed in the hero.
wp_enqueue_style( 'block-styles/lists' );
