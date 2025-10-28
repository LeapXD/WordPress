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
 * @property string $anchor anchor.
 * -
 *
 * @property string $id id.
 * -
 *
 * @property array $class_name class.
 * -
 *
 * @property array $css style.
 * -
 *
 * @property array $css_vars style.
 * -
 *
 * @property string $style style.
 * -
 *
 * @property array $dataset dataset array.
 * -
 *
 * @property array $aria accessibility labels.
 * -
 *
 * @property array $role accessibility role.
 * -
 */
#[\AllowDynamicProperties]
class XD_Template_Props extends XD_Type_Base {

	/**
	 * Sets property.
	 *
	 * @param string $property the property name.
	 * @param mixed  $value the property value.
	 */
	public function __set( $property, $value ) {
		if ( 'id' === $property && ! empty( $value ) ) {
			$parts = explode( '\\', ( get_class( $this ) ) );
			$id    = str_replace( 'xd-block', '', $value );
			if ( (string) $id === (string) $value ) {
				$id = '-' . $id;
			}
			$id       = str_replace(
				'xd-template-props',
				'xd-block',
				xd_kebab_case(
					end(
						$parts
					) . $id
				)
			);
			$this->id = $id;
			return;
		}
		parent::__set( $property, $value );
	}

	/**
	 * Retrieve type data.
	 */
	public function get_data() {
		$properties = parent::get_data();
		if ( empty( $properties ) ) {
			return null;
		}
		if ( ! empty( $this->dataset ) && is_array( $this->dataset ) ) {
			$dataset        = $this->dataset;
			$dataset_string = '';
			foreach ( $dataset as $key => $val ) {
				if ( $val instanceof XD_Type_Base ) {
					$val = $val->get_data();
				}
				if ( is_null( $val ) ) {
					$dataset_string .= "data-$key ";
				} else {
					if ( ! is_scalar( $val ) ) {
						$val = wp_json_encode(
							array_filter(
								(array) $val,
								fn( $key ) => xd_camel_case( $key ) === $key,
								ARRAY_FILTER_USE_KEY
							)
						);
					}
					$dataset_string .= "data-$key='" . $val . "'\n";
				}
			}
			$properties['dataset'] = $dataset_string;
		}
		if ( ! empty( $this->aria ) ) {
			$aria        = $this->aria;
			$aria_string = '';
			foreach ( $aria as $key => $val ) {
				if ( is_null( $val ) ) {
					$aria_string .= "aria-$key ";
				} else {
					if ( ! is_scalar( $val ) ) {
						$val = wp_json_encode( $val );
					}
					$aria_string .= "aria-$key='" . $val . "'\n";
				}
			}
			$properties['aria'] = $aria_string;
		}
		if ( ! empty( $this->role ) ) {
			$properties['role'] = 'role="' . $this->role . '"';
		}
		if ( ! empty( $this->id ) || ! empty( $this->anchor ) ) {
			$id               = ! empty( $this->anchor ) ? $this->anchor : $this->id;
			$properties['ui'] = $id;
			$properties['id'] = 'id="' . $id . '"';
		}
		if ( ! empty( $this->class_name ) && is_array( $this->class_name ) ) {
			$properties['className'] = xd_classnames( $this->class_name );
		}
		$style = '';
		if ( ! empty( $this->style ) ) {
			$style = $this->style;
		}
		if ( ! empty( $this->css ) ) {
			$css = wp_style_engine_get_styles( $this->css );
			if ( ! empty( $css['css'] ) ) {
				$style            .= $css['css'];
				$properties['css'] = $css;
			}
		}
		if ( ! empty( $this->css_vars ) ) {
			foreach ( $this->css_vars as $css_prop => $css_val ) {
				$style .= "{$css_prop}:{$css_val};";
			}
		}
		if ( ! empty( $style ) ) {
			$properties['style'] = 'style="' . $style . '"';
		}
		$parts = explode( '\\', ( get_class( $this ) ) );
		return apply_filters( 'xd_template_props', $properties, end( $parts ), $this );
	}

	/**
	 * Initialize template props.
	 *
	 * @param array $props template props.
	 */
	public function __construct( $props = array() ) {
		$this->dataset    = array();
		$this->class_name = array();
		parent::__construct( $props );
	}

}
