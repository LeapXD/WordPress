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
 * @phpcs:disable WordPress.WP.GlobalVariablesOverride.Prohibited
 */

use function Leap\Editor\Block_Render\xd_render_block;

$content = '';

foreach ( $inner_blocks as $inner_block ) {
	$content .= xd_render_block(
		$inner_block,
		array(),
		array( 'options' => array( 'use_image_size' => true ) )
	);
}
