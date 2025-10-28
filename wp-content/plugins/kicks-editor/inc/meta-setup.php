<?php
/**
 * Meta setup.
 *
 * @package Leap Editor
 */

namespace Leap\Editor\Meta_Setup;

/**
 * Fetches the plugin data from local json files.
 */
function xd_get_plugin_json() {

	$plugin_json = wp_cache_get( 'xd-plugin-json' );
	if ( false !== $plugin_json ) {
		return $plugin_json;
	}
	$plugin_json = array();

	$xd_plugin_json_folders = array(
		'theme' => realpath( get_template_directory() . '/editor/plugins' ),
	);

	$xd_plugin_json_folders = apply_filters( 'xd_plugin_json_folders', $xd_plugin_json_folders );

	foreach ( $xd_plugin_json_folders as $xd_plugin_json_folder ) {
		foreach ( array_merge(
			glob( $xd_plugin_json_folder . '/**/index.json' ),
			glob( $xd_plugin_json_folder . '/*.json' )
		)
			as $plugin_file
		) {
			$plugin = wp_json_file_decode( $plugin_file, array( 'associative' => true ) );
			if ( ! empty( $plugin['name'] ) ) {
				$plugin_json[ $plugin['name'] ] = $plugin;
			}
		}
	}

	$plugin_json = apply_filters( 'xd_plugin_json', $plugin_json );

	$plugin_json = array_values( $plugin_json );

	wp_cache_set( 'xd-plugin-json', $plugin_json );

	return $plugin_json;
}


/**
 * Registers plugin post meta fields
 */
function xd_register_custom_post_meta_fields() {

	$should_register_field = function ( $locations, $post_type ) {
		$included_post_types = array();
		$excluded_post_types = array();
		$page_type_implied   = false;
		foreach ( $locations as $location ) {
			$params = array_column( $location, 'param' );
			foreach ( $location as $loc ) {
				// We can only safely include or exclude by post type when there are no runtime params present.
				if ( 1 === count( $params ) && 1 === count( $locations ) ) {
					if ( 'post_type' === $loc['param'] ) {
						if ( '==' === $loc['operator'] ) {
							$included_post_types[] = $loc['value'];
						} else {
							$excluded_post_types[] = $loc['value'];
						}
					}
				}
				if ( 'post_state' === $loc['param'] || 'page_template' === $loc['param'] ) {
					if ( '==' === $loc['operator'] ) {
						$page_type_implied = true;
					}
				}
			}
		}
		if ( ! empty( $included_post_types ) ) {
			return in_array( $post_type, $included_post_types, true );
		}
		if ( ! empty( $excluded_post_types ) ) {
			return ! in_array( $post_type, $excluded_post_types, true );
		}
		if ( 'page' !== $post_type && $page_type_implied ) {
			return false;
		}
		return true;
	};

	$plugins = xd_get_plugin_json( true );

	$exclude_post_types = array(
		'attachment',
		'revision',
		'nav_menu_item',
		'custom_css',
		'customize_changeset',
		'oembed_cache',
		'user_request',
		'wp_block',
		'wp_template',
		'wp_template_part',
		'wp_global_styles',
		'wp_font_family',
		'wp_font_face',
		'wp_navigation',
		'acf-field-group',
		'acf-field',
		'acf-post-type',
		'acf-taxonomy',
	);
	$user_post_types    = array_values( array_diff( get_post_types(), $exclude_post_types ) );

	foreach ( $user_post_types as $user_post_type ) {
		foreach ( $plugins as $plugin ) {
			if ( empty( $plugin['fieldGroups'] ) ) {
				continue;
			}
			$locations = ! empty( $plugin['locations'] ) ? $plugin['locations'] : array();
			if ( ! $should_register_field( $locations, $user_post_type ) ) {
				continue;
			}
			foreach ( $plugin['fieldGroups'] as $field_group ) {
				if ( empty( $field_group['fields'] ) ) {
					continue;
				}
				$locations = ! empty( $field_group['locations'] ) ? $field_group['locations'] : array();
				if ( ! $should_register_field( $locations, $user_post_type ) ) {
					continue;
				}
				foreach ( $field_group['fields'] as $field ) {
					if ( empty( $field['name'] ) || empty( $field['schema'] ) ) {
						continue;
					}
					$locations = ! empty( $field['locations'] ) ? $field['locations'] : array();
					if ( ! $should_register_field( $locations, $user_post_type ) ) {
						continue;
					}
					$name   = $field['name'];
					$schema = $field['schema'];
					$type   = isset( $schema['type'] ) ? $schema['type'] : 'string';
					if ( 'string' === $type && ! isset( $schema['default'] ) ) {
						$schema['default'] = '';
					}
					if ( 'object' === $schema['type'] && empty( $schema['properties'] ) ) {
						$schema['additionalProperties'] = true;
					}
					if ( 'array' === $schema['type']
						&& ! empty( $schema['items']['type'] )
						&& 'object' === $schema['items']['type']
						&& empty( $schema['items']['properties'] ) ) {
						$schema['items']['additionalProperties'] = true;
					}
					$meta_settings = array(
						'single'       => true,
						'type'         => $type,
						'show_in_rest' => array(
							'schema' => $schema,
						),
					);
					if ( isset( $schema['default'] ) ) {
						$meta_settings['default']                 = $schema['default'];
						$meta_settings['show_in_rest']['default'] = $schema['default'];
					}
					register_post_meta(
						$user_post_type,
						$name,
						$meta_settings
					);
				}
			}
		}
	}
}

add_action( 'rest_api_init', __NAMESPACE__ . '\xd_register_custom_post_meta_fields' );
if ( xd_theme_version_compare( '>=', '2.5.3' ) ) {
	// We should have been doing this all along, but we'll introduce it in 2.5.3 so as not to
	// introduce unexpected behavior in earlier theme versions.
	// It makes it so that calls to get_post_meta will return the same value as
	// the rest api, and the default value, if no value is set.
	add_action( 'after_setup_theme', __NAMESPACE__ . '\xd_register_custom_post_meta_fields' );
}

/**
 * Appends the default value to the meta value if the meta value is an array.
 *
 * @param mixed  $check the meta value - short circuits meta data when not null.
 * @param int    $object_id the post id.
 * @param string $meta_key the meta key.
 * @param bool   $single whether the meta value is a single value.
 * @param string $meta_type the meta type.
 */
function xd_merge_meta_value_defaults( $check, $object_id, $meta_key, $single, $meta_type ) {
	if ( $single || 'post' !== $meta_type ) {
		return $check;
	}

	$appended_fields = false;

	// otherwise, we'll get an infinite loop.
	remove_filter( 'get_post_metadata', __NAMESPACE__ . '\xd_merge_meta_value_defaults', 10, 5 );
	// get the actual meta value.
	$meta = get_metadata( 'post', $object_id, $meta_key );
	// re-add the filter.
	add_filter( 'get_post_metadata', __NAMESPACE__ . '\xd_merge_meta_value_defaults', 10, 5 );
	if ( ! empty( $meta_key ) ) {
		if ( isset( $meta[0] ) ) {
			$value   = maybe_unserialize( $meta[0] );
			$default = get_metadata_default( $meta_type, $object_id, $meta_key, true );
			if ( xd_is_associative_array( $value ) && xd_is_associative_array( $default ) ) {
				$meta[0]         = array_replace_recursive( $default, $value );
				$appended_fields = true;
			}
		}
	} else {
		foreach ( $meta as $key => $value ) {
			if ( isset( $value[0] ) ) {
				$value   = maybe_unserialize( $value[0] );
				$default = get_metadata_default( $meta_type, $object_id, $key, true );
				if ( xd_is_associative_array( $value ) && xd_is_associative_array( $default ) ) {
					$meta[ $key ][0] = array_replace_recursive( $default, $value );
					$appended_fields = true;
				}
			}
		}
	}
	return $appended_fields ? $meta : $check;
}

add_filter( 'get_post_metadata', __NAMESPACE__ . '\xd_merge_meta_value_defaults', 10, 5 );

