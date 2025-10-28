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

$template_props->import(
	array(
		'contact_form'     => '<iframe id="gform-1" class="xd-gravityform-iframe" title="form" src="/get-form/?form_id=1" width="100%" height="100" loading="lazy"></iframe>',
		'contact_widget_1' => Timber::get_widgets( 'xd-contact-1' ),
		'contact_widget_2' => Timber::get_widgets( 'xd-contact-2' ),
		'contact_widget_3' => Timber::get_widgets( 'xd-contact-3' ),
	)
);
