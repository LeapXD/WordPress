<?php
/**
 * Editor setup.
 *
 * @package Leap Editor
 */

/**
 * Avoid collisions with function names in theme.
 */
namespace Leap\Editor\Block_Render;

use \Timber\Timber;
use WP_Block;
use WP_Block_Supports;
use XD\Types\XD_Block_Wrap;
use XD\Types\XD_Post;


/**
 * Removes render callbacks registered by the theme
 * The block render filters (below) were moved to the plugin
 * for ease of patching in case of breaking changes in WordPress.
 *
 * @since 1.3.0
 */
function xd_remove_deprecated_block_filters() {
	remove_filter( 'acf/register_block_type_args', 'xd_filter_acf_block_settings' );
	remove_filter( 'render_block_data', 'xd_append_parent_block_data' );
	remove_filter( 'register_block_type_args', 'xd_filter_block_args' );
}

// Must happen before blocks are registered  in plugin (priority 10).
if ( xd_theme_version_compare( '<=', '2.4.3' ) ) {
	add_action( 'after_setup_theme', __NAMESPACE__ . '\xd_remove_deprecated_block_filters', 5 );
}


/**
 * Creates a prepared "parsed_block" which can be used for rendering.
 *
 * @param array $block The block parameters
 *
 *  - string name           => the block name
 *  - array attributes => the block attributes.
 *  - array inner_blocks    => same as $block[] or array of strings (rendered content). Array can be mixed.
 */
function xd_create_parsed_block( $block = array() ) {

	$block = wp_parse_args(
		$block,
		array(
			'name'         => null,
			'attributes'   => array(),
			'inner_blocks' => array(),
			'context'      => array(),
		)
	);

	if ( isset( $block['name'] ) || is_null( $block['name'] ) ) {
		$block['blockName'] = $block['name'];
	}

	if ( empty( $block['attrs'] ) ) {
		$block['attrs'] = array();
	}

	$block['attrs'] = array_replace_recursive( $block['attrs'], $block['attributes'] );

	if ( ! empty( $block['inner_blocks'] ) && is_array( $block['inner_blocks'] ) ) {
		$block['innerContent'] = array();
		$block['innerBlocks']  = array();
		foreach ( $block['inner_blocks'] as $inner_block ) {
			if ( is_string( $inner_block ) ) {
				$block['innerContent'][] = $inner_block;
			} else {
				$block['innerBlocks'][]  = xd_create_parsed_block( $inner_block );
				$block['innerContent'][] = null;
			}
		}
	}

	if ( empty( $block['innerContent'] ) && ! empty( $block['innerBlocks'] ) ) {
		$block['innerContent'] = array_fill( 0, count( $block['innerBlocks'] ), null );
	}

	unset( $block['name'], $block['attributes'], $block['inner_blocks'] );

	return $block;
}

/**
 * Render a block with additional context
 *
 * @param WP_Block|array|string $block the block, parsed block, or block name.
 * @param array                 $attributes attributes to render block with.
 * @param array                 $context the context to merge.
 */
function xd_render_block( $block, $attributes = array(), $context = array() ) {
	$context = array( 'xd/context' => $context );
	if ( $block instanceof WP_Block ) {
		$context = array_replace_recursive( $block->context, $context );
		$block   = $block->parsed_block;
	}
	if ( is_string( $block ) ) {
		$block = array(
			'blockName' => $block,
			'attrs'     => array(),
		);
	}

	if ( ! is_array( $block ) ) {
		return '';
	}

	if ( isset( $block['name'] ) || isset( $block['attributes'] ) || isset( $block['inner_blocks'] ) ) {
		$block = xd_create_parsed_block( $block );
	}

	if ( ! empty( $block['context'] ) ) {
		$context['xd/context'] = array_replace_recursive( $block['context'], $context['xd/context'] );
	}

	if ( empty( $block['attrs'] ) ) {
		$block['attrs'] = array();
	}

	if ( ! empty( $block['attributes'] ) ) {
		$attributes = array_replace_recursive( $block['attributes'], $attributes );
	}

	$block['attrs'] = array_replace_recursive( $block['attrs'], $attributes );

	$xd_filter_block_data = function( $parsed_block ) {
		if ( ! empty( $parsed_block['attributes'] ) ) {
			$parsed_block['attrs'] = array_replace_recursive( $parsed_block['attrs'], $parsed_block['attributes'] );
		}
		if ( isset( $parsed_block['name'] ) ) {
			$parsed_block['blockName'] = $parsed_block['name'];
		}
		return $parsed_block;
	};

	$xd_filter_block_context = function( $block_context, $parsed_block ) {
		if ( ! empty( $parsed_block['context'] ) ) {
			$context       = array( 'xd/context' => $parsed_block['context'] );
			$block_context = array_replace_recursive( $block_context, $context );
		}
		return $block_context;
	};

	if ( ! apply_filters( 'xd_suppress_block_data_filter', false ) ) {
		add_filter( 'render_block_data', $xd_filter_block_data );
	}
	if ( ! apply_filters( 'xd_suppress_block_context_filter', false ) ) {
		add_filter( 'render_block_context', $xd_filter_block_context, 10, 2 );
	}

	$block           = new WP_Block( $block, $context );
	$block_to_render = \WP_Block_Supports::$block_to_render;
	if ( empty( \WP_Block_Supports::$block_to_render ) ) {
		\WP_Block_Supports::$block_to_render = $block->parsed_block;
	}
	$content                             = $block->render();
	\WP_Block_Supports::$block_to_render = $block_to_render;

	remove_filter( 'render_block_data', $xd_filter_block_data );
	remove_filter( 'render_block_context', $xd_filter_block_context, 10, 2 );

	return $content;
}

/**
 * Get block active variation.
 *
 * @param WP_Block|array|void $block block instance to test.
 */
function xd_get_block_variation( $block = null ) {

	if ( empty( $block ) ) {
		$block = new WP_Block( \WP_Block_Supports::$block_to_render );
	} elseif ( is_array( $block ) && isset( $block['blockName'] ) && isset( $block['attrs'] ) ) {
		$block = new WP_Block( $block );
	}
	if ( ! $block instanceof WP_Block ) {
		return false;
	}
	$registry   = \WP_Block_Type_Registry::get_instance();
	$block_type = $registry->get_registered( $block->name );
	$variations = $block_type->variations;
	foreach ( $variations as $variation ) {
		if ( empty( $variation['isActive'] ) || ! is_array( $variation['isActive'] ) ) {
			continue;
		}
		$match = true;
		foreach ( $variation['isActive'] as $attribute ) {
			if (
				isset( $variation['attributes'][ $attribute ] ) &&
				isset( $block->attributes[ $attribute ] ) &&
				$variation['attributes'][ $attribute ] !== $block->attributes[ $attribute ] ) {
				$match = false;
				break;
			}
		}
		if ( $match ) {
			return (object) $variation;
		}
	}
	return $block;
}

/**
 * Retrieves a block attribute.
 *
 * @param string         $attribute attribute name.
 * @param WP_Block|array $block WordPress block or parsed block attribute name.
 */
function xd_attribute( $attribute, $block = null ) {
	if ( empty( $block ) ) {
		$block = new WP_Block( \WP_Block_Supports::$block_to_render );
	} elseif ( is_array( $block ) && isset( $block['blockName'] ) && isset( $block['attrs'] ) ) {
		$block = new WP_Block( $block );
	}
	if ( ! $block instanceof WP_Block ) {
		return false;
	}
	return isset( $block->attributes[ $attribute ] ) ? $block->attributes[ $attribute ] : null;
}

/**
 * Retrieves a context field.
 *
 * @param string         $field field name.
 * @param WP_Block|array $block WordPress block or parsed block attribute name.
 */
function xd_context( $field, $block = null ) {
	if ( empty( $block ) ) {
		$block = new WP_Block( \WP_Block_Supports::$block_to_render );
	} elseif ( is_array( $block ) && isset( $block['blockName'] ) && isset( $block['attrs'] ) ) {
		$block = new WP_Block( $block );
	}
	if ( ! $block instanceof WP_Block ) {
		return false;
	}
	$block_context = $block->context;
	$xd_context    = ! empty( $block_context['xd/context'] ) ? $block_context['xd/context'] : array();
	return isset( $xd_context[ $field ] ) ? $xd_context[ $field ] : null;
}

/**
 * Retrieves settings from block based on context.
 *
 * @param array          $block_setting_group group.
 * @param WP_Block|array $block WordPress block or parsed block attribute name.
 */
function xd_get_context( $block_setting_group, $block = null ) {
	if ( empty( $block ) ) {
		$block = new WP_Block( \WP_Block_Supports::$block_to_render );
	} elseif ( is_array( $block ) && isset( $block['blockName'] ) && isset( $block['attrs'] ) ) {
		$block = new WP_Block( $block );
	}
	if ( ! $block instanceof WP_Block ) {
		return false;
	}
	$post                    = XD_Post::get_current() ?? get_post();
	$post_type               = ! empty( $post ) ? $post->post_type : null;
	$parent_block            = ! empty( $block->parsed_block['parent']['block'] ) ? $block->parsed_block['parent']['block'] : null;
	$parent                  = ! empty( $parent_block['blockName'] ) ? $parent_block['blockName'] : null;
	$parent_active_variation = xd_get_block_variation( $parent_block );
	$parent_active_variation = ! empty( $parent_active_variation ) ? $parent_active_variation->name : null;
	$active_variation        = xd_get_block_variation( $block );
	$active_variation        = ! empty( $active_variation ) ? $active_variation->name : null;
	$attributes              = ( $block )->attributes;

	$matched_group = array_reduce(
		array_reverse(
			// The client-side version of this function uses array.reduceRight.
			// move fallback context to start.
			array_values(
				// reset array to ascending keys.
				array_filter(
					// remove empty contexts (where attributes don't match).
					array_map(
						// array_map callback.
						function( $settings ) use ( $post_type, $parent, $parent_active_variation, $active_variation, $attributes ) {
							$context                       = ! empty( $settings['context'] ) ? $settings['context'] : false;
							$block_parent                  = ! empty( $context['parent'] ) ? (array) $context['parent'] : array();
							$block_parent_active_variation = ! empty( $context['parentVariation'] ) ? (array) $context['parentVariation'] : array();
							$type                          = ! empty( $context['postType'] ) ? (array) $context['postType'] : array();
							$block_attributes              = ! empty( $context['attributes'] ) ? (array) $context['attributes'] : array();
							$block_variation               = ! empty( $context['variation'] ) ? (array) $context['variation'] : array();
							$score                         = 0;

							if ( ! empty( $block_parent ) ) {
								$score = in_array( $parent, $block_parent, true ) ? $score + 1 : $score - 1;
							}
							if ( ! empty( $block_parent_active_variation ) ) {
								$score = in_array( $parent_active_variation, $block_parent_active_variation, true ) ? $score + 1 : $score - 1;
							}
							if ( ! empty( $block_variation ) ) {
								$score = in_array( $active_variation, $block_variation, true ) ? $score + 1 : $score - 1;
							}
							if ( ! empty( $type ) ) {
								$score = in_array( $post_type, $type, true ) ? $score + 1 : $score - 1;
							}
							if ( $block_attributes ) {
								foreach ( $block_attributes as $key => $val ) {
									if ( $attributes[ $key ] === $val ) {
										$score++;
									} else {
										// if any tested attributes are present, and attribute value doesn't match test, disregard current context.
										return false;
									}
								}
							}
							$settings['score'] = $score;
							return $settings;
						},
						$block_setting_group
					)
				)
			)
		),
		// array_reduce callback.
		function( $prev, $settings ) {
			if ( empty( $prev ) ) {
				return $settings;
			}
			return $settings['score'] > $prev['score'] ? $settings : $prev;
		}
	) ?? array();

	$default_group = array();
	foreach ( $block_setting_group as $group ) {
		if ( isset( $group['isDefault'] ) && $group['isDefault'] ) {
			$default_group = $group;
			break;
		}
	}

	return array_replace( $default_group, $matched_group );
}

/**
 * Get block css variables.
 *
 * @param array $block_type_attributes Block type attributes.
 * @param array $attributes Block attributes.
 */
function get_css_variables( $block_type_attributes, $attributes ) {

	$css_vars = array();

	foreach ( $block_type_attributes as $attribute_name => $attribute_schema ) {
		if ( empty( $attribute_schema['cssVariable'] ) || empty( $attribute_schema['type'] ) ) {
			continue;
		}
		$key      = xd_kebab_case( $attribute_name );
		$prop_key = "--xd-{$key}";
		switch ( $attribute_schema['type'] ) {
			case 'string':
			case 'integer':
			case 'number':
				$value = $attributes[ $attribute_name ];
				if ( ! empty( $attribute_schema['cssTransform'] ) ) {
					$keys                  = array_keys( $attribute_schema['cssTransform'] );
					$values                = array_values( $attribute_schema['cssTransform'] );
					$regexp                = preg_quote( $keys[0], '/' );
					$replace               = $values[0];
					$transformed_value     = preg_replace( "/$regexp/", $replace, $value );
					$css_vars[ $prop_key ] = $transformed_value;
				} else {
					$css_vars[ $prop_key ] = $value;
				}
				break;
			case 'boolean':
				$css_vars[ $prop_key ] = $attributes[ $attribute_name ] ? $attribute_schema['cssVariable'] : false;
				break;
			case 'array':
				$css_vars[ $prop_key ] = implode( ' ', $attributes[ $attribute_name ] );
				break;
			case 'object':
				foreach ( $attributes[ $attribute_name ] as $prop_name => $value ) {
					$key      = xd_kebab_case( $prop_name );
					$prop_key = "$prop_key-$key";
					if ( ! empty( $attribute_schema['cssTransform'][ $prop_name ] ) ) {
						$keys                  = array_keys( $attribute_schema['cssTransform'][ $prop_name ] );
						$values                = array_values( $attribute_schema['cssTransform'][ $prop_name ] );
						$regexp                = preg_quote( $keys[0], '/' );
						$replace               = $values[0];
						$transformed_value     = preg_replace( "/$regexp/", $replace, $value );
						$css_vars[ $prop_key ] = $transformed_value;
					} else {
						$css_vars[ $prop_key ] = $value;
					}
				}
				break;
			default:
		}
	}

	return $css_vars;

}

/**
 * Gets the class names from the block attribute schema based on attribute values and schema settings.
 *
 * @param string         $schema_prop the wrapping element to search for in attributes schema.
 * @param array          $block_type_attributes the block attribute definition.
 * @param array          $attributes the block instance attributes.
 * @param WP_Block|array $block the block.
 */
function xd_get_class_names( $schema_prop, $block_type_attributes, $attributes, $block ) {

	$variation = xd_get_block_variation( $block );
	if ( $variation->name !== $block->name ) {
		$variation = (array) $variation;
		if ( isset( $variation['classNames'] ) ) {
			foreach ( $variation['classNames'] as $attr_name => $attr_val ) {
				if ( isset( $attr_val[ $schema_prop ] ) ) {
					$block_type_attributes[ $attr_name ][ $schema_prop ] = $attr_val[ $schema_prop ];
				}
			}
		};
	}

	$classnames = array();
	foreach ( $block_type_attributes as $attribute_name => $attribute_schema ) {
		if ( ! empty( $attribute_schema[ $schema_prop ] ) && ! empty( $attributes[ $attribute_name ] ) ) {
			if ( ! empty( $attribute_schema['type'] ) && ! empty( $attribute_schema[ $schema_prop ] ) ) {
				$type = $attribute_schema['type'];
				switch ( $type ) {
					case 'string':
					case 'integer':
					case 'number':
					case 'array':
						if ( xd_is_associative_array( $attribute_schema[ $schema_prop ] ) ) {
							if ( isset( $attribute_schema[ $schema_prop ][ $attributes[ $attribute_name ] ] ) ) {
								$classnames[] = xd_classnames( $attribute_schema[ $schema_prop ][ $attributes[ $attribute_name ] ] );
							}
							break;
						}
						$classnames[] = xd_classnames( $attributes[ $attribute_name ] );
						break;
					case 'object':
						if ( true === $attribute_schema[ $schema_prop ] ) {
							$classnames[] = xd_classnames( $attributes[ $attribute_name ] );
							break;
						}
						if ( xd_is_associative_array( $attribute_schema[ $schema_prop ] ) ) {
							$classes = array();
							foreach ( $attributes[ $attribute_name ] as $property_name => $value ) {
								if ( isset( $attribute_schema[ $schema_prop ][ $property_name ] ) ) {
									$classes[] = true === $attribute_schema[ $schema_prop ][ $property_name ] ?
									$value :
									array( ! empty( $attribute_schema[ $schema_prop ][ $property_name ] ) ? $attribute_schema[ $schema_prop ][ $property_name ] : '' => $attributes[ $attribute_name ][ $property_name ] );
								}
							}
							$classnames[] = xd_classnames( $classes );
						}
						break;
					case 'boolean':
						$classnames[] = xd_classnames( array( $attribute_schema[ $schema_prop ] => $attributes[ $attribute_name ] ) );
						break;
					default:
				}
			}
		}
	}
	return xd_classnames( $classnames );

}

/**
 * Merges the wrapper classes, adding classes to be added an removing classes to be removed
 *
 * @param array $wrapper groups of classes to process.
 */
function xd_get_wrapper_classnames( $wrapper ) {
	$class_names = array();
	$to_remove   = array();
	foreach ( $wrapper as $wrap ) {
		if ( false !== $wrap ) {
			if ( empty( $wrap ) ) {
				$wrap = '';
			}
			if ( xd_is_associative_array( $wrap ) ) {
				if ( isset( $wrap['add'] ) ) {
					$class_names = array_merge( $class_names, explode( ' ', $wrap['add'] ) );
				}
				if ( isset( $wrap['remove'] ) ) {
					$to_remove = array_merge( $to_remove, explode( ' ', $wrap['remove'] ) );
				}
			} else {
				$class_names = array_merge( $class_names, explode( ' ', $wrap ) );
			}
		}
	}
	return xd_classnames( array_values( array_diff( $class_names, $to_remove ) ) );
}

/**
 * Remove the default class from xd/ namespaced blocks
 *
 * @param string $classname the original class name.
 * @param string $block_name the block name.
 */
function xd_filter_generated_classname( $classname, $block_name ) {
	if ( false !== strpos( $block_name, 'xd/' ) ) {
		return '';
	}
	return $classname;
}

add_filter( 'block_default_classname', __NAMESPACE__ . '\xd_filter_generated_classname', 10, 2 );

/**
 * Get a blocks wrapper attributes
 *
 * @param WP_Block $block the block.
 */
function xd_get_block_props( $block ) {

	$registry              = \WP_Block_Type_Registry::get_instance();
	$block_settings        = $registry->get_registered( $block->name );
	$block_type_attributes = ! empty( $block_settings->attributes ) ? $block_settings->attributes : array();
	$inner_blocks          = ! empty( $block_settings->supports['custom']['innerBlocks'] ) ?
		$block_settings->supports['custom']['innerBlocks'] :
		array();
	$inner_blocks_setup    = xd_get_context( $inner_blocks, $block );
	$wrap                  = ! empty( $inner_blocks_setup['wrap'] ) ? $inner_blocks_setup['wrap'] : array();

	$attributes = $block->attributes;
	$id         = '';
	$anchor     = '';
	if ( ! empty( $attributes['anchor'] ) ) {
		$anchor = $attributes['anchor'];
	} if ( ! empty( $attributes['id'] ) ) {
		$id = $attributes['id'];
	}
	$css = array();
	if ( ! empty( $attributes['style'] ) ) {
		$css = $attributes['style'];
	}
	$context_class_name                 = xd_context( 'class_name', $block ) ?? '';
	$context_dataset                    = xd_context( 'dataset', $block );
	$variation                          = xd_get_block_variation( $block );
	$class_name                         = ! empty( $attributes['className'] ) ? $attributes['className'] : '';
	$block_class                        = implode( ' ', array_unique( explode( ' ', str_replace( '/', '-', "{$block->name} {$variation->name}" ) ) ) );
	$block_to_render                    = \WP_Block_Supports::$block_to_render;
	WP_Block_Supports::$block_to_render = $block->parsed_block;
	$core_supports                      = WP_Block_Supports::get_instance()->apply_block_supports();
	WP_Block_Supports::$block_to_render = $block_to_render;
	$core_class                         = ! empty( $core_supports['class'] ) ? $core_supports['class'] : '';

	$wrapper_attributes = new XD_Block_Wrap(
		array(
			'block'             => array(
				'class_name' => xd_classnames( array( $block_class, $class_name, $context_class_name, $core_class ) ),
				'id'         => $id,
				'anchor'     => $anchor,
				'dataset'    => $context_dataset,
				'css'        => $css,
				'css_vars'   => get_css_variables( $block_type_attributes, $attributes ),
			),
			'outer'             => array(
				'class_name' => '',
			),
			'inner'             => array(
				'class_name' => '',
			),
			'pre_inner_blocks'  => array(
				'class_name' => '',
			),
			'inner_blocks'      => array(
				'class_name' => '',
			),
			'post_inner_blocks' => array(
				'class_name' => '',
			),
		)
	);

	$wrapper_attributes->block->class_name             = xd_classnames(
		$wrapper_attributes->block->class_name,
		xd_get_class_names( 'className', $block_type_attributes, $attributes, $block )
	);
	$wrapper_attributes->outer->class_name             = xd_classnames(
		$wrapper_attributes->outer->class_name,
		xd_get_class_names( 'outerClassName', $block_type_attributes, $attributes, $block )
	);
	$wrapper_attributes->inner->class_name             = xd_classnames(
		$wrapper_attributes->inner->class_name,
		xd_get_class_names( 'innerClassName', $block_type_attributes, $attributes, $block )
	);
	$wrapper_attributes->pre_inner_blocks->class_name  = xd_classnames(
		$wrapper_attributes->pre_inner_blocks->class_name,
		xd_get_class_names( 'preInnerBlocksClassName', $block_type_attributes, $attributes, $block )
	);
	$wrapper_attributes->inner_blocks->class_name      = xd_classnames(
		$wrapper_attributes->inner_blocks->class_name,
		xd_get_class_names( 'innerBlocksClassName', $block_type_attributes, $attributes, $block )
	);
	$wrapper_attributes->post_inner_blocks->class_name = xd_classnames(
		$wrapper_attributes->post_inner_blocks->class_name,
		xd_get_class_names( 'postInnerBlocksClassName', $block_type_attributes, $attributes, $block )
	);

	if ( true === $wrap ) {
		$wrapper_attributes->inner_blocks->class_name = xd_classnames( $wrapper_attributes->inner_blocks->class_name, str_replace( '/', '-', $block->name ) . '__inner' );
	} elseif ( ! empty( $wrap ) ) {
		foreach ( $wrap as $level => $class_name ) {
			if ( isset( $wrapper_attributes->$level->class_name ) ) {
				$wrapper_attributes->$level->class_name = xd_get_wrapper_classnames(
					array( $class_name, $wrapper_attributes->$level->class_name )
				);
			}
		}
	}

	foreach ( $wrapper_attributes as $level ) {
		$class_names       = explode( ' ', $level->class_name );
		$level->class_name = array_combine( $class_names, $class_names );
	}

	return $wrapper_attributes;
}

/**
 * Gets a cached list of folders to search templates and context files.
 * By caching the results, we can improve performance by excluding folders
 * that we've already determined not to exist.
 *
 * @param string $group the cache group to store / check.
 */
function xd_get_block_files( $group ) {

	$stylesheet_directory = get_template_directory();

	$folder = null;
	if ( 'block_template' === $group ) {
		$folder = $stylesheet_directory . '/views/blocks/';
		if ( xd_theme_version_compare( '<=', '2.4.3' ) ) {
			$folder = $stylesheet_directory . '/views/dynamic-blocks/';
		}
	} elseif ( 'block_context' === $group ) {
		$folder = $stylesheet_directory . '/template-context/blocks/';
		if ( xd_theme_version_compare( '<=', '2.4.3' ) ) {
			$folder = $stylesheet_directory . '/timber-context/dynamic-blocks/';
		}
	} elseif ( 'acf_block_template' === $group ) {
		$folder = $stylesheet_directory . '/views/blocks/';
		if ( xd_theme_version_compare( '<=', '2.4.3' ) ) {
			$folder = $stylesheet_directory . '/views/acf-blocks/';
		}
	} elseif ( 'acf_block_context' === $group ) {
		$folder = $stylesheet_directory . '/template-context/blocks/';
		if ( xd_theme_version_compare( '<=', '2.4.3' ) ) {
			$folder = $stylesheet_directory . '/timber-context/acf-blocks/';
		}
	}

	$cache = wp_cache_get( 'xd_template_folders', $group );
	if ( $cache ) {
		return $cache;
	}

	$folders = apply_filters( 'xd_template_folders', array( $folder ), $group );
	$cache   = array();

	foreach ( $folders as $template_folder ) {
		if ( is_dir( $template_folder ) ) {
			if ( 'block_context' === $group || 'acf_block_context' === $group ) {
				$cache = array_merge( $cache, xd_file_search( $template_folder, '/.*\.php/' ) );
			} else {
				$cache = array_merge( $cache, xd_file_search( $template_folder, '/.*\.twig/' ) );

			}
		}
	}
	wp_cache_set( 'xd_template_folders', $cache, $group );

	return $cache;
}

/**
 * Filters block registration args, creating a render function if template file exists
 * Creates and passes in the twig context to twig templates.
 *
 * @since 1.3.0
 *
 * @param array  $args the original arguments.
 * @param string $name the block name.
 * @return array the modified arguments.
 */
function xd_filter_block_args( $args, $name ) {

	$template  = str_replace( 'xd/', '', $name );
	$parts     = explode( '/', $name );
	$parts     = array_reverse( $parts );
	$file      = $parts[0];
	$namespace = $parts[1];

	if ( 'acf/' === substr( $template, 0, 4 ) || is_admin() ) {
		return $args;
	}
	$found_context_files = wp_cache_get( 'xd_context_files', $name );
	$found_template_file = wp_cache_get( 'xd_template_file', $name );

	if ( false === $found_context_files ) {
		$found_context_files = array();
		$context_files       = xd_get_block_files( 'block_context' );
		if ( ! empty( $context_files ) ) {
			foreach ( $context_files as $found_context_file ) {
				$context_parts     = explode( DIRECTORY_SEPARATOR, $found_context_file );
				$context_parts     = array_reverse( $context_parts );
				$context_file_name = $context_parts[0];
				$context_namespace = $context_parts[1];
				if ( $context_file_name === $file . '.php' &&
					( $context_namespace === $namespace ||
						( 'xd' === $namespace &&
							( 'blocks' === $context_namespace || 'dynamic-blocks' === $context_namespace || 'parts' === $context_namespace )
						)
					)
				) {
					$found_context_files[] = $found_context_file;
				}
			}
		}
		wp_cache_set( 'xd_context_files', $found_context_files, $name );
	}

	if ( false === $found_template_file ) {
		$template_files = xd_get_block_files( 'block_template' );
		if ( ! empty( $template_files ) ) {
			foreach ( $template_files as $template_file ) {
				$template_parts     = explode( DIRECTORY_SEPARATOR, $template_file );
				$template_parts     = array_reverse( $template_parts );
				$template_file_name = $template_parts[0];
				$file_namespace     = $template_parts[1];
				if ( $template_file_name === $file . '.twig' &&
					( $file_namespace === $namespace ||
						( 'xd' === $namespace &&
							( 'blocks' === $file_namespace || 'dynamic-blocks' === $file_namespace || 'parts' === $file_namespace )
						)
					)
				) {
					$found_template_file = true;
					wp_cache_set( 'xd_template_file', true, $name );
					break;
				}
			}
		}
	}
	if ( empty( $found_template_file ) ) {
		wp_cache_set( 'xd_template_file', null, $name );
	}

	$original_callback = $args['render_callback'];

	if ( $found_template_file || ! empty( $found_context_files ) ) {
		$args['render_callback'] = function(
		$attributes,
		$content,
		$block
		) use (
			$template,
			$found_context_files,
			$original_callback
			) {

			if ( class_exists( 'Timber\Timber' ) ) {
				$context = Timber::context();

				$parent          = ! empty( $block->parsed_block['parent'] ) ? $block->parsed_block['parent'] : array();
				$parent['block'] = ! empty( $parent['block'] ) ? new WP_Block( $parent['block'] ) : null;
				$inner_blocks    = ! empty( $block->inner_blocks ) ? $block->inner_blocks : array();
				$wrap            = xd_get_block_props( $block );
				$block_variation = xd_get_block_variation( $block );
				$variation       = $block_variation->name;
				$block_context   = $block->context;
				$xd_context      = ! empty( $block_context['xd/context'] ) ? $block_context['xd/context'] : array();

				if ( xd_theme_version_compare( '<=', '2.4.3' ) ) {
					$class_name              = implode( ' ', $wrap->block->class_name );
					$props                   = $attributes;
					$context['block']        = $block;
					$context['className']    = $class_name;
					$context['content']      = $content;
					$context['innerBlocks']  = $inner_blocks;
					$context['parent']       = $parent;
					$context['props']        = $props;
					$context['blockContext'] = $block_context;

				} else {
					$original_callback       = $original_callback;
					$block_props             = &$wrap->block;
					$props                   = &$attributes;
					$context['attributes']   = &$attributes;
					$context['props']        = &$attributes;
					$context['block']        = &$block;
					$context['blockProps']   = &$block_props;
					$context['content']      = &$content;
					$context['innerBlocks']  = &$inner_blocks;
					$context['parent']       = &$parent;
					$context['blockContext'] = &$block_context;
					$context['xdContext']    = &$xd_context;
					$context['variation']    = &$variation;
					$context['wrap']         = &$wrap;
				}

				$templates = array_values(
					array_unique(
						array(
							str_replace( 'xd/', '', "blocks/$variation.twig" ),
							"blocks/$template.twig",
							str_replace( 'xd/', '', "blocks/parts/$variation.twig" ),
							"blocks/parts/$template.twig",
							str_replace( 'xd/', '', "dynamic-blocks/$variation.twig" ),
							"dynamic-blocks/$template.twig",
						)
					)
				);

				$templates = array_combine( $templates, $templates );

				if ( ! empty( $found_context_files ) ) {
					if ( xd_theme_version_compare( '<', '2.5.3' ) ) {
						$found_context_files = array( $found_context_files[0] );
					}
					foreach ( $found_context_files as $context_file ) {
						include $context_file;
					}
				}

				$templates = (array) $templates;

				if ( filter_input( INPUT_GET, 'editor' ) ) {

					$check = implode( '', $templates );

					if ( ! str_contains( $check, 'blocks-serverside' ) ) {
						$templates = array_merge(
							array(
								str_replace( 'xd/', '', "blocks-serverside/$variation.twig" ),
								"blocks-serverside/$template.twig",
								str_replace( 'xd/', '', "blocks-serverside/parts/$variation.twig" ),
								"blocks-serverside/parts/$template.twig",
							),
							$templates,
						);
					}
				}

				if ( empty( $templates ) ) {
					return '';
				}
				foreach ( $wrap as $level ) {
					if ( isset( $level->class_name ) ) {
						$level->class_name = implode( ' ', array_unique( $level->class_name ) );
					}
				}
				return Timber::compile( $templates, $context );

			} else {
				throw new \Exception( "Tried to load twig template for block $template but Timber is not activated" );
			}
		};
	} elseif ( is_file( get_template_directory() . "/template-parts/dynamic-blocks/$template.php" ) ) {
		$args['render_callback'] = function( $attributes, $content, $block ) use ( $template ) {
			include get_template_directory() . "/template-parts/dynamic-blocks/$template.php";
		};
	}
	return $args;
}
add_filter( 'register_block_type_args', __NAMESPACE__ . '\xd_filter_block_args', 10, 2 );

/**
 * Filters ACF block registration args, creating a render function if template file exists
 * Creates and passes in the twig context to twig templates.
 *
 * @since 1.3.0
 *
 * @param array $args the original arguments.
 * @return array the modified arguments.
 */
function xd_filter_acf_block_settings( $args ) {

		$args['render_callback'] = function( $attributes, $content, $is_preview, $post_id, $block, $block_context ) use ( $args ) {
			$template  = str_replace( 'acf/', '', $args['name'] );
			$parts     = explode( '/', $args['name'] );
			$parts     = array_reverse( $parts );
			$file      = $parts[0];
			$namespace = $parts[1];

			if ( is_admin() ) {
				return $args;
			}
			$context_file        = wp_cache_get( 'xd_acf_context_file', $template );
			$found_template_file = wp_cache_get( 'xd_acf_template_file', $template );

			$context_file        = wp_cache_get( 'xd_acf_context_file', $template );
			$found_template_file = wp_cache_get( 'xd_acf_template_file', $template );

			if ( false === $context_file ) {

				$context_files = xd_get_block_files( 'acf_block_context' );
				if ( ! empty( $context_files ) ) {
					foreach ( $context_files as $found_context_file ) {
						$context_parts     = explode( DIRECTORY_SEPARATOR, $found_context_file );
						$context_parts     = array_reverse( $context_parts );
						$context_file_name = $context_parts[0];
						$context_namespace = $context_parts[1];
						if ( $context_file_name === $file . '.php' &&
							( $context_namespace === $namespace ||
								( 'acf' === $namespace &&
									( 'blocks' === $context_namespace || 'acf-blocks' === $context_namespace || 'parts' === $context_namespace )
								)
							)
						) {
							$context_file = $found_context_file;
							wp_cache_set( 'xd_acf_context_file', $found_context_file, $template );
							break;
						}
					}
				}
			}
			if ( empty( $context_file ) ) {
				wp_cache_set( 'xd_acf_context_file', null, $template );
			}

			if ( false === $found_template_file ) {
				$template_files = xd_get_block_files( 'acf_block_template' );
				if ( ! empty( $template_files ) ) {
					foreach ( $template_files as $template_file ) {
						$template_parts     = explode( DIRECTORY_SEPARATOR, $template_file );
						$template_parts     = array_reverse( $template_parts );
						$template_file_name = $template_parts[0];
						$file_namespace     = $template_parts[1];
						if ( $template_file_name === $file . '.twig' &&
							( $file_namespace === $namespace ||
								( 'acf' === $namespace &&
									( 'blocks' === $file_namespace || 'acf-blocks' === $file_namespace || 'parts' === $file_namespace )
								)
							)
						) {
							$found_template_file = true;
							wp_cache_set( 'xd_acf_template_file', true, $template );
							break;
						}
					}
				}
			}
			if ( empty( $found_template_file ) ) {
				wp_cache_set( 'xd_acf_template_file', null, $template );
			}

			$attributes['className'] = ! empty( $attributes['className'] ) ? $attributes['className'] : '';
			if ( $found_template_file ) {
				if ( class_exists( 'Timber\Timber' ) ) {
					$context               = Timber::context();
					$parent                = ! empty( $block->parsed_block['parent'] ) ? $block->parsed_block['parent'] : array();
					$parent['block']       = ! empty( $parent['block'] ) ? new WP_Block( $parent['block'] ) : null;
					$fields                = get_fields();
					$class_name            = $attributes['className'];
					$context['attributes'] = $attributes;
					$context['block']      = $block;
					$context['fields']     = $fields;
					$context['parent']     = $parent;
					$context['context']    = $block_context;
					$context['className']  = $class_name;
					$context['is_preview'] = $is_preview;
					$context['attributes'] = $attributes;
					$props                 = $attributes;
					$context['props']      = $props;

					if ( ! empty( $context_file ) ) {
						include $context_file;
					}

					Timber::render(
						array(
							"blocks/$template.twig",
							"acf-blocks/$template.twig",
						),
						$context
					);
				} else {
					throw new \Exception( "Tried to load twig template for block $template but Timber is not activated" );
				}
			} else {
				if ( ! empty( $args['render_callback'] ) ) {
					$args['render_callback']( $attributes, $content, $is_preview, $post_id, $block, $block_context );
				} else {
					$template_file = get_template_directory() . "/template-parts/acf-blocks/{$template}.php";
					if ( is_file( $template_file ) ) {
						include $template_file;
					}
				}
			}
		};

	return $args;
}
add_filter( 'acf/register_block_type_args', __NAMESPACE__ . '\xd_filter_acf_block_settings' );

/**
 * Add a reference to block parent in render function.
 *
 * @since 1.3.0
 *
 * @param array    $parsed_block Block data.
 * @param array    $source_block Block data.
 * @param WP_Block $parent_block Parent block.
 */
function xd_append_parent_block_data( $parsed_block, $source_block, $parent_block ) {
	$parsed_block['parent'] = null;
	if ( ! empty( $parent_block->parsed_block ) ) {

		$parsed_block['parent'] = array(
			'name'       => $parent_block->name,
			'props'      => $parent_block->attributes,
			'attributes' => $parent_block->attributes,
			'block'      => $parent_block->parsed_block,
		);
	}
	return $parsed_block;
}

add_filter( 'render_block_data', __NAMESPACE__ . '\xd_append_parent_block_data', 10, 3 );


