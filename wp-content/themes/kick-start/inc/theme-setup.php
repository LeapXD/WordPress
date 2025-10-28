<?php
/**
 * Theme setup.
 *
 * @package Kicks
 */

/**
 * Filter twig template paths.
 *
 * @param array $paths original template paths.
 */
function xdc_filter_twig_template_paths( $paths ) {
	$views = get_stylesheet_directory() . '/views';
	array_unshift( $paths, $views );

	return $paths;
}

//phpcs:ignore
// add_filter( 'timber/loader/paths', __NAMESPACE__ . '\xdc_filter_twig_template_paths' );

/**
 * Filters the block json folder locations.
 *
 * @param array $folders the original folder locations.
 */
function xdc_filter_block_json_folders( $folders ) {
	$block_json_folder = get_stylesheet_directory() . '/editor/blocks';
	$folders['xdc']    = $block_json_folder;

	return $folders;
}

add_filter( 'xd_block_json_folders', __NAMESPACE__ . '\xdc_filter_block_json_folders' );



/**
 * Filters the module json folder locations.
 *
 * @param array $folders the original folder locations.
 */
function xdc_filter_module_json_folders( $folders ) {
	$module_json_folder = get_stylesheet_directory() . '/editor/modules';
	$folders['xdc']     = $module_json_folder;

	return $folders;
}

add_filter( 'xd_module_json_folders', __NAMESPACE__ . '\xdc_filter_module_json_folders' );


/**
 * Filters the plugin json folder locations.
 *
 * @param array $folders the original folder locations.
 */
function xdc_filter_plugin_json_folders( $folders ) {
	$plugin_json_folder = get_stylesheet_directory() . '/editor/plugins';
	$folders['xdc']     = $plugin_json_folder;

	return $folders;
}

add_filter( 'xd_plugin_json_folders', __NAMESPACE__ . '\xdc_filter_plugin_json_folders' );

/**
 * Filters The included template files
 *
 * @param array $files the original file list.
 */
function xdc_filter_template_files( $files ) {
	return $files;
}

add_filter( 'xd_template_files', __NAMESPACE__ . '\xdc_filter_template_files' );

/**
 * Filters The included template context files
 *
 * @param array $files the original file list.
 */
function xdc_filter_template_context_files( $files ) {
	$template = get_stylesheet_directory() . '/template-context/templates/templates.php';
	$files[]  = $template;
	return $files;
}

add_filter( 'xd_template_context_files', __NAMESPACE__ . '\xdc_filter_template_context_files' );

/**
 * Filters The included template props files
 * Note: these files are loaded after the blocks have been rendered.
 *
 * @param array $files the original file list.
 */
function xdc_filter_template_props_files( $files ) {
	$template = get_stylesheet_directory() . '/template-context/templates/template-props.php';
	$files[]  = $template;
	return $files;
}

add_filter( 'xd_template_props_files', __NAMESPACE__ . '\xdc_filter_template_props_files' );

/**
 * Filters the location of template and context files.
 *
 * @param array  $folders the original locations for group.
 * @param string $group the the group to locate files for.
 */
function xdc_filter_template_folders( $folders, $group ) {
	if ( 'block_template' === $group ) {
		$folders[] = get_stylesheet_directory() . '/views/blocks' . DIRECTORY_SEPARATOR;
	}
	if ( 'block_context' === $group ) {
		$folders[] = get_stylesheet_directory() . '/template-context/blocks' . DIRECTORY_SEPARATOR;
	}
	return $folders;
}

add_filter( 'xd_template_folders', __NAMESPACE__ . '\xdc_filter_template_folders', 10, 2 );


/**
 * Setup manually selectable page templates in back end
 *
 * @param array $templates the original template list.
 */
function xdc_twig_page_templates( $templates ) {
	if ( ! class_exists( 'Timber\Timber' ) ) {
		return $templates;
	}

	return array_merge(
		$templates,
		array(
			//phpcs:ignore
			// 'your-custom-template.twig' => 'Custom Template',
		)
	);
}

add_filter( 'theme_page_templates', __NAMESPACE__ . '\xdc_twig_page_templates' );


add_filter( 'xd_menu_flyout_enabled', '__return_true' );
add_filter( 'xd_search_modal_enabled', '__return_false' );
add_filter( 'xd_split_hero_enabled', '__return_false' );

add_filter( 'xd_floorplan_post_type_enabled', '__return_false' );
add_filter( 'xd_floorplan_taxonomies_enabled', '__return_false' );

add_filter( 'xd_project_post_type_enabled', '__return_false' );
add_filter( 'xd_project_taxonomies_enabled', '__return_false' );
add_filter( 'xd_project_archive_enabled', '__return_false' );

add_filter( 'xd_accommodation_post_type_enabled', '__return_false' );

add_filter( 'xd_promotion_post_type_enabled', '__return_false' );
add_filter( 'xd_promotion_archive_enabled', '__return_false' );

add_filter( 'xd_map_pin_post_type_enabled', '__return_false' );
add_filter( 'xd_map_pin_taxonomies_enabled', '__return_false' );
