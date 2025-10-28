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

if ( ! $attributes['formId'] ) {
		return;
}
if ( $attributes['lazyLoad'] ) {
	$context['iframeUrl'] = '';
	$query_string         = http_build_query(
		array(
			'form_id'            => $attributes['formId'],
			'display_form_title' => $attributes['showTitle'],
			'display_form_desc'  => $attributes['showDescription'],
		)
	);
	$iframe_url           = home_url( '/get-form/?' . $query_string );
	$context['iframeUrl'] = $iframe_url;
} else {
	if ( ! function_exists( 'gravity_form' ) ) {
		return;
	}
	$context['content'] = \gravity_form( $attributes['formId'], $attributes['showTitle'], $attributes['showDescription'], false, null, false, 0, false );
}
