<?php
/**
 * Timber Filters
 *
 * @package Leap Editor
 */

namespace Leap\Editor\Timber_Filters;

use Timber\PostCollection;
use Timber\PostQuery;
use Timber\Timber;
use XD\Types\XD_Post;
use XD\Types\XD_Template_Props;
use XD\Types\XD_Type_Base;

/**
 * Removes filter previously added by the theme.
 *
 * @since 1.3.0
 */
function xd_remove_deprecated_timber_filters() {
	remove_filter( 'timber_post_get_meta', 'xd_timber_post_get_meta', 10, 2 );
	remove_filter( 'timber_context', 'xd_add_debug_context' );
}

// Must happen before filter are registered  in plugin (priority 10).
if ( xd_theme_version_compare( '<=', '2.4.3' ) ) {
	add_action( 'after_setup_theme', __NAMESPACE__ . '\xd_remove_deprecated_timber_filters', 5 );
}

/**
 * Get a list of twig template candidates for a given route
 *
 * @since 1.3.0
 * @param string $template potential candidate if .twig file.
 *
 * @see wp-includes/template.php
 */
function xd_get_template_list( $template ) {
	$templates      = array();
	$queried_object = get_queried_object();

	if ( is_404() ) {
		$templates[] = '404.twig';
	}

	if ( is_search() ) {
		$templates[] = 'search.twig';
	}

	if ( is_front_page() ) {
		$templates[] = 'front-page.twig';
	}

	if ( is_home() ) {
		$templates[] = 'home.twig';
	}

	if ( is_tax() ) {

		if ( ! empty( $queried_object->slug ) ) {
			$taxonomy    = $queried_object->taxonomy;
			$templates[] = "taxonomy-$taxonomy.twig";
		}
		$templates[] = 'taxonomy.twig';
	}

	if ( is_single() ) {

		if ( ! empty( $queried_object->post_type ) ) {
			$templates[] = "single-{$queried_object->post_type}.twig";
		}

		$templates[] = 'single.twig';
	}

	if ( is_page() ) {
		if ( str_ends_with( $template, '.twig' ) && $template && 0 === validate_file( $template ) ) {
			array_unshift( $templates, $template );
		}
		$template = get_page_template_slug();
		if ( $template && 0 === validate_file( $template ) ) {
			array_unshift( $templates, $template );
		}
		$pagename = get_query_var( 'pagename' );
		if ( $pagename ) {
			$templates[] = "page-{$pagename}.twig";
		}
		$templates[] = 'page.twig';
	}

	if ( is_singular() ) {
		$templates[] = 'singular.twig';
	}

	if ( ! empty( $queried_object->ID ) ) {
		if ( post_password_required( $queried_object->ID ) ) {
			array_unshift( $templates, 'password-protected.twig' );
		}
	}

	if ( is_category() ) {
		$templates[] = 'category.twig';
	}

	if ( is_tag() ) {
		$templates[] = 'tag.php';
	}

	if ( is_archive() ) {
		$post_types = array_filter( (array) get_query_var( 'post_type' ) );

		if ( count( $post_types ) === 1 ) {
			$post_type   = reset( $post_types );
			$templates[] = "archive-{$post_type}.twig";
		}
		$templates[] = 'archive.twig';
	}

	$templates[] = 'index.twig';
	return $templates;
}


/**
 * Override WordPress template include with timber render.
 *
 * @since 1.3.0
 * @param String $template The original php template.
 */
function xd_twig_template_include( $template ) {

	if ( ! class_exists( 'XD\Types\XD_Template_Props' ) ) {
		return $template;
	}

	if ( ! class_exists( 'Timber\Timber' ) ) {
		return $template;
	}

	$templates        = xd_get_template_list( $template );
	$template_folders = apply_filters( 'xd_template_files', array( get_template_directory() . '/views' ) );
	$exists           = false;
	foreach ( $templates as $template_candidate ) {
		foreach ( $template_folders as $template_folder ) {
			if ( file_exists( $template_folder . '/' . $template_candidate ) ) {
				$exists = true;
				break 2;
			}
		}
	}
	if ( ! $exists ) {
		return $template;
	}
	$template_props = new XD_Template_Props();
	$context        = Timber::context();

	if ( 'v1' === get_option( 'timber_loader_version', 'v1' ) ) {
		$context['posts'] = new PostCollection( Timber::get_posts() );
		if ( is_singular() ) {
			$context['post'] = isset( $context['posts'][0] ) ? $context['posts'][0] : new XD_Post();
		} else {
			$context['post'] = new XD_Post();
		}
	}
	$context['templateProps'] = $template_props;

	$post    = &$context['post'];
	$posts   = &$context['posts'];
	$theme   = &$context['theme'];
	$site    = &$context['site'];
	$user    = &$context['user'];
	$request = &$context['request'];

	foreach ( apply_filters(
		'xd_template_context_files',
		array(
			get_template_directory() . '/template-context/templates/templates.php',
		)
	) as $context_file ) {
		if ( file_exists( $context_file ) ) {
			include realpath( $context_file );
		}
	}

	foreach ( apply_filters(
		'xd_template_props_files',
		array(
			get_template_directory() . '/template-context/templates/template-props.php',
		)
	) as $context_file ) {
		if ( file_exists( $context_file ) ) {
			include realpath( $context_file );
		}
	}

	if ( xd_theme_version_compare( '>=', '2.6.01' ) ) {
		// render the content early so that block dependencies are queued before wp_head.
		if ( isset( $post ) && method_exists( $post, 'content' ) ) {
			$post->content();
		}
	}

	Timber::render( $templates, $context );
	do_action( 'shutdown' );
	exit;
}
if ( xd_theme_version_compare( '>', '2.4.3' ) ) {
	add_filter( 'template_include', __NAMESPACE__ . '\xd_twig_template_include', 10, 3 );
}



/**
 * Load additional data into the timber context.
 *
 * @since 1.3.0
 * @param array $meta the original timber data.
 * @param int   $post_id the post id.
 */
function xd_timber_post_get_meta( $meta, $post_id ) {
	$meta['fields']  = get_fields( $post_id );
	$meta['excerpt'] = wp_trim_words( get_the_excerpt( $post_id ), 20 );
	return $meta;
}
if ( 'v1' === get_option( 'timber_loader_version', 'v1' ) ) {
	add_filter( 'timber_post_get_meta', __NAMESPACE__ . '\xd_timber_post_get_meta', 10, 2 );
}

/**
 * This exposes the entire twig context to the twig context.
 * In other words, you can view the full twig context through {{context | debug}}.
 * This is only enabled if WP_DEBUG is true, because it has the potential to consume
 * huge amounts of memory.
 * This can be useful, but in some cases it produces so much output
 * that it will crash the browser.
 *
 * @param \Timber\Cache $cache The cached timber context.
 */
function xd_add_debug_context( $cache ) {
	$cache['context'] = $cache;
	return $cache;
}
if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
	if ( 'v1' === get_option( 'timber_loader_version', 'v1' ) ) {
		add_filter( 'timber_context', __NAMESPACE__ . '\xd_add_debug_context' );
	}
	add_filter( 'timber/context', __NAMESPACE__ . '\xd_add_debug_context' );
}

/**
 * Replace search results with relevanssi search results.
 *
 * @since 1.4.54
 * @param  Timber\Cache $cache The cached timber context.
 */
function xd_get_relevanssi_search_results( $cache ) {
	global $wp_query;
	if ( ! is_admin() && is_search() && function_exists( 'relevanssi_do_query' ) ) {
		if ( 'v1' === get_option( 'timber_loader_version', 'v1' ) ) {
			$cache['posts'] = new PostCollection( relevanssi_do_query( $wp_query ) );
		} else {
			$cache['posts'] = Timber::get_posts( relevanssi_do_query( $wp_query ) );
		}
	}
	return $cache;
}
if ( 'v1' === get_option( 'timber_loader_version', 'v1' ) ) {
	add_filter( 'timber_context', __NAMESPACE__ . '\xd_get_relevanssi_search_results' );
}
add_filter( 'timber/context', __NAMESPACE__ . '\xd_get_relevanssi_search_results' );

/**
 * Filters timber post class
 *
 * @param string $class the original class.
 */
function xd_filter_timber_post_class_map( $class ) {
	if ( xd_theme_version_compare( '<=', '2.4.3' ) ) {
		return $class;
	}
	return 'XD\Types\XD_Post';
}

if ( 'v1' === get_option( 'timber_loader_version', 'v1' ) ) {
	add_filter( 'Timber\PostClassMap', __NAMESPACE__ . '\xd_filter_timber_post_class_map' );
}
add_filter( 'timber/post/class', __NAMESPACE__ . '\xd_filter_timber_post_class_map' );

/**
 * Import timber context data.
 *
 * @since 1.3.0
 * @param array $data Timber context data.
 */
function xd_timber_compile_data( $data ) {
	foreach ( $data as $key => $val ) {
		if ( $val instanceof XD_Type_Base ) {
			$data[ $key ] = $val->get_data();
		}
		if ( is_array( $val ) ) {
			$data[ $key ] = xd_timber_compile_data( $val );
		}
	}
	return $data;
}

if ( 'v1' === get_option( 'timber_loader_version', 'v1' ) ) {
	add_filter( 'timber_compile_data', __NAMESPACE__ . '\xd_timber_compile_data' );
	add_filter( 'timber_render_data', __NAMESPACE__ . '\xd_timber_compile_data' );
}
add_filter( 'timber/compile/data', __NAMESPACE__ . '\xd_timber_compile_data' );
add_filter( 'timber/render/data', __NAMESPACE__ . '\xd_timber_compile_data' );

/**
 * Loads the XD_Post class on demand and sets version/compat flags.
 *
 * @param string $class Fully-qualified class name.
 */
function xd_autoload_xd_post_class( $class ) {
	if ( 'XD\Types\XD_Post' === $class ) {
		if ( ! class_exists( 'XD\Types\XD_Post', false ) ) {
			require __DIR__ . '/types/class-xd-post.php';

			// Determine Timber "selected" major version (by your site option).
			$selected                          = get_option( 'timber_loader_version', 'v1' );
			\XD\Types\XD_Post::$timber_version = ( 'v1' === $selected ) ? 1 : 2;

			// Default: no compat. Compat is only for Timber v2 + older themes.
			$compat = false;

			if ( 2 === \XD\Types\XD_Post::$timber_version ) {
				$parent_ok = xd_theme_version_compare( '>=', '2.7.12' );
				$child_ok  = xd_theme_version_compare( '>=', '1.1.05', true );
				$compat    = ! ( $parent_ok && $child_ok ); // older theme => compat ON.
			}

			if ( ! defined( 'XD_COMPAT_MODE' ) ) {
				define( 'XD_COMPAT_MODE', $compat );
			} else {
				$compat = (bool) XD_COMPAT_MODE;
			}
			$compat = (bool) apply_filters( 'xd/compat_mode', $compat );

			// Apply flags for this request.
			\XD\Types\XD_Post::$compat_mode = $compat;
		}
	}
}
spl_autoload_register( __NAMESPACE__ . '\xd_autoload_xd_post_class' );

/**
 * Filter twig template paths.
 *
 * @param array $paths original template paths.
 */
function xd_filter_twig_template_paths( $paths ) {

	if ( xd_theme_version_compare( '<=', '2.5.1' ) ) {

		if ( 'v1' === get_option( 'timber_loader_version', 'v1' ) ) {
			return array(
				'/',
				get_template_directory(),
				get_template_directory() . '/views',
			);
		}

		return array(
			array(
				'/',
				get_template_directory(),
				get_template_directory() . '/views',
			),
		);
	}

	if ( 'v1' === get_option( 'timber_loader_version', 'v1' ) ) {
		return array(
			get_stylesheet_directory() . '/views',
			get_template_directory() . '/views',
			realpath( get_template_directory() . '/../' ),
		);
	}

	return array(
		array(
			get_stylesheet_directory() . '/views',
			get_template_directory() . '/views',
			realpath( get_template_directory() . '/../' ),
		),
	);
}

if ( 'v1' === get_option( 'timber_loader_version', 'v1' ) ) {
	add_filter( 'timber/loader/paths', __NAMESPACE__ . '\xd_filter_twig_template_paths', 0 );
}
add_filter( 'timber/locations', __NAMESPACE__ . '\xd_filter_twig_template_paths', 0 );
