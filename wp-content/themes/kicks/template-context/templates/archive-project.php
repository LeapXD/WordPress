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

/**
 * Get the available terms for each taxonomy
 */
$project_type_options = get_terms_by_post_type( 'project_type', 'project' );
// ***************************************************** */

/**
 * Get the selected filters.
 */
$project_type = get_query_var( 'project-type' );
$page         = get_query_var( 'paged' );
if ( ! $page ) {
	$page = 1;
}
// ***************************************************** */


/**
 * Set the selected option in the dropdowns as selected.
 */
foreach ( $project_type_options as $project_type_option ) {
	$project_type_option->selected = selected( $project_type === $project_type_option->slug, true, false );
}
// ***************************************************** */


/**
 * Import the available options and selected options into post data.
 */
$post->import(
	array(
		'project_type_options' => $project_type_options,
	),
	true
);


/**
 * Setup the query.
 */
$meta_query = array();
$tax_query  = array();
$query_args = array( 'post_type' => 'project' );

if ( $page ) {
	$query_args['paged'] = $page;
}

if ( ! empty( $project_type ) ) {
	$tax_query[] = array(
		'taxonomy' => 'project_type',
		'field'    => 'slug',
		'terms'    => $project_type,
	);
}


if ( ! empty( $tax_query ) ) {
	$query_args['tax_query'] = $tax_query;
}
if ( ! empty( $meta_query ) ) {
	$query_args['meta_query'] = $meta_query;
}
// ***************************************************** */


/**
 * Run the query.
 */
$query    = new WP_Query( $query_args );
$projects = Timber::get_posts( $query->get_posts() );
// ***************************************************** */


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

wp_enqueue_style( 'block-styles/post-cards' );

$cards = array();

/**
 * Convert the query results into rendered cards.
 */
foreach ( $projects as $project ) {
	// Avoid overwriting $post in loop, as this is where the page data is stored.
	$cards[] = xd_render_block(
		'xd/project-card',
		array(
			'postId'   => $project->id,
			'postType' => 'project',
		)
	);
}
// ***************************************************** */


// Send the cards into the template context.
$context['cards'] = $cards;


// This is not strictly necessary, as a page named "projects" will already use the page-projects.twig template.
// However, manually setting the template prevents unexpected results if the user changes the page slug.
$templates = array( 'page-projects.twig' );
