<?php
/**
 * ACF render functions.
 *
 * @package Kicks
 */

/**
 * Render featued pages.
 *
 * @param Array $props block properties.
 */
function render_featured_pages( $props ) {
	global $post;

	$posts  = get_field( 'featured_pages' );
	$slider = get_field( 'slider' );

	if ( $slider ) {
		include get_template_directory() . '/template-parts/acf-blocks/featured-pages-slider.php';
	} else {
		include get_template_directory() . '/template-parts/acf-blocks/featured-pages.php';
	}

	// $post got overwritten in the template so it needs to be restored
	wp_reset_postdata();
}

/**
 * Render instagram feed.
 */
function render_instagram_feed() {
	$display = 'grid';
	$number  = 4;
	$title   = get_field( 'title' );
	if ( ! is_admin() ) {
		echo '<div id="instagram_feed_container" style="display: none;">';
		echo do_shortcode( "[instagram-feed customtemplates=\"true\" num=\"$number\" display=\"$display\"  title=\"$title\" ]" );
		echo '</div>';
	} else {
		echo '<div style="text-align:center;background:#eee;padding:1rem"><h4>Instagram Feed</h4></div>';
	}

}

/**
 * Render latest posts.
 *
 * @param Array $props block properties.
 */
function render_latest_posts( $props ) {
	global $post;

	// the block fields data.
	$data = wp_parse_args(
		get_fields(),
		array(
			'posts_per_page' => 3,
			'posts'          => array(),
			'display'        => 'recent',
		)
	);

	$posts_per_page = $data['posts_per_page'];
	$posts          = array();

	switch ( $data['display'] ) {
		case 'sticky':
			$posts = get_option( 'sticky_posts' );
			$posts = (array) $posts;
			$posts = array_slice( $posts, 0, (int) $posts_per_page );
			break;
		case 'recent':
			$posts = get_posts(
				array(
					'posts_per_page' => (int) $posts_per_page,
					'post_type'      => array( 'post' ),
				)
			);
			break;
		case 'selected':
			// only 1 - 4 posts can be selected in the editor
			// so no need to worry about posts_per_page.
			$posts = $data['posts'];
			break;
		default:
			$posts = array();
	}

	// using include instead of get_template_part()
	// so that the above variables are available in the template.
	include get_template_directory() . '/template-parts/acf-blocks/latest-posts.php';

	// $post got overwritten in the template so it needs to be restored
	wp_reset_postdata();
}

/**
 * Render team members.
 *
 * @param Array $props block properties.
 */
function render_team( $props ) {

	global $post;

	$posts = get_field( 'team_members' );

	include get_template_directory() . '/template-parts/acf-blocks/team-members.php';

	// $post got overwritten in the template so it needs to be restored
	wp_reset_postdata();

}

add_action(
	'wp_footer',
	function() {
		global $flyouts;
		if ( ! empty( $flyouts ) ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo implode( '', $flyouts );
		}
	}
);

/**
 * Render testimonal slider.
 *
 * @param Array $props block properties.
 */
function render_testimonial_slider( $props ) {

	global $post;
	$posts = get_field( 'testimonials' );
	// convert name ("acf/testimonial") into path friendly slug ("testimonial").
	$slug = str_replace( 'acf/', '', $props['name'] );

	include get_template_directory() . '/template-parts/acf-blocks/testimonials.php';

	// $post got overwritten in the template so it needs to be restored
	wp_reset_postdata();

}
