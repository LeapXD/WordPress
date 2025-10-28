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
 *
 * @phpcs:disable WordPress.WP.GlobalVariablesOverride.Prohibited
 */

if ( filter_input( INPUT_GET, 'context' ) ) {
	$templates = 'blocks-serverside/gravityforms/form.twig';
	$content   = '<h3>Gravity Form</h3>';
	if ( is_callable( $original_callback ) ) {
		$content = $original_callback( $attributes, $content, $block );
	}
} else {
	// Default is true, and attribute is omitted when true hence the ! isset.
	$show_title           = ! isset( $attributes['title'] ) || $attributes['title'];
	$show_description     = ! isset( $attributes['description'] ) || $attributes['description'];
	$query_string         = http_build_query(
		array(
			'form_id'            => $attributes['formId'],
			'display_form_title' => $show_title,
			'display_form_desc'  => $show_description,
		)
	);
	$iframe_url           = home_url( '/get-form/?' . $query_string );
	$context['iframeUrl'] = $iframe_url;

}
