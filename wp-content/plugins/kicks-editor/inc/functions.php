<?php
/**
 * Editor setup.
 *
 * @package Leap Editor
 */

/**
 * Check installed theme version
 *
 * @since 1.3.0
 * @param string $compare eg. >=.
 * @param string $ver Theme version to check.
 * @param bool   $parent Check parent theme otherwise check child theme.
 * @return bool theme is greater than version.
 */
function xd_theme_version_compare( $compare, $ver, $parent = true ) {

	$template   = get_template();
	$stylesheet = get_stylesheet();

	if ( ! $parent && $template === $stylesheet ) {
		// When there is no child theme and we're attempting to check the child theme assume the parent theme is older.
		return false;
	}

	$theme_version = wp_get_theme( $parent ? get_template() : get_stylesheet() )->get( 'Version' );
	if ( strpos( $ver, '<' ) !== false && preg_match( '/201[7-9]/', $theme_version ) ) {
		return true;
	}
	return version_compare( $theme_version, $ver, $compare );

}

/**
 * Checks to see if any array keys are not integers or not sequential.
 * Arrays with non-sequential or non-numeric keys will json encode as objects
 *
 * @param array $array the array to check.
 */
function xd_is_associative_array( $array = null ) {
	if ( ! is_array( $array ) ) {
		return false;
	}
	$sequential = array_values( $array );
	$intersect  = array_intersect_key( $array, $sequential );
	return( count( $intersect ) !== count( $array ) );
}

/**
 * Php port of https://github.com/JedWatson/classnames.
 */
function xd_classnames() {
	$classes          = array();
	$arguments        = func_get_args();
	$arguments_length = count( $arguments );
	for ( $i = 0; $i < $arguments_length; $i++ ) {
		$arg = $arguments[ $i ];
		if ( ! $arg ) {
			continue;
		}
		if ( is_scalar( $arg ) ) {
			$classes = array_merge( $classes, explode( ' ', $arg ) );
		} elseif ( is_array( $arg ) ) {
			if ( count( $arg ) ) {
				if ( xd_is_associative_array( $arg ) ) {
					foreach ( $arg as $key => $val ) {
						if ( is_int( $key ) && $val ) {
							$classes[] = $val;
						} elseif ( $val ) {
							$classes[] = $key;
						}
					}
				} else {
					$inner = xd_classnames( ...$arg );
					if ( $inner ) {
						$classes[] = $inner;
					}
				}
			}
		}
	}
	return join( ' ', array_unique( $classes ) );
}

/**
 * Convert string to camelCased string
 *
 * @param string $string string to convert.
 */
function xd_camel_case( $string ) {
	foreach ( array( '-', '_', '/' ) as $delimiter ) {
		$string = lcfirst( str_replace( $delimiter, '', ucwords( $string, $delimiter ) ) );
	}
	return $string;
}

/**
 * Convert string to snake_cased string
 *
 * @param string $string string to convert.
 */
function xd_snake_case( $string ) {
	$string = strtolower( preg_replace( '/([a-z])([A-Z])/', '$1_$2', $string ) );
	foreach ( array( '_', '-', '/' ) as $delimiter ) {
		$string = str_replace( $delimiter, '_', $string );
	}

	return $string;
}

/**
 * Convert string to kebab-cased string
 *
 * @param string $string string to convert.
 */
function xd_kebab_case( $string ) {
	return str_replace( '_', '-', xd_snake_case( $string ) );
}

/**
 * Recursively get a list of files in directory.
 *
 * @param string $directory Directory location.
 */
function get_files( $directory ) {
	$dir = opendir( $directory );
	$tmp = array();
	if ( $dir ) {

		$file = readdir( $dir );
		while ( $file ) {
			if ( '.' !== $file && '..' !== $file && '.' !== $file[0] ) {
				if ( false === strpos( $file, '.twig' ) && false === strpos( $file, '.php' ) ) {
					$tmp2 = get_files( $directory . $file . DIRECTORY_SEPARATOR );
					$tmp  = array_merge( $tmp, $tmp2 );
				} else {
					array_push( $tmp, $directory . $file );
				}
			}
			$file = readdir( $dir );
		}
		closedir( $dir );
	}
	return $tmp;
}

/**
 * Recursively get a list of files in directory.
 *
 * @param string $folder Directory location.
 * @param string $reg_pattern Regex pattern.
 */
function xd_file_search( $folder, $reg_pattern ) {
	$dir       = new RecursiveDirectoryIterator( $folder );
	$ite       = new RecursiveIteratorIterator( $dir );
	$files     = new RegexIterator( $ite, $reg_pattern, RegexIterator::GET_MATCH );
	$file_list = array();
	foreach ( $files as $file ) {
			$file_list = array_merge( $file_list, $file );
	}
	return $file_list;
}

/**
 * Recursively merge arrays without replacing numeric keys
 *
 * @param array   $array1 first array.
 * @param boolean $merge_variations array.
 * @param array   ...$arrays additional arrays.
 */
function xd_merge_block_settings( $array1, $merge_variations = false, ...$arrays ) {
	if ( is_array( $merge_variations ) ) {
		$arrays = array( $merge_variations );
	}
	$merged = $array1;
	foreach ( $arrays as $array ) {
		foreach ( $array as $key => $value ) {
			if ( is_array( $value ) && isset( $merged[ $key ] ) && is_array( $merged[ $key ] ) ) {
				if ( xd_is_associative_array( $value ) ) {
					$merged[ $key ] = xd_merge_block_settings( $merged[ $key ], $value );
				} elseif ( $merge_variations && 'variations' === $key ) {
					$merge_variations = array_column( $merged[ $key ], null, 'name' );
					$value_variations = array_column( $value, null, 'name' );
					foreach ( $value_variations as $variation ) {
						if ( isset( $merge_variations[ $variation['name'] ] ) ) {
							$merge_variations[ $variation['name'] ] = xd_merge_block_settings( $merge_variations[ $variation['name'] ], $variation );
						} else {
							$merge_variations[ $variation['name'] ] = $variation;
						}
						$value = array_values( $merge_variations );
					}
					$merged[ $key ] = $value;
				} else {
					$merged[ $key ] = $value;
				}
			} else {
				if ( is_int( $key ) ) {
					$merged[] = $value;
				} else {
					$merged[ $key ] = $value;
				}
			}
		}
	}
	return $merged;
}

/**
 * Export an array to PHP code using array() syntax, preserving associative keys, omitting numeric keys.
 *
 * @param mixed  $value  The value to export.
 * @param string $indent (Optional) Current indentation for pretty output.
 * @return string
 */
function xd_export_blocks_array( $value, $indent = '' ) {
	if ( is_array( $value ) ) {
		$is_assoc = array_keys( $value ) !== range( 0, count( $value ) - 1 );
		$output   = "array(\n";

		foreach ( $value as $key => $sub_value ) {
			$output .= $indent . "\t";

			if ( $is_assoc ) {
				//phpcs:ignore
				$output .= var_export( $key, true ) . ' => ';
			}

			$output .= xd_export_blocks_array( $sub_value, $indent . "\t" ) . ",\n";
		}

		return $output . $indent . ')';
	}

	//phpcs:ignore
	return var_export( $value, true );
}

/**
 * Collect all possible tokens from a feature's supports value (block.json),
 * ignoring any `when` conditions. Also detects a wildcard (supports === true or
 * any unconditional `true` overlay) to mean "all tokens allowed".
 *
 * @param mixed $feature_support  Value under supports["xd/feature"] or similar.
 * @return array{tokens: string[], wildcard: bool}
 */
function xd_collect_possible_tokens( $feature_support ) {
	$tokens   = array();
	$wildcard = false;

	$add = function( $val ) use ( &$add, &$tokens, &$wildcard ) {
		if ( true === $val ) {
			$wildcard = true;
			return; }

		if ( is_string( $val ) || is_int( $val ) || is_float( $val ) ) {
			$tokens[] = (string) $val;
			return;
		}

		if ( is_array( $val ) ) {
			// list.
			if ( array_keys( $val ) === range( 0, count( $val ) - 1 ) ) {
				foreach ( $val as $v ) {
					if ( true === $v ) {
						$wildcard = true;
						continue;
					}
					if ( is_string( $v ) || is_int( $v ) || is_float( $v ) ) {
						$tokens[] = (string) $v;
					}
				}
				return;
			}
			// assoc with context.
			if ( isset( $val['context'] ) && is_array( $val['context'] ) ) {
				foreach ( $val['context'] as $item ) {
					if ( true === $item ) {
						$wildcard = true;
						continue;
					}
					if ( is_string( $item ) || is_int( $item ) || is_float( $item ) ) {
						$tokens[] = (string) $item;
						continue;
					}
					if ( is_array( $item ) ) {
						if ( array_keys( $item ) === range( 0, count( $item ) - 1 ) ) {
							$add( $item );
							continue;
						}
						if ( isset( $item['supports'] ) ) {
							$add( $item['supports'] ); }
						continue;
					}
				}
			}
		}
	};

	$add( $feature_support );
	$tokens = array_values( array_unique( array_map( 'strval', $tokens ) ) );
	return array(
		'tokens'   => $tokens,
		'wildcard' => $wildcard,
	);
}


/**
 * Normalize an attribute "if" into a flat list of tokens.
 * Accepts string | list<string> | anything else → [].
 *
 * @param string|array $if the "if" attribute value.
 */
function xd_attr_if_tokens( $if ) {
	if ( is_string( $if ) ) {
		return array( $if );
	}
	if ( is_array( $if ) && array_keys( $if ) === range( 0, count( $if ) - 1 ) ) {
		return array_values( array_filter( $if, 'is_string' ) );
	}
	return array();
}

