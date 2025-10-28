<?php
/**
 * Image type
 *
 * @package Kicks.
 * @phpcs:disable WordPress.NamingConventions.ValidVariableName.PropertyNotSnakeCase
 */

namespace XD\Types;

/**
 * Image type definition
 *
 * @property string $src image src.
 * -
 *
 * @property string $id image id.
 * -
 */
class Image extends XD_Type_Base {

	/**
	 * Image is considered empty when it has no id.
	 */
	public function is_empty() {
		return empty( $this->id );
	}
}
