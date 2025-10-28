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

$options                 = isset( $xd_context['options'] ) ? (array) xd_context( 'options', $block ) : array();
$use_image_size          = isset( $options['use_image_size'] ) ? $options['use_image_size'] : true;
$options                 = new XD_Image_Options( $options );
$options->use_image_size = $use_image_size;

$image               = new XD_Image();
$image->id           = xd_attribute( 'id' );
$image->primary      = xd_attribute( 'imageDesktop' );
$image->link->rel    = xd_attribute( 'rel' );
$image->link->target = xd_attribute( 'target' );
$image->link->url    = xd_attribute( 'url' );
$image->options      = $options;
$image->dataset      = xd_context( 'dataset', $block );
$image->css          = $block_props->css;

$context['image'] = $image;
