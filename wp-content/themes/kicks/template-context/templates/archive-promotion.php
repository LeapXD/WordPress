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

use function Leap\Editor\Block_Render\xd_render_block;
global $wpdb;

$page = get_query_var( 'paged' );
if ( ! $page ) {
	$page = 1;
}

/**
 * Setup the query.
 */
$meta_query = array();
$tax_query  = array();
$query_args = array( 'post_type' => 'promotion' );

if ( $page ) {
	$query_args['paged'] = $page;
}

if ( ! empty( $meta_query ) ) {
	$query_args['meta_query'] = $meta_query;
}
// ***************************************************** */


/**
 * Run the query.
 */
$query      = new WP_Query( $query_args );
$promotions = Timber::get_posts( $query->get_posts() );
// ***************************************************** */

wp_enqueue_style( 'block-styles/post-cards' );
wp_enqueue_style( 'block-styles/promotion-cards' );

/**
 * Get the pagination links.
 */
$context['pagination'] = paginate_links(
	array(
		'total'     => $query->max_num_pages,
		'current'   => $page,
		'mid_size'  => 1,
		'prev_text' => 'Previous',
		'next_text' => 'Next',
	),
);
// ***************************************************** */

$cards = array();

/**
 * Convert the query results into rendered cards.
 */
foreach ( $promotions as $promotion ) {
	// Avoid overwriting $post in loop, as this is where the page data is stored.
	$cards[] = xd_render_block(
		'xd/promotion-card',
		array(
			'postId'   => $promotion->id,
			'postType' => 'promotion',
		)
	);
}
// ***************************************************** */


// Send the cards into the template context.
$context['posts'] = $promotions;


// This is not strictly necessary, as a page named "promotions" will already use the page-promotions.twig template.
// However, manually setting the template prevents unexpected results if the user changes the page slug.
$templates = array( 'page-promotions.twig' );
