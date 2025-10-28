<?php
/**
 * XD_Link type
 *
 * @package Kicks.
 * @phpcs:disable WordPress.NamingConventions.ValidVariableName.PropertyNotSnakeCase
 */

namespace XD\Types;

/**
 * XD_Link type definition.
 *
 * @property string $url Link url
 * -
 *
 * @property string $target Link target
 * -
 *
 * @property string $rel Link rel
 * -

 * @property string $title Link title
 * -
 */
class XD_Link extends XD_Type_Base {

	/**
	 * XD_Link is considered empty when it has no url.
	 */
	public function is_empty() {
		return empty( $this->url );
	}
	/**
	 * Setter.
	 *
	 * @param string $property the property name.
	 * @param mixed  $value the property value.
	 */
	public function __set( $property, $value ) {
		if ( 'rel' === $property && ! empty( $value ) ) {
			$this->rel = "rel=\"{$value}\"";
		} elseif ( 'target' === $property && ! empty( $value ) ) {
			$this->target = "target=\"{$value}\"";
		} else {
			parent::__set( $property, $value );
		}
	}

}
