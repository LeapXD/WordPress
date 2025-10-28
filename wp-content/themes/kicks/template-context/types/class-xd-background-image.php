<?php
/**
 * XD_Background_Image type
 *
 * @package Kicks.
 * @phpcs:disable WordPress.NamingConventions.ValidVariableName.PropertyNotSnakeCase
 */

namespace XD\Types;

/**
 * XD_Background_Image type definition
 */
class XD_Background_Image extends XD_Image {

	/**
	 * Get image data
	 */
	public function get_data() {
		$data = parent::get_data();
		if ( ! empty( $data['className'] ) ) {
			$class_name        = explode( ' ', $data['className'] );
			$class_name        = array_diff( $class_name, array( 'xd-image' ) );
			$class_name[]      = 'xd-background-image';
			$data['className'] = implode( ' ', $class_name );
		}
		return $data;
	}


}
