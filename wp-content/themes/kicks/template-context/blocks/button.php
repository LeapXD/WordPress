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

use XD\Types\XD_Button;
use function Leap\Editor\Block_Render\xd_attribute;

$button                 = new XD_Button( $attributes );
$button->text           = xd_attribute( 'text' );
$button->link->url      = xd_attribute( 'url' );
$button->link->rel      = xd_attribute( 'rel' );
$button->link->target   = xd_attribute( 'target' );
$button->entity_type    = xd_attribute( 'kind' );
$button->entity_subtype = xd_attribute( 'type' );
$button->css            = $block_props->css;


if ( isset( $block_props->class_name['xd-button--download'] ) ) {
	$button->icon = get_icon( 'download' );
}

if ( ! empty( $button->entity_subtype && ! empty( $button->entity_id ) ) ) {
	$button->link->url = get_permalink( $button->entity_id );
}

if ( function_exists( 'xd_theme_version_compare' ) && xd_theme_version_compare( '<', '1.1.01', false ) ) {
	if ( 'post-type' === $button->entity_type &&
			( 'flyout' === $button->entity_subtype ||
				'modal' === $button->entity_subtype
			) &&
			$button->link &&
			'#' === $button->link->url
			) {

		$post              = get_post( xd_attribute( 'entityId' ) );
		$button->link->url = '#' . esc_attr( $post->post_name );
		$context['post']   = $post;
	}
}

$context['button'] = $button;
