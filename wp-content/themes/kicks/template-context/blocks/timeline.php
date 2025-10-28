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
 * @var WP_Block_List<WP_Block> $inner_blocks
 * @var array $parent
 * @var array $templates
 * @var string $variation
 * @var \XD\Types\XD_Block_Wrap $wrap
 * @var array $xd_context
 * @phpcs:disable WordPress.WP.GlobalVariablesOverride.Prohibited
 */

use function Leap\Editor\Block_Render\xd_attribute;

if ( ! empty( $inner_blocks ) ) {
		$item_count = count( $inner_blocks );
		$first_item = $inner_blocks[0];
		$last_item  = $inner_blocks[ $item_count - 1 ];

		$title_color_theme     = xd_attribute( 'colorTheme', $first_item );
		$padding_top           = xd_attribute( 'paddingTop', $first_item );
		$padding_bottom        = xd_attribute( 'paddingBottom', $last_item );
		$last_item_color_theme = xd_attribute( 'colorTheme', $last_item );

		$block_props->class_name['padding-bottom']                     = 'last-child-' . $padding_bottom;
		$wrap->post_inner_blocks->class_name['padding-bottom']         = $padding_bottom;
		$wrap->post_inner_blocks->class_name[ $last_item_color_theme ] = $last_item_color_theme;

	if ( xd_attribute( 'title' ) ) {
		$wrap->pre_inner_blocks->class_name[ $title_color_theme ] = $title_color_theme;
		$wrap->pre_inner_blocks->class_name['padding-top']        = $padding_top;
		$block_props->class_name['has-block-title']               = 'has-block-title';
	}
}
