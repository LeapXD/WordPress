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

$block_props->css_vars = array(
	'--grid-area'   => implode(
		' / ',
		array(
			$attributes['rowStart']['mobile'],
			$attributes['colStart']['mobile'],
			'span ' . $attributes['rowSpan']['mobile'],
			'span ' . $attributes['colSpan']['mobile'],
		)
	),
	'--grid-area-m' => implode(
		' / ',
		array(
			$attributes['rowStart']['tablet'],
			$attributes['colStart']['tablet'],
			'span ' . $attributes['rowSpan']['tablet'],
			'span ' . $attributes['colSpan']['tablet'],
		)
	),
	'--grid-area-l' => implode(
		' / ',
		array(
			$attributes['rowStart']['desktop'],
			$attributes['colStart']['desktop'],
			'span ' . $attributes['rowSpan']['desktop'],
			'span ' . $attributes['colSpan']['desktop'],
		)
	),
);
