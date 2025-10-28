<?php
/**
 * Twig configuration.
 *
 * @package Kicks
 * @phpcs:disable Squiz.PHP.CommentedOutCode.Found
 */

use Twig\TwigFunction;

/**
 * Registers the twig functions and filters
 *
 * @param \Twig\Environment $twig The twig environment.
 */
function xdc_register_twig_functions( $twig ) {
	// $twig->addFunction( new TwigFunction( 'custom_function', 'custom_function' ) );

	return $twig;
}

add_filter( 'timber/twig', 'xdc_register_twig_functions' );
