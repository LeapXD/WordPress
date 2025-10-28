<?php
/**
 * Twig configuration.
 *
 * @package Kicks
 */

use \Timber\Timber;
use Twig\TwigFilter;
use Twig\TwigFunction;
use XD\Types\XD_Post;
use XD\Types\XD_Type;
use XD\Types\XD_Type_Base;

/**
 * Setup manually selectable page templates in back end
 *
 * @param array $templates the original template list.
 */
function xd_twig_page_templates( $templates ) {
	if ( ! class_exists( 'Timber\Timber' ) ) {
		return $templates;
	}

	return array_merge(
		$templates,
		array(
			'path/to/custom/template',
		)
	);
}

//phpcs:ignore
// add_filter( 'theme_page_templates', 'xd_twig_page_templates' );

/**
 * A function to load the primary menu in twig templates.
 */
function primary_menu() {
	if ( has_nav_menu( 'primary-menu' ) ) {
		return wp_nav_menu(
			array(
				'theme_location' => 'primary-menu',
				'menu_id'        => 'primary-menu',
				'menu_class'     => 'xd-menu__nav',
				'container'      => 'nav',
				'walker'         => new XD_Menu(),
				'echo'           => false,
			)
		);
	}
	return '';
}

/**
 * A function to load a uikit menu in twig templates.
 *
 * @param string $menu menu.
 */
function nav_menu( $menu ) {
	if ( has_nav_menu( $menu ) ) {
		return wp_nav_menu(
			array(
				'theme_location' => $menu,
				'menu_id'        => $menu,
				'menu_class'     => 'uk-navbar-nav',
				'link_before'    => '<span>',
				'link_after'     => '</span>',
				'container'      => 'ul',
				'walker'         => new XD_Navbar_Menu(),
				'echo'           => false,
			)
		);
	}
	return '';
}

/**
 * A function to load WordPress menus in twig templates.
 *
 * @param string $menu menu.
 */
function footer_menu( $menu ) {
		return wp_nav_menu(
			array(
				'menu'        => $menu,
				'echo'        => false,
				'fallback_cb' => false,
			)
		);
}

/**
 * A function to load the social icons in twig templates.
 *
 * @param String $theme icons theme.
 */
function get_timber_social_icons( $theme ) {
		$context            = Timber::context();
		$context['options'] = get_fields( 'option' );
		$context['theme']   = $theme;
		Timber::render( 'partials/social-icons.twig', $context );
}

/**
 * Json encode filter
 *
 * @param mixed $data the raw data.
 */
function xd_twig_json_filter( $data ) {
	return ! empty( $data ) ? esc_attr( wp_json_encode( $data ) ) : '';
}

/**
 * Twig debug filter.
 *
 * @param mixed $data the raw data.
 */
function xd_twig_debug_filter( $data ) {
	$methods = array();
	$vars    = array();
	$props   = array();
	if ( $data instanceof XD_Post ) {
		$props = ( new XD_Type( $data ) )->get_data();
	}
	if ( is_object( $data ) ) {
		$methods = get_class_methods( $data );
		if ( ! empty( $methods ) ) {
			$methods = array_flip( $methods );
			foreach ( $methods as &$method ) {
				$method = 'method';
			}
		}
		$vars = get_object_vars( $data );
		$data = $methods + $vars + $props;
	}
	if ( is_array( $data ) ) {
		foreach ( $data as $key => $field ) {
			if ( $field instanceof XD_Type_Base ) {
				$data[ $key ] = $field->get_data();
			}
		}
	}
	$json = wp_json_encode(
		$data,
		JSON_PRETTY_PRINT
	);
	if ( false === $json ) {
		$json = wp_json_encode(
			array(
				'json_error'         => json_last_error(),
				'json_error_message' => json_last_error_msg(),
			)
		);
	}
	return '<pre style="font-size:14px;line-height:1.2;font-family:monospace">' . esc_html(
		$json
	) . '</pre>';
}

/**
 * Registers the twig functions and filters
 *
 * @param \Twig\Environment $twig The twig environment.
 */
function xd_register_twig_functions( $twig ) {
	$twig->addFunction( new TwigFunction( 'xd_get_picture', 'xd_get_picture' ) );
	$twig->addFunction( new TwigFunction( 'get_icon', 'get_icon' ) );
	$twig->addFunction( new TwigFunction( 'do_action', 'do_action' ) );
	$twig->addFunction( new TwigFunction( 'get_theme_mod', 'get_theme_mod' ) );
	$twig->addFunction( new TwigFunction( 'has_custom_logo', 'has_custom_logo' ) );
	$twig->addFunction( new TwigFunction( 'is_front_page', 'is_front_page' ) );
	$twig->addFunction( new TwigFunction( 'get_custom_logo', 'get_custom_logo' ) );
	$twig->addFunction( new TwigFunction( 'has_nav_menu', 'has_nav_menu' ) );
	$twig->addFunction( new TwigFunction( 'has_alt_logo', 'has_alt_logo' ) );
	$twig->addFunction( new TwigFunction( 'get_alt_logo', 'get_alt_logo' ) );
	$twig->addFunction( new TwigFunction( 'nav_menu', 'nav_menu' ) );
	$twig->addFunction( new TwigFunction( 'primary_menu', 'primary_menu' ) );
	$twig->addFunction( new TwigFunction( 'footer_menu', 'footer_menu' ) );
	$twig->addFunction( new TwigFunction( 'get_social_icons', 'get_timber_social_icons' ) );
	$twig->addFunction( new TwigFunction( 'xd_link_pages', 'xd_link_pages' ) );
	$twig->addFunction( new TwigFunction( 'xd_entry_footer', 'xd_entry_footer' ) );
	$twig->addFunction( new TwigFunction( 'get_the_posts_pagination', 'get_the_posts_pagination' ) );

	$twig->addFilter( new TwigFilter( 'json', 'xd_twig_json_filter' ) );
	$twig->addFilter( new TwigFilter( 'debug', 'xd_twig_debug_filter' ) );
	$twig->addFilter( new TwigFilter( 'obfuscate_email', 'obfuscate_email' ) );

	if ( function_exists( 'xd_classnames' ) ) {
		$twig->addFunction( new TwigFunction( 'xd_classnames', 'xd_classnames' ) );
	}

	return $twig;
}
add_filter( 'timber/twig', 'xd_register_twig_functions' );
