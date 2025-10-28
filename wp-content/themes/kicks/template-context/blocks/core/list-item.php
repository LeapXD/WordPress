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

if ( ! isset( $parent['block']->parsed_block['parent']['name'] ) ||
( isset( $parent['block']->parsed_block['parent']['name'] ) &&
	'core/list-item' !== $parent['block']->parsed_block['parent']['name'] ) ) {

	$parent_attributes = $parent['block']->attributes;

	if ( isset( $parent_attributes['className'] ) &&
	false !== strpos( $parent_attributes['className'], 'is-style-standard-list-with-checkmarks' ) ) {
		$context['checkMarkItem']  = true;
		$context['icon']           = get_icon( 'checkmark' );
		$block_props->class_name[] = 'has-checkmark';
		$content                   = str_replace( array( '<li>', '</li>' ), '', $block->inner_html );
		foreach ( $inner_blocks as $inner_block ) {
			$content .= $inner_block->render();
		}
	}

	if ( isset( $parent_attributes['className'] ) &&
	false !== strpos( $parent_attributes['className'], 'is-style-standard-list-with-links' ) ) {
		$inner_html = str_replace( array( '<li>', '</li>' ), '', $block->inner_html );
		$html       = new WP_HTML_Tag_Processor( $inner_html );
		$html->next_tag();
		$href   = $html->get_attribute( 'href' );
		$target = $html->get_attribute( 'target' );
		$rel    = $html->get_attribute( 'rel' );
		if ( $href ) {
			$context['icon']         = get_icon( 'link-arrow' );
			$content                 = $inner_html;
			$context['linkListItem'] = true;
			$context['href']         = $href;
			$context['target']       = ! empty( $target ) ? "target=\"{$target}\"" : '';
			$context['rel']          = ! empty( $rel ) ? "rel=\"{$rel}\"" : '';
			// In the template, we'll render the whole block as a link if it contains a link.
			$content = wp_strip_all_tags( $content );
		}
		foreach ( $inner_blocks as $inner_block ) {
			$content .= $inner_block->render();
		}
	}
}
