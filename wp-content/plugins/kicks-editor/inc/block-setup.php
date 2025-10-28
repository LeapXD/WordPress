<?php
/**
 * Block setup.
 *
 * @package Leap Editor
 */

namespace Leap\Editor\Block_Setup;

use WP_Block_Editor_Context;
use WP_Block_Type_Registry;

/**
 * Fetches the block json folders from plugin and  theme.
 */
function xd_get_block_json_folders() {

	$xd_block_json_folders = array(
		'core'  => realpath( get_template_directory() . '/editor/blocks/core' ),
		'parts' => realpath( get_template_directory() . '/editor/blocks/parts' ),
		'theme' => realpath( get_template_directory() . '/editor/blocks' ),
	);

	// Back compat.
	if ( xd_theme_version_compare( '<=', '2.4.3' ) ) {
		$xd_block_json_folders = array(
			'plugin' => realpath( dirname( __FILE__ ) . '/../build/blocks' ),
			// Load theme blocks last so they can override plugin blocks.
			'theme'  => realpath( get_template_directory() . '/assets/editor/components' ),
		);
	}
	$xd_block_json_folders = apply_filters( 'xd_block_json_folders', $xd_block_json_folders );

	return $xd_block_json_folders;
}

/**
 * Fetches the block widget data from local json files.
 */
function xd_get_widget_override_json() {

	$block_json = wp_cache_get( 'xd-widget-override-json' );
	if ( false !== $block_json ) {
		return $block_json;
	}
	$block_json = array();

	$xd_block_json_folder = realpath( get_template_directory() . '/editor/widgets' );
	$xd_block_json_folder = apply_filters( 'xd_widget_override_json', $xd_block_json_folder );

	foreach ( glob( $xd_block_json_folder . '/*.json' ) as $metadata_file ) {

		$block_type = wp_json_file_decode( $metadata_file, array( 'associative' => true ) );
		if ( ! empty( $block_type['name'] ) ) {
			$block_json[ $block_type['name'] ] = $block_type;
		}
	}

	$block_json = apply_filters( 'xd_widget_block_json', $block_json );

	wp_cache_set( 'xd-widget-override-json', $block_json );

	return $block_json;
}


/**
 * Fetches the module data from local json files.
 */
function xd_get_module_json() {

	$module_json = wp_cache_get( 'xd-module-json' );
	if ( false !== $module_json ) {
		return $module_json;
	}
	$module_json = array();

	$xd_module_json_folders = array(
		'theme' => realpath( get_template_directory() . '/editor/modules' ),
	);

	$xd_module_json_folders = apply_filters( 'xd_module_json_folders', $xd_module_json_folders );

	foreach ( $xd_module_json_folders as $xd_module_json_folder ) {
		foreach ( array_merge(
			glob( $xd_module_json_folder . '/**/index.json' ),
			glob( $xd_module_json_folder . '/*.json' )
		)as $module_file ) {
			$module = wp_json_file_decode( $module_file, array( 'associative' => true ) );
			if ( xd_theme_version_compare( '>=', '1.0.05', false ) ) {
				// From 1.0.05, modules require a name for ease of overriding.
				if ( ! empty( $module['name'] ) ) {
					if ( ! empty( $module['merge'] ) && isset( $module_json[ $module['name'] ] ) ) {
						// When module is merged, each property (attributes, inspectorControls, etc), of each named supported feature
						// replaces the corresponding property of the same feature in the parent module.
						$feature_indices = array_flip( array_column( $module_json[ $module['name'] ]['supports'], 'name' ) );
						foreach ( $module['supports'] as $feature ) {
							if ( isset( $feature_indices[ $feature['name'] ] ) ) {
								$module_json[ $module['name'] ]['supports'][ $feature_indices[ $feature['name'] ] ] = array_replace( $module_json[ $module['name'] ]['supports'][ $feature_indices[ $feature['name'] ] ], $feature );
							} else {
								$module_json[ $module['name'] ]['supports'][] = $feature;
							}
						}
					} else {
						$module_json[ $module['name'] ] = $module;
					}
				}
			} else {
				$module_json[] = $module;
			}
		}
	}

	$module_json = apply_filters( 'xd_module_json', $module_json );

	$module_json = array_values( $module_json );

	wp_cache_set( 'xd-module-json', $module_json );

	return $module_json;
}


/**
 * Retrieves setting defaults from block-settings.php.
 *
 * @param Mixed $name the setting name or null to retrieve all defaults.
 * @return array the setting defaults.
 */
function xd_get_default_block_settings( $name = '' ) {

	if ( ! empty( $name ) ) {
		$default_settings = wp_cache_get( 'xd-block-default-settings', $name );
		if ( false !== $default_settings ) {
			return $default_settings;
		}
	} else {
		$default_settings = wp_cache_get( 'xd-block-default-settings' );
		if ( false !== $default_settings ) {
			return $default_settings;
		}
	}

	$default_settings = array();
	$module_json      = xd_get_module_json();

	foreach ( $module_json as $group ) {
		if ( isset( $group['supports'] ) && is_array( $group['supports'] ) ) {
			foreach ( $group['supports'] as $support ) {
				if ( isset( $support['attributes'] ) && isset( $support['name'] ) ) {
					$default_settings[ $support['name'] ] = $support['attributes'];
				}
			}
		}
	}

	$default_settings = apply_filters( 'xd_default_block_settings', $default_settings );
	wp_cache_set( 'xd-block-default-settings', $default_settings );

	if ( ! empty( $name ) ) {

		if ( ! empty( $default_settings[ $name ] ) ) {
			wp_cache_set( 'xd-block-default-settings', $default_settings[ $name ], $name );
			return $default_settings[ $name ];
		}
		wp_cache_set( 'xd-block-default-settings', array(), $name );
		return array();
	}

	return $default_settings;

}


/**
 * Generate block metadata collections.
 */
function xd_get_block_type_metadata_collections() {

	$collections = array();
	foreach ( xd_get_block_json_folders() as $xd_block_json_folder ) {
		foreach ( glob( $xd_block_json_folder . '/**/block.json' ) as $block ) {
			$metadata = wp_json_file_decode( $block, array( 'associative' => true ) );
			if ( ! empty( $metadata['name'] ) ) {
				$namespace                 = explode( '/', $metadata['name'] );
				$namespace                 = $namespace[0];
				$name                      = str_replace( $namespace . '/', '', $metadata['name'] );
				$collections[ $namespace ] = $collections[ $namespace ] ?? array();
				if ( ! empty( $collections[ $namespace ][ $name ] ) ) {

					if ( 'xd' === $namespace && xd_theme_version_compare( '>=', '2.5.3' ) ) {
						// From 2.5.3, allow child theme and plugins to override parent theme blocks .
						if ( ! empty( $metadata['supports']['custom']['merge'] ) ) {
							$merge_variations                   = ! empty( $metadata['supports']['custom']['mergeVariations'] );
							$collections[ $namespace ][ $name ] = xd_merge_block_settings( $collections[ $namespace ][ $name ], $merge_variations, $metadata );
						} else {
							$collections[ $namespace ][ $name ] = $metadata;
						}
					} else {
						$collections[ $namespace ][ $name ] = array_replace_recursive( $collections[ $namespace ][ $name ], $metadata );
					}
				} else {
					$collections[ $namespace ][ $name ] = $metadata;
				}
			}
		}
	}

	foreach ( $collections as $namespace => $blocks ) {
		$collections[ $namespace ] = apply_filters( 'xd_block_json', $blocks, $namespace );
	}

	return $collections;

}


/**
 * Export blocks to json files.
 */
function xd_save_block_type_metadata_collections() {
	if ( ! wp_is_file_mod_allowed( 'xd_block_json' ) ) {
		return;
	}
	$collections       = xd_get_block_type_metadata_collections();
	$total_block_count = count( array_merge( ...array_values( $collections ) ) );
	/**
	 * Undefined type 'WP_CLI'.
	 *
	 * @disregard WP_CLI P1009 Undefined type 'WP_CLI'.
	 */
	if ( defined( 'WP_CLI' ) && \WP_CLI ) {
		/**
		 * Undefined type 'WP_CLI'.
		 *
		 * @disregard WP_CLI P1009 Undefined type 'WP_CLI'.
		 */
		$progress = \WP_CLI\Utils\make_progress_bar( 'Exporting blocks', $total_block_count );
	}

	foreach ( $collections as $namespace => $blocks ) {
		$json_folder = get_stylesheet_directory() . '/build/blocks/' . $namespace . '/';
		if ( ! file_exists( $json_folder ) ) {
			mkdir( $json_folder, 0755, true );
		}
				//phpcs:ignore
				file_put_contents(
					$json_folder . '/blocks-json.php',
					'<?php return ' . xd_export_blocks_array( $blocks ) . ';' . "\n"
				);

	}
	/**
	 * Undefined type 'WP_CLI'.
	 *
	 * @disregard WP_CLI P1009 Undefined type 'WP_CLI'.
	 */
	if ( defined( 'WP_CLI' ) && \WP_CLI ) {
		$progress->finish();
		/**
		 * Undefined type 'WP_CLI'.
		 *
		 * @disregard WP_CLI P1009 Undefined type 'WP_CLI'.
		 */
		\WP_CLI::line( 'Exported ' . $total_block_count . ' blocks' );
	}
}

/**
 * Register the xd build command.
 */
function xd_add_wp_cli_build_command() {
	if ( ! class_exists( 'WP_CLI' ) ) {
		return;
	}

	/**
	 * Undefined type 'WP_CLI'.
	 *
	 * @disregard WP_CLI P1009 Undefined type 'WP_CLI'.
	 */
	\WP_CLI::add_command( 'xd build-blocks-manifest', __NAMESPACE__ . '\xd_save_block_type_metadata_collections' );
}

add_action( 'cli_init', __NAMESPACE__ . '\xd_add_wp_cli_build_command' );

/**
 * Merge default attributes for one feature into $block_settings['attributes'].
 * - Flattens possible tokens from block supports (ignores `when`)
 * - If wildcard: all attribute `if` pass
 * - Else: include an attribute only if its `if` tokens intersect the possible set
 * - array_replace_recursive keeps unspecified default fields while honoring overrides
 * - Strips editor-only keys like 'if'/'when'
 *
 * @param array  $block_settings the block settings to merge.
 * @param string $feature_key the feature key to merge.
 */
function xd_register_feature_attributes( array $block_settings, string $feature_key ): array {
	if ( empty( $block_settings['supports'][ $feature_key ] ) ) {
		return $block_settings;
	}

	$collected    = xd_collect_possible_tokens( $block_settings['supports'][ $feature_key ] );
	$possible     = $collected['tokens'];
	$wildcard     = $collected['wildcard'];
	$possible_set = array_flip( $possible );

	// Defaults for this feature: attrName => full schema (not just type/default).
	$defaults = xd_get_default_block_settings( $feature_key );
	if ( empty( $defaults ) || ! is_array( $defaults ) ) {
		return $block_settings;
	}

	$final = $block_settings['attributes'] ?? array();

	foreach ( $defaults as $attr_name => $default_schema ) {
		$if_tokens = isset( $default_schema['if'] ) ? xd_attr_if_tokens( $default_schema['if'] ) : array();

		$allowed = empty( $if_tokens ) || $wildcard;
		if ( ! $allowed ) {
			foreach ( $if_tokens as $tok ) {
				if ( isset( $possible_set[ $tok ] ) ) {
					$allowed = true;
					break;
				}
			}
		}
		if ( ! $allowed ) {
			unset( $final[ $attr_name ] );
			continue;
		}

		// Block-level override (full shape).
		$override = ( isset( $final[ $attr_name ] ) && is_array( $final[ $attr_name ] ) ) ? $final[ $attr_name ] : array();

		// Strip editor-only keys from both sides, keep everything else.
		unset( $default_schema['if'], $default_schema['when'] );
		unset( $override['if'], $override['when'] );

		// Keep full schema so renderer has cssVariable/cssTransform/className/etc.
		$final[ $attr_name ] = array_replace_recursive( $default_schema, $override );
	}

	$block_settings['attributes'] = $final;
	return $block_settings;
}


/**
 * Retrieves block settings
 *
 * @param mixed $name the block name or null to retrieve all defaults.
 * @return array the block defaults.
 */
function xd_get_block_settings( $name = null ) {

	global $xd_block_settings;

	// Ensure that $xd_block_settings is initialized.
	// This is for the benefit of older themes which define this variable globally.
	// in certain situations, like when running through wp_cli, the theme may not have setup $xd_block_settings
	// which will result in a fatal error when the xd_block_settings filter is applied.
	if ( empty( $xd_block_settings ) ) {
		$xd_block_settings = array();
	}

		$block_settings_all = ! empty( $name ) ?
		array( $name => XD_Block_Metadata_Registry::get_block_type( $name ) ) :
		XD_Block_Metadata_Registry::get_all_block_types();

	$widget_block_overrides = array();

	if ( function_exists( 'get_current_screen' ) ) {
		$current_screen = \get_current_screen();
		if ( isset( $current_screen->id ) ) {
			if ( 'widgets' === $current_screen->id || 'customize' === $current_screen->id ) {
				$widget_block_overrides = xd_get_widget_override_json();
			}
		}
	}

	// themes <= version 2.4.3 add to this filter, with the block override / auto register settings only.
	$block_settings_all = apply_filters( 'xd_block_settings', $block_settings_all );

	$widget_block_overrides = apply_filters( 'xd_widget_block_overrides', $widget_block_overrides );

	foreach ( $block_settings_all as $block_name => &$block_settings ) {

		if ( empty( $block_settings['name'] ) ) {
			$block_settings['name'] = $block_name;
		}

		if ( ! empty( $block_settings['viewStyle'] ) ) {
			$block_settings['view_style_handles'] = (array) $block_settings['viewStyle'];
		}
		if ( ! empty( $block_settings['editorStyle'] ) ) {
			$block_settings['editor_style_handles'] = (array) $block_settings['editorStyle'];
		}
		if ( ! empty( $block_settings['editorScript'] ) ) {
			$block_settings['editor_script_handles'] = (array) $block_settings['editorScript'];
		}
		if ( ! empty( $block_settings['viewScript'] ) ) {
			$block_settings['view_script_handles'] = (array) $block_settings['viewScript'];
		}
		if ( isset( $block_settings['usesContext'] ) ) {
			$block_settings['uses_context'] = $block_settings['usesContext'];
			unset( $block_settings['usesContext'] );
		}

		if ( ! empty( $block_settings['providesContext'] ) ) {
			$block_settings['provides_context'] = $block_settings['providesContext'];
			unset( $block_settings['providesContext'] );
		}

			$block_settings = apply_filters( 'xd_init_block_settings', $block_settings );
			$block_settings = apply_filters( 'xd_init_block_settings_' . $block_name, $block_settings );

		if ( strpos( $block_name, 'xd/' ) !== false ) {
			$block_settings['api_version'] = isset( $block_settings['api_version'] ) ? $block_settings['api_version'] : 2;
		}

		if ( ! isset( $block_settings['uses_context'] ) ) {
			$block_settings['uses_context'] = array();
		}

		if ( ! in_array( 'xd/context', $block_settings['uses_context'], true ) ) {
			$block_settings['uses_context'][] = 'xd/context';
		}

		if ( empty( $block_settings['supports'] ) ) {
			$block_settings['supports'] = array();
		}

		if ( empty( $block_settings['attributes'] ) ) {
			$block_settings['attributes'] = array();
		}

			$block_settings['supports']['lock'] = false;

		// Custom property at root is deprecated, and has been moved to the supports array.
		if ( isset( $block_settings['custom'] ) ) {
			$block_settings['supports']['custom'] = $block_settings['custom'];
		}

		foreach ( array_keys( $block_settings['supports'] ?? array() ) as $feature_key ) {
				$block_settings = xd_register_feature_attributes( $block_settings, $feature_key );
		}

		if ( empty( $block_settings['attributes'] ) ) {
			unset( $block_settings['attributes'] );
		}

		if ( isset( $widget_block_overrides[ $block_name ] ) ) {
			$block_settings = array_replace_recursive( $block_settings, $widget_block_overrides[ $block_name ] );
		}

		$block_settings = array_filter( $block_settings );
	}

	if ( ! empty( $name ) ) {

		if ( ! empty( $block_settings_all[ $name ] ) ) {
			return $block_settings_all[ $name ];
		}
		return array();
	}

	return $block_settings_all;

}


/**
 *
 * For backward compatibility only. This filter is deprecated.
 *
 * In this filter, WordPress conditionals are available.
 * This gives us the opportunity to modify the block settings
 * based on the current queried object via the
 * xd_block_settings filter.
 *
 * This is not aligned with WordPress' block registration process.
 * Block settings should be consistent site wide.
 *
 * Client side strategies should be used to selectively consume
 * server side block settings if needed.
 *
 * @deprecated
 */
function xd_get_block_setting_back_compat() {

	remove_filter( 'xd_block_settings', __NAMESPACE__ . '\xd_patch_block_parents', 100 );

	if ( ! has_filter( 'xd_block_settings' ) ) {
		// No need to run this filter if it is not registered.
		return;
	}
	remove_filter( 'register_block_type_args', __NAMESPACE__ . '\xd_filter_block_type_args', 10, 2 );

	$registry   = WP_Block_Type_Registry::get_instance();
	$reflection = new \ReflectionClass( 'WP_Block_Type' );
	$properties = array_column( $reflection->getProperties(), 'name' );

	$block_settings_all = xd_get_block_settings();

	foreach ( $registry->get_all_registered() as $block_name => $registered_block ) {

		if ( str_starts_with( $block_name, 'core/' ) ) {
			continue;
		}

		if ( ! array_key_exists( $block_name, $block_settings_all ) ) {
			// Nothing to filter.
			continue;
		}

		$registered_block_settings = array();
		foreach ( $properties as $property ) {
			if ( ! empty( $registered_block->$property ) ) {
				// Some WP_Block_Type properties are private and have getters so we can't use get_object_vars.
				// We need to use the getter method via reflection.
				$registered_block_settings[ $property ] = $registered_block->{$property};
			}
		}

		$registry->unregister( $block_name );
		// re-register via xd_block_settings filter (filter is applied in xd_get_block_settings).
		$registry->register( $block_name, array_replace_recursive( $registered_block_settings, $block_settings_all[ $block_name ] ) );
	}
}

if ( xd_theme_version_compare( '<', '2.7.0' ) ) {
	add_action( 'template_redirect', __NAMESPACE__ . '\xd_get_block_setting_back_compat', -10 );
	add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\xd_get_block_setting_back_compat', -10 );
}


/**
 * Filter block type arguments.
 *
 * @param array  $block_type Block type.
 * @param string $block_name Block name.
 */
function xd_filter_block_type_args( $block_type, $block_name ) {
	$block_settings = xd_get_block_settings( $block_name );

	$block_type = array_replace_recursive( $block_type, $block_settings );
	return $block_type;
}

add_filter( 'register_block_type_args', __NAMESPACE__ . '\xd_filter_block_type_args', 10, 2 );

/**
 * Register blocks.
 */
function xd_register_blocks() {
	$collection = XD_Block_Metadata_Registry::get_collection( 'xd' );
	if ( ! empty( $collection ) ) {
		$block_types = $collection['block_types'];
		$path        = dirname( $collection['path'] );
		$block_names = array_keys( $block_types );
		foreach ( $block_names as $block_name ) {
			if ( str_starts_with( $path, 'block:' ) ) {
				register_block_type( $block_name );
			} else {
				register_block_type_from_metadata( $path . '/' . $block_name );
			}
		}
	}

	// In 2.4.3 and earlier, blocks did not have individual json files. Blocks to be registered were
	// defined in the block-settings.php file or in the block-settings.json file, and loaded with xd_get_block_settings().
	// This will also register blocks that are not in the xd collection and/or have not been registered yet.
	$registry       = WP_Block_Type_Registry::get_instance();
	$block_settings = xd_get_block_settings();
	foreach ( $block_settings as $block_name => $block_settings ) {
		if ( ! $registry->is_registered( $block_name ) ) {
			$registry->register( $block_name, $block_settings );
		}
	}
}

add_action( 'init', __NAMESPACE__ . '\xd_register_blocks' );

/**
 * Register block collections.
 *
 * @return void
 */
function xd_register_block_collections() {
	$manifest_files = glob( get_stylesheet_directory() . '/build/blocks/*/blocks-json.php' );
	if ( ! empty( $manifest_files ) ) {
		foreach ( $manifest_files as $manifest ) {
			$path = dirname( $manifest );
			wp_register_block_metadata_collection( $path, $manifest );
			XD_Block_Metadata_Registry::register( $path, $manifest );
		}
	} else {
		$collections = xd_get_block_type_metadata_collections();
		foreach ( $collections as $path => $blocks ) {
			XD_Block_Metadata_Registry::register( 'block:./' . $path, $blocks );
		}
	}
}

// Collections are registered early (priority 1) so that they are available in the above filters.
// ACF blocks that are are registered using the classic method (acf_register_block_type) will be
// registered at priority 5 (acf/init).
// Core blocks are registered at priority 10.
// xd prefixed blocks are registered at priority 10 (xd_register_blocks) above.
add_action( 'init', __NAMESPACE__ . '\xd_register_block_collections', 1 );

/**
 * Retrieves setting defaults from block-settings.php.
 *
 * @param array                   $categories Block Categories.
 * @param WP_Block_Editor_Context $block_editor_context the setting defaults.
 */
function xd_filter_block_categories_all( $categories, $block_editor_context ) {
	return array_merge(
		$categories,
		array(
			array(
				'slug'  => 'standard',
				'title' => __( 'Standard', 'XD' ),
			),
			array(
				'slug'  => get_stylesheet(),
				'title' => get_bloginfo( 'name' ),
			),
		)
	);
}

add_filter( 'block_categories_all', __NAMESPACE__ . '\xd_filter_block_categories_all', 10, 2 );


/**
 * Ensure List items are supported where there are lists.
 *
 * @param Array $allowed_block_types allowed block types.
 */
function xd_filter_allowed_blocks_all( $allowed_block_types ) {

	if ( ! is_array( $allowed_block_types ) ) {
		return $allowed_block_types;
	}

	if ( in_array( 'core/list', $allowed_block_types, true ) && ! in_array( 'core/list-item', $allowed_block_types, true ) ) {
		$allowed_block_types[] = 'core/list-item';
	}

	return $allowed_block_types;
}

// priority 20 so that the theme can setup its allowed blocks first.
add_filter( 'allowed_block_types_all', __NAMESPACE__ . '\xd_filter_allowed_blocks_all', 20 );

/**
 * Patch block parents.
 * In WordPress 6.8, Block parents need to be explicitly set.
 * For example, if a xd/column block is allowed inside a xd/columns block and a xd/cta block,
 * it is not enough to set the parent of the xd/column block to ['xd/columns'].
 * It needs to be set to ['xd/columns', 'xd/cta'].
 * This function will inspect each block type's allowed blocks and set the parent of said block types
 * when the parent is set but does not include the current block type.
 *
 * @param array $settings Block settings.
 */
function xd_patch_block_parents( $settings ) {
	static $xd_block_settings      = array();
	static $xd_block_parents_store = array();
	if ( empty( $xd_block_settings ) ) {
		// We need to remove the filter temporarily to avoid an infinite loop.
		remove_filter( 'xd_block_settings', __NAMESPACE__ . '\xd_patch_block_parents', 100 );
		$xd_block_settings = xd_get_block_settings();
		add_filter( 'xd_block_settings', __NAMESPACE__ . '\xd_patch_block_parents', 100 );
	}
	foreach ( $xd_block_settings as $block_name => $block_settings ) {
		if ( ! empty( $block_settings['supports']['custom']['innerBlocks'] ) ) {
			foreach ( $block_settings['supports']['custom']['innerBlocks'] as $inner_blocks_settings ) {
				if ( ! empty( $inner_blocks_settings['allowedBlocks'] ) && is_array( $inner_blocks_settings['allowedBlocks'] ) ) {
					foreach ( $inner_blocks_settings['allowedBlocks'] as $allowed_block ) {
						if ( ! array_key_exists( $allowed_block, $xd_block_parents_store ) ) {
							$xd_block_parents_store[ $allowed_block ] = array();
						}
						if ( ! in_array( $block_name, $xd_block_parents_store[ $allowed_block ], true ) ) {
							$xd_block_parents_store[ $allowed_block ][] = $block_name;
						}
					}
				}
			}
		}
	}

	foreach ( $settings as $block_name => $block_settings ) {
		if ( ! empty( $block_settings['parent'] ) ) {
			if ( ! empty( $xd_block_parents_store[ $block_name ] ) ) {
				foreach ( $xd_block_parents_store[ $block_name ] as $parent_block ) {
					if ( ! in_array( $parent_block, $block_settings['parent'], true ) ) {
						$settings[ $block_name ]['parent'][] = $parent_block;
					}
				}
			}
		}
	}

	return $settings;
}


add_filter( 'xd_block_settings', __NAMESPACE__ . '\xd_patch_block_parents', 100 );
// Priority 100 so that the patch is applied after the block settings are merged.

