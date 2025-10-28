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

use XD\Types\XD_Image;
use XD\Types\XD_Image_Options;

use function Leap\Editor\Block_Render\xd_attribute;
use function Leap\Editor\Block_Render\xd_context;

$options = new XD_Image_Options( xd_context( 'options', $block ) );

$image                   = new XD_Image();
$image->options          = $options;
$image->id               = xd_attribute( 'id' );
$image->primary          = xd_attribute( 'imageDesktop' );
$image->secondary        = xd_attribute( 'imageMobile' );
$image->link->rel        = xd_attribute( 'rel' );
$image->link->target     = xd_attribute( 'target' );
$image->link->url        = xd_attribute( 'url' );
$image->dataset          = xd_context( 'dataset', $block );
$image->content          = xd_context( 'content', $block );
$image->css_vars         = $block_props->css_vars;
$image->video->modal->id = $image->id;
$image->video->sm        = xd_attribute( 'videoMobile' );
$image->video->md        = xd_attribute( 'videoTablet' );
$image->video->lg        = xd_attribute( 'videoDesktop' );
$image->video->modal->src->import( xd_attribute( 'videoModal' ) );
$image->video->modal->options->cls_page[] = 'header-theme-transparent';
$image->video->modal->options->cls_page[] = 'video-open';
$image->video->modal->type                = 'full';
$image->video->modal->class_name[]        = 'xd-modal--video';
$image->show_caption                      = xd_attribute( 'showCaption' );

if ( ! empty( $image->primary['id'] ) ) {
	$image->caption = get_the_excerpt( $image->primary['id'] );
}
if ( $image->video->modal->src->is_empty() ) {
	$image->video->modal = null;
} else {
	$image->video->show_modal_button = true;
}

$context['image'] = $image;
