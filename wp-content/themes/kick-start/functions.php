<?php
/**
 * Bootstrap theme includes.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 * @package Kicks
 */

/**
 * Theme includes.
 */
define( 'STYLESHEET_URI', get_stylesheet_directory_uri() );
define( 'STYLESHEET_DIR', get_stylesheet_directory() );
require get_stylesheet_directory() . '/template-context/types/types.php';
require get_stylesheet_directory() . '/inc/template-functions.php';
require get_stylesheet_directory() . '/inc/block-config.php';
require get_stylesheet_directory() . '/inc/scrollspy.php';
require get_stylesheet_directory() . '/inc/custom-post-types.php';
require get_stylesheet_directory() . '/inc/enqueue-scripts.php';
require get_stylesheet_directory() . '/inc/theme-setup.php';
require get_stylesheet_directory() . '/inc/timber.php';
