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

use Timber\Timber;
use XD\Types\XD_Background_Image;
use XD\Types\XD_Card;
use XD\Types\XD_Flyout;
use XD\Types\XD_Image;

use function Leap\Editor\Block_Render\xd_get_block_variation;

$post = Timber::get_post( $attributes['postId'] );
if ( ! $post->id ) {
	return;
}
$card_image                                 = ! empty( $post->featured_image_card['id'] )
? $post->featured_image_card
: $post->thumbnail();
$card                                       = new XD_Card();
$card->is_linked                            = true;
$card->link->url                            = $post->link;
$card->image->primary                       = $card_image;
$card->image->options                       = array(
	'breakpoints' => array(
		'md' => '50vw',
		'lg' => '33vw',
	),
);
$card->image->class_name['xd-image--cover'] = 'xd-image--cover';

if ( 'xd/page-card' === xd_get_block_variation( $block )->name ) {
	$card->image = new XD_Background_Image( $card->image );
}

if ( in_array( 'xd-thumbnail-card', $block_props->class_name, true ) ) {
	$card->image->options = array(
		'breakpoints' => array( 'xs' => '130px' ),
	);
	$flyout               = new XD_Flyout();
	$flyout->id           = sanitize_title( $post->title );

	$flyout->options->cls_page[] = 'header-theme-transparent';
	$flyout->dataset['link']     = $post->link;
	$flyout->dataset['slug']     = $post->slug;
	$context['flyout']           = $flyout;

	$flyout_image                                = new XD_Image();
	$flyout_image->options                       = array( 'breakpoints' => array( 'xs' => '500px' ) );
	$flyout_image->primary                       = $post->thumbnail();
	$flyout_image->class_name['xd-image--cover'] = 'xd-image--cover';

	$post->import( array( 'flyout_image' => $flyout_image ), true );
} else {
	unset( $templates['blocks/team-member-card.twig'] );
}

$card->class_name = $block_props->class_name;
$post->import( $card, true );
$context['post'] = $post;
