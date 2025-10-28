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
use XD\Types\XD_Flyout;
use XD\Types\XD_Modal;
use XD\Types\XD_Type;
$template_props->user_popups = array();

if ( ! empty( $post->modal ) ) {
	$popups[] = $post->modal;
}

foreach ( wp_get_nav_menus() as $nav_menu ) {
	$nav_menu_items = wp_get_nav_menu_items( $nav_menu );
	foreach ( $nav_menu_items as $nav_menu_item ) {
		if ( 'post_type' === $nav_menu_item->type ) {
			if ( 'flyout' === $nav_menu_item->object ) {
				$flyouts[] = $nav_menu_item->object_id;
			} elseif ( 'modal' === $nav_menu_item->object ) {
				$popups[] = $nav_menu_item->object_id;
			}
		}
	}
}

$ui_link_results = array( 1 => array() );

if ( ! empty( $post->post_content ) ) {
	preg_match_all( '/href="#?([^"]+)"/', $post->content(), $ui_link_results );
}

$ui_links = array();

foreach ( $ui_link_results[1] as $ui_link ) {
	$parts      = explode( '/', trim( $ui_link, '/' ) );
	$ui_links[] = end( $parts );
}

$popup_posts = get_posts(
	array(
		'post_type'      => apply_filters( 'xd_modal_post_types', array( 'modal' ) ),
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'post_name__in'  => $ui_links,
		'fields'         => 'ids',
	)
);

$popups = array_merge( $popups, $popup_posts );
$popups = array_values( array_unique( $popups ) );

foreach ( $popups as $popup ) {
	$popup_post = Timber::get_post( $popup );

	if ( 'publish' !== $popup_post->status ) {
		continue;
	}
	$user_popup                      = new XD_Modal();
	$user_popup->id                  = $popup_post->slug;
	$user_popup->type                = 'full';
	$user_popup->show_on_load        = $post->show_modal_on_load && (int) $post->modal === (int) $popup;
	$user_popup->options->cls_page[] = 'header-theme-transparent';
	$user_popup->class_name[]        = 'xd-modal--modal-post-type';
	$user_popup->has_close_button    = false;
	$user_popup->embed_template      = str_contains( $popup_post->content(), 'iframe' );
	$user_popup->dataset['slug']     = $popup_post->slug;
	$user_popup->post                = $popup_post;
	$template_props->user_popups[]   = new XD_Type(
		array(
			'modal' => $user_popup,
			'post'  => $popup_post,
		)
	);
}

$template_props->user_flyouts = array();

if ( ! empty( $post->flyouts ) ) {
	$flyouts = array_merge( $flyouts, $post->flyouts );
}

$flyout_posts = get_posts(
	array(
		'post_type'      => apply_filters( 'xd_flyout_post_types', array( 'flyout' ) ),
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'post_name__in'  => $ui_links,
		'fields'         => 'ids',
	)
);


$flyouts = array_merge( $flyouts, $flyout_posts );
$flyouts = array_values( array_unique( $flyouts ) );

foreach ( $flyouts as $flyout ) {
	$flyout_post = Timber::get_post( $flyout );

	$flyout_post_types          = xd_flyout_post_types();
	$embedded_flyout_post_types = xd_embedded_flyout_post_types();

	if ( ! in_array( $flyout_post->post_type, $flyout_post_types, true ) ) {
		continue;
	}

	if ( ! xd_is_collection_page( $flyout_post->post_type ) &&
			! in_array( $flyout_post->post_type, $embedded_flyout_post_types, true ) ) {
				continue;
	}

	$user_flyout                      = new XD_Flyout();
	$user_flyout->id                  = $flyout_post->slug;
	$user_flyout->options->cls_page[] = 'header-theme-transparent';
	$user_flyout->class_name[]        = 'xd-flyout--flyout-post-type';
	$user_flyout->has_navbar_spacing  = true;
	$user_flyout->embed_template      = str_contains( $flyout_post->content(), 'iframe' );
	$user_flyout->has_navbar_spacing  = true;
	$user_flyout->dataset['slug']     = $flyout_post->slug;
	$user_flyout->dataset['link']     = $flyout_post->link;
	$user_flyout->post                = $flyout_post;

	$user_flyout->class_name[] = 'xd-flyout--' . $flyout_post->post_type;

	if ( xd_is_collection_page( $flyout_post->post_type ) ) {

		$query_var = $flyout_post->post_type . '-slug';

		if ( get_query_var( $query_var ) === $flyout_post->slug ) {
			$user_flyout->show_on_load    = true;
			$template_props->class_name[] = 'uk-offcanvas-page';
			$template_props->class_name[] = 'modal-open';
			$template_props->body_class  .= ' uk-offcanvas-flip';
			$user_flyout->class_name[]    = 'uk-open';
			if ( false !== $user_flyout->options->overlay ) {
				$user_flyout->class_name[] = 'uk-offcanvas-overlay';
			}
			if ( false !== $user_flyout->options->flip ) {
				$template_props->body_class .= ' uk-offcanvas-flip';
			}
		}
	}

	$template_props->user_flyouts[] = new XD_Type(
		array(
			'flyout' => $user_flyout,
			'post'   => $flyout_post,
		)
	);
}
