<?php
/**
 * Twig template context.
 *
 * @package Kicks
 *
 * @var \XD\Types\XD_Template_Props $template_props
 * @var \XD\Types\XD_Post $post
 * @var \Timber\PostQuery<\XD\Types\XD_Post> $posts
 * @var \Timber\Theme $theme
 * @var \Timber\Site $site
 * @var \Timber\User $user
 * @var \Timber\Request $request
 * @phpcs:disable WordPress.WP.GlobalVariablesOverride.Prohibited
 */

use Timber\Timber;
use XD\Types\XD_Link;
use XD\Types\XD_Modal;

use function Leap\Editor\Block_Render\xd_render_block;


$inner_blocks = array(
	// An inner_block can be a pre-rendered block string, or a block definition.
	Timber::compile(
		'partials/hero/hero-split-content.twig',
		array(
			'post'          => $post,
			'templateProps' => $template_props,
		)
	),
);

if ( ! empty( $post->hero_button ) ) {

	$hero_button = new XD_Link( $post->hero_button );

	if ( ! $hero_button->is_empty() ) {
		// $button_attributes = array_merge( $post->hero_button, array( 'text' => $post->hero_button['title'] ) );
		$inner_blocks[] = array(
			'name'       => 'xd/button',
			'attributes' => $button_attributes,
		);
	}
}

if ( isset( $post->hero_image['video']['modal']['src'] ) ) {
	$modal                              = new XD_Modal();
	$modal->src                         = $post->hero_image['video']['modal']['src'];
	$modal->id                          = 'hero';
	$modal->options->cls_page[]         = 'header-theme-transparent';
	$modal->options->cls_page[]         = 'video-open';
	$modal->type                        = 'full';
	$modal->class_name[]                = 'xd-modal--video';
	$post->hero_image['video']['modal'] = $modal->get_data();
}

$post->hero_video = (array) $post->hero_video;
// We use a two tile block to render the hero content.
// the inner blocks are assembled above.
$context['heroContent'] = xd_render_block(
	array(
		'name'         => 'xd/two-tile',
		'attributes'   => array(
			'align'         => 'right',
			'paddingTop'    => '',
			'paddingBottom' => '',
			'id'            => 'hero',
			'imageDesktop'  => (array) $post->featured_image,
			'imageMobile'   => (array) $post->featured_image_mobile,
			'videoMobile'   => ! empty( $post->hero_video['sm'] ) ? $post->hero_video['sm'] : null,
			'videoTablet'   => ! empty( $post->hero_video['md'] ) ? $post->hero_video['md'] : null,
			'videoDesktop'  => ! empty( $post->hero_video['lg'] ) ? $post->hero_video['lg'] : null,
			'videoModal'    => ! empty( $post->hero_video['modal_src'] ) ? $post->hero_video['modal_src'] : null,
		),
		'inner_blocks' => $inner_blocks,
	)
);

$templates = array( 'template-split-hero.twig' );
