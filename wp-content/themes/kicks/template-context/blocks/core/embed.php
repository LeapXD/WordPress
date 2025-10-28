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

use XD\Types\XD_Post;


// The current post is post that contains this block.
// Not necessarily the the current post/page being viewed. Eg. when this block is used in a modal or flyout.
$post = XD_Post::get_current();

if ( ! empty( $post->post_type ) && in_array( $post->post_type, array( 'modal', 'flyout' ), true ) ) {
	$context['post'] = $post;
	if ( preg_match( '/youtube|vimeo/', $content ) ) {

		$iframe = new WP_HTML_Tag_Processor( $content );
		if ( $iframe->next_tag( 'iframe' ) ) {
			$iframe->set_attribute( 'data-ui', 'video' );
			$iframe->set_attribute( 'data-video', wp_json_encode( array( 'autoplay' => true ) ) );
		}
		$content = $iframe->get_updated_html();
	}
}
