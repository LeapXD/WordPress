<?php
/**
 * Base type
 *
 * @package Kicks.
 * @phpcs:disable WordPress.NamingConventions.ValidVariableName.PropertyNotSnakeCase
 */

namespace XD\Types;

/**
 * Base context type.
 *
 * @method bool is_empty() override to define empty data shape. Defaults to alias of empty().
 */
#[\AllowDynamicProperties]
abstract class XD_Type_Base {

	/**
	 * Gets property.
	 *
	 * @param string $property the property name.
	 */
	public function __get( $property ) {
		$snake_cased_property = xd_snake_case( $property );
		if ( isset( $this->$snake_cased_property ) ) {
			return $this->$snake_cased_property;
		}
	}

	/**
	 * Un-sets property.
	 *
	 * @param string $property the property name.
	 */
	public function __unset( $property ) {
		if ( isset( $this->$property ) ) {
			unset( $this->$property );
		}
	}

	/**
	 * Sets property.
	 *
	 * @param string $property the property name.
	 * @param mixed  $value the property value.
	 */
	public function __set( $property, $value ) {
		$snake_cased_property        = xd_snake_case( $property );
		$this->$snake_cased_property = $value;
	}

	/**
	 * Initialize Type.
	 *
	 * @param array $props type properties.
	 */
	public function __construct( $props = array() ) {
		$this->import( $props );
		if ( empty( $this->dataset ) ) {
			$this->dataset = array();
		}
		if ( empty( $this->aria ) ) {
			$this->aria = array();
		}
	}

	/**
	 * Fallback for when method does not exist.
	 * Uses __call so that is_empty() can be overridden without parameters while avoiding PHP warnings.
	 * Also allows the presence of is_empty() to be tested when establishing the method of determining an empty data shape.
	 *
	 * @param string $method the method name.
	 * @param array  $args the method arguments.
	 */
	public function __call( $method, $args ) {
		if ( 'is_empty' === $method && ! empty( $args ) ) {
			return $this->empty( ...$args );
		}
	}

	/**
	 * Tests for empty strings and empty arrays
	 *
	 * @param mixed $value the value to test.
	 */
	public function empty( $value ) {
		if ( is_array( $value ) ) {
			$value = array_filter(
				$value,
				function( $value ) {
					return ! $this->empty( $value );
				}
			);
		}
		if ( $value instanceof XD_Type_Base ) {
			// When testing for empty, use the object's is_empty method if it exists.
			// Otherwise, inspect the object's data.
			return method_exists( $value, 'is_empty' ) ? $value->is_empty() : $this->empty( $value->get_data() );
		}
		return ! ( is_int( $value ) || is_float( $value ) || ( is_bool( $value ) ) || ! empty( $value ) );
	}

	/**
	 * Filters empty values.
	 *
	 * @param mixed $value the value to filter.
	 */
	public function filter( $value ) {
		if ( is_array( $value ) ) {
			foreach ( $value as $key => $item ) {
				$value[ $key ] = $this->filter( $item );
			}
			return array_filter(
				$value,
				function( $value ) {
					return ! $this->empty( $value );
				}
			);
		}
		return $value;
	}


	/**
	 * Retrieve type data.
	 */
	public function get_data() {
		if ( $this->is_empty() ) {
			return null;
		}
		$properties = get_object_vars( $this );
		foreach ( $properties as $name => $value ) {
			if ( is_array( $value ) ) {
				foreach ( $value as $key => $field ) {
					if ( $field instanceof XD_Type_Base ) {
						$properties[ $name ][ $key ] = $field->get_data();
					}
				}
			}
			if ( $value instanceof XD_Type_Base ) {
				$properties[ $name ] = $value->get_data();
			}
			$properties[ xd_camel_case( $name ) ] = $properties[ $name ];
		}
		$parts      = explode( '\\', ( get_class( $this ) ) );
		$properties = apply_filters( 'xd_pre_template_props', $properties, end( $parts ), $this );
		$properties = $this->filter( $properties );
		return $properties;
	}

	/**
	 * Import additional props
	 *
	 * @param array $props type properties.
	 */
	public function import( $props = array() ) {
		if ( is_object( $props ) || is_array( $props ) ) {
			foreach ( $props as $name => $value ) {
				$this->$name = $value;
			}
		}
	}
}
