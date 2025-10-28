<?php
/**
 * Scrollspy setup.
 *
 * @package Kicks
 * @phpcs:disable Squiz.PHP.CommentedOutCode.Found
 * @phpcs:disable Squiz.Commenting.InlineComment.InvalidEndChar
 */

// Example:

/**
 * Register scrollspy rules.
 */
function xdc_register_scrollspy_rules() {

	// xd_deregister_scrollspy_rule( '.hero-full');

	// register new rule
	// xd_register_scrollspy_rule( '.hero-full', array( 'target' => '.hero-custom__foreground-body-contents > *, .hero-full__foreground-footer' ) );
}
add_action( 'after_setup_theme', 'xdc_register_scrollspy_rules' );
