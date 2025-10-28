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

use \Timber\Timber;
use XD\Types\XD_Background_Image;
use XD\Types\XD_Flyout;
use XD\Types\XD_Link;
use XD\Types\XD_Modal;

use function Leap\Editor\Block_Render\xd_render_block;

$popups  = array();
$flyouts = array();

require 'template-parts.php';

$id = get_the_ID();

if ( is_category() || is_home() ) {
	$post = Timber::get_post( get_option( 'page_for_posts', true ) );
	include 'blog.php';
} elseif ( $id && (int) get_option( 'xd_contact_page' ) === $id ) {
	include 'page-contact.php';
} elseif ( $id && is_singular( 'page' ) && (int) get_option( 'page_for_projects' ) === $id ) {
	include 'archive-project.php';
} elseif ( is_singular( 'project' ) ) {
	include 'single-project.php';
} elseif ( $id && is_singular( 'page' ) && (int) get_option( 'page_for_promotions' ) === $id ) {
	include 'archive-promotion.php';
} elseif ( is_page( 'get-form' ) ) {
	$form_id            = get_query_var( 'form_id' );
	$display_form_title = get_query_var( 'display_form_title' );
	$display_form_desc  = get_query_var( 'display_form_desc' );
	$post->content      = \gravity_form( $form_id, $display_form_title, $display_form_desc, false, null, false, 0, false );
}

$hero_button = new XD_Link( $post->hero_button );

if ( ! $hero_button->is_empty() ) {
	$button_attributes = array_merge(
		$post->hero_button,
		array(
			'text'        => $post->hero_button['title'],
			'buttonStyle' => 'xd-button--inverse',
		),
	);
	$template_props->import( array( 'hero_button' => xd_render_block( 'xd/button', $button_attributes ) ) );
}

$template_props->import(
	array(
		'recaptcha_site_key'        => defined( 'RECAPTCHA_PUBLIC_KEY' ) ? RECAPTCHA_PUBLIC_KEY : null,
		'search_query'              => filter_input( INPUT_GET, 's' ),
		'xd_footer_main_navigation' => Timber::get_widgets( 'xd-footer-main-navigation' ),
		'xd_footer_bottom'          => Timber::get_widgets( 'xd-footer-bottom' ),
		'class_name'                => get_body_class(),
	)
);

$modal_src                     = ! empty( $post->hero_video['modal_src'] ) ? $post->hero_video['modal_src'] : false;
$hero_image                    = new XD_Background_Image();
$hero_image->primary           = $post->featured_image;
$hero_image->secondary         = $post->featured_image_mobile;
$hero_image->options->lazyload = false;
$hero_image->video->import( $post->hero_video );

// Defined in hero.json plugin.
if ( ! $hero_image->video->is_empty() && ! empty( $hero_image->video->md['portrait'] ) ) {
	$hero_image->options->portrait = true;
}

if ( $modal_src ) {
	$hero_image->video->show_modal_button          = false;
	$hero_image->video->modal->src                 = $modal_src;
	$hero_image->video->modal->id                  = 'full-video';
	$hero_image->video->modal->options->cls_page[] = 'header-theme-transparent';
	$hero_image->video->modal->options->cls_page[] = 'video-open';
	$hero_image->video->modal->type                = 'full';
	$hero_image->video->modal->class_name[]        = 'xd-modal--video';
}

$menu_flyout                     = new XD_Flyout();
$menu_flyout->inner_class_name   = 'xd-menu';
$menu_flyout->options->cls_page  = array( 'menu-open', 'header-theme-transparent' );
$menu_flyout->id                 = 'main-menu';
$menu_flyout->has_navbar_spacing = true;
$menu_flyout->enabled            = apply_filters( 'xd_menu_flyout_enabled', true );

$search_modal                    = new XD_Modal();
$search_modal->id                = 'site-search';
$search_modal->options->cls_page = array( 'search-open', 'header-theme-filled' );
$search_modal->type              = 'full';
$search_modal->class_name        = array( 'xd-search-modal' );
$search_modal->enabled           = apply_filters( 'xd_search_modal_enabled', false );

$popup_modal                      = new XD_Modal();
$popup_modal->id                  = 'popup';
$popup_modal->type                = 'dialog';
$popup_modal->options->cls_page[] = 'popup-modal-open';
$popup_modal->options->cls_page[] = 'header-theme-transparent';
$popup_modal->has_close_button    = true;

$post->import( array( 'hero_image' => $hero_image ) );

$template_props->menu_flyout  = $menu_flyout;
$template_props->search_modal = $search_modal;
$template_props->popup_modal  = $popup_modal;

if ( is_front_page()
&& ! empty( $template_props->alert_bar )
&& ! empty( $template_props->alert_bar->display_alert_bar ) ) {
	$template_props->alert_bar->show         = true;
	$template_props->class_name['alert-bar'] = 'alert-bar';
}

if ( is_singular( 'post' ) || is_singular( 'project' ) || is_404() || is_search() || get_the_ID() === (int) get_option( 'xd_thank_you_page' ) ) {
	$template_props->class_name['page-header-theme-filled'] = 'page-header-theme-filled';
	$template_props->hero_class_name                        = 'hero--no-image';
} elseif ( $hero_image->has_image() ) {
	$template_props->class_name['page-header-theme-transparent'] = 'page-header-theme-transparent';
	$template_props->hero_class_name                             = 'xd-has-background-image xd-has-background-image--overlay';
} else {
	$template_props->class_name['page-header-theme-filled'] = 'page-header-theme-filled';
	$template_props->hero_class_name                        = 'hero--no-image';
}

if ( is_front_page() && apply_filters( 'xd_split_hero_enabled', false ) ) {
	require 'hero-split.php';
}
