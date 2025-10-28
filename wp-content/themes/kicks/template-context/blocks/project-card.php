<?php
/**
 * Block context
 *
 * @package Kicks
 *
 * @var array $attributes
 * @var WP_Block $block
 * @var \XD\Types\XD_Template_Props $block_props
 * @var string $content
 * @var array $context
 * @var array<WP_Block> $inner_blocks
 * @var array $parent
 * @var array $templates
 * @var string $variation
 * @var \XD\Types\XD_Block_Wrap $wrap
 * @var array $xd_context
 * @var XD\Types\XD_Post $post XD_post instance.
 * @phpcs:disable WordPress.WP.GlobalVariablesOverride.Prohibited
 */

require get_template_directory() . '/template-context/blocks/post-card.php';

$project_types = $post->terms(
	array(
		'query' => array(
			'taxonomy' => 'project_type',
		),
	)
);

if ( ! empty( $project_types ) ) {
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
