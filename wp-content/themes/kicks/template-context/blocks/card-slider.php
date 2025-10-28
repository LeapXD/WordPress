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

use XD\Types\XD_Slider;

use function Leap\Editor\Block_Render\xd_get_block_variation;

$slider = new XD_Slider();
foreach ( $inner_blocks as $inner_block ) {
	$slider->slides[] = $inner_block->render();
}
$context['slider'] = $slider;

if ( 'xd/testimonials-slider' === xd_get_block_variation( $block )->name ) {
	$slider->options->center = true;
}
