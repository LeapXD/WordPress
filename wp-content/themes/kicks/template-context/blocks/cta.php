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

use XD\Types\XD_Background_Image;
use XD\Types\XD_Video;

$block_props->background_image = new XD_Background_Image(
	array(
		'primary'   => $attributes['backgroundImageDesktop'],
		'secondary' => $attributes['backgroundImageMobile'],
		'video'     => new XD_Video(
			array(
				'lg' => $attributes['videoDesktop'],
				'md' => $attributes['videoTablet'],
				'sm' => $attributes['videoMobile'],
			)
		),
	)
);
