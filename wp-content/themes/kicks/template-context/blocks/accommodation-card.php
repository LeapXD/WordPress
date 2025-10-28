<?php
/**
 * Block context
 *
 * @package Leap Destination
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
 * $post and $card are already defined in theme/template-context/blocks/post-card.php
 * @var XD\Types\XD_Post $post
 * @var XD\Types\XD_Card $card
 *
 * @phpcs:disable WordPress.WP.GlobalVariablesOverride.Prohibited
 */

use XD\Types\XD_Image_Options;

require get_template_directory() . '/template-context/blocks/post-card.php';

$image_options              = new XD_Image_Options();
$image_options->breakpoints = array( 'xl' => '50vw' );
$card->image->options       = $image_options;

$post->import( $card, true );
