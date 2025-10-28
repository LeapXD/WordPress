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

use XD\Types\XD_Slide;
use XD\Types\XD_Slider;

$slider                  = new XD_Slider();
$slider->options->finite = true;
$slider->class_name[]    = 'slider-theme-dark';
foreach ( $inner_blocks as $inner_block ) {
	$slide            = new XD_Slide();
	$slide->content   = $inner_block->render();
	$slider->slides[] = $slide;
}

$context['slider'] = $slider;
