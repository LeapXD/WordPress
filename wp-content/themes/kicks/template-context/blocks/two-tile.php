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

use XD\Types\XD_Image;
use XD\Types\XD_Image_Options;
use XD\Types\XD_Modal;

use function Leap\Editor\Block_Render\xd_attribute;

$image                                    = new XD_Image();
$image_options                            = new XD_Image_Options();
$image_options->breakpoints               = array( 'lg' => '57vw' );
$image->options                           = $image_options;
$image->primary                           = xd_attribute( 'imageDesktop' );
$image->secondary                         = xd_attribute( 'imageMobile' );
$image->video->lg                         = xd_attribute( 'videoDesktop' );
$image->video->md                         = xd_attribute( 'videoTablet' );
$image->video->sm                         = xd_attribute( 'videoMobile' );
$image->video->modal                      = new XD_Modal();
$image->video->modal->src                 = xd_attribute( 'videoModal' );
$image->video->modal->id                  = $block_props->id;
$image->video->modal->type                = 'full';
$image->video->modal->class_name[]        = 'xd-modal--video';
$image->video->modal->options->cls_page[] = 'header-theme-transparent';
$image->video->show_modal_button          = true;
$image->caption                           = isset( $image->primary['id'] ) ? get_the_excerpt( $image->primary['id'] ) : '';
$image->show_caption                      = xd_attribute( 'showCaption' );
$image->class_name['xd-image--cover']     = 'xd-image--cover';

$context['image'] = $image;
