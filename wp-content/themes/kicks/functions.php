<?php
/**
 * Bootstrap theme includes.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Kicks
 */

$theme_data = wp_get_theme();

define( 'THEME_VER', $theme_data->get( 'Version' ) );
define( 'TEMPLATE_URI', get_template_directory_uri() );
define( 'TEMPLATE_DIR', get_template_directory() );
define( 'PLUGIN_DIR_PATH', ABSPATH . 'wp-content/plugins/' );

require TEMPLATE_DIR . '/vendor/autoload.php';

require TEMPLATE_DIR . '/template-context/types/types.php';
require TEMPLATE_DIR . '/inc/images.php';
require TEMPLATE_DIR . '/inc/install/class-xd-theme-installer.php';
require TEMPLATE_DIR . '/inc/template-functions.php';
require TEMPLATE_DIR . '/inc/acf-block-render.php';
require TEMPLATE_DIR . '/inc/acf-blocks.php';
require TEMPLATE_DIR . '/inc/block-config.php';
require TEMPLATE_DIR . '/inc/class-xd-scrollspy.php';
require TEMPLATE_DIR . '/inc/custom-post-types.php';
require TEMPLATE_DIR . '/inc/custom-taxonomies.php';
require TEMPLATE_DIR . '/inc/custom-archives.php';
require TEMPLATE_DIR . '/inc/enqueue-scripts.php';
require TEMPLATE_DIR . '/inc/gravity-forms.php';
require TEMPLATE_DIR . '/inc/options.php';
require TEMPLATE_DIR . '/inc/theme-setup.php';
require TEMPLATE_DIR . '/inc/timber.php';
require TEMPLATE_DIR . '/inc/rest.php';

require TEMPLATE_DIR . '/inc/map-pins.php';

require TEMPLATE_DIR . '/inc/menus/class-xd-menu.php';
require TEMPLATE_DIR . '/inc/menus/class-xd-navbar-menu.php';

require TEMPLATE_DIR . '/inc/recaptcha/recaptcha.php';
require TEMPLATE_DIR . '/inc/recaptcha/scramble-email.php';
