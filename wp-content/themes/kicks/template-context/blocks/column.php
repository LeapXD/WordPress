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

use function Leap\Editor\Block_Render\xd_get_block_variation;

// when the last element of a column is a heading, remove the inner class and add a heading class.
// This prevents the heading bottom margin from being removed
// and allows the row gap to be removed with the heading class.

if ( xd_get_block_variation( $parent['block'] )->name === 'xd/columns' && count( $parent['block']->inner_blocks ) > 1 ) {

	$column_blocks = array();
	foreach ( $inner_blocks as $inner_block ) {
		if ( 'core/paragraph' === $inner_block->name ) {
			if ( empty( wp_strip_all_tags( $inner_block->inner_html ) ) ) {
				continue;
			}
		}
		$column_blocks[] = $inner_block;
	}

	$column_blocks_count = count( $column_blocks );
	if ( $column_blocks_count && 'core/heading' === $column_blocks[ $column_blocks_count - 1 ]->name ) {
			$wrap->inner_blocks->class_name['xd-column__inner'] = 'xd-column__heading';
	}
}
