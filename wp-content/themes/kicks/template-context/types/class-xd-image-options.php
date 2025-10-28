<?php
/**
 * XD_Image_Options type
 *
 * @package Kicks.
 * @phpcs:disable WordPress.NamingConventions.ValidVariableName.PropertyNotSnakeCase
 */

namespace XD\Types;

/**
 * XD_Image_Options type definition.
 *
 * @property string $size Image dimension descriptor
 * -
 * - 'img'
 * - 'img-square'
 * - 'img-modal'
 *
 * @property string $breakpoint When to switch to secondary image
 * -
 * - 'sm'
 * - 'md'
 * - 'lg'
 * - 'xl'
 * - 'xxl'
 * - 'xxxl'
 * - 'hd'
 * - 'retina'
 * - '4k'.
 *
 * @property array $breakpoints Width of image in viewport
 * -
 * eg. ['lg' => '50vw', 'xxl' => '33vw']
 *
 * @property string $class Picture element class
 * -
 *
 * @property string $img_class Img element class
 * -
 *
 * @property array $attributes Picture element attributes key value pairs
 * -
 *
 * @property array $img_attributes Img element attributes key value pairs
 * -
 *
 * @property boolean $lazyload Image should be lazy loaded.
 * -
 *
 * @property string $width the image width.
 * -
 *
 * @property string $media the image media query.
 * -
 *
 * @property string $portrait use a portrait media query.
 * -
 *
 * @property string $use_image_size whether or not to use the image actual size.
 * -
 */
class XD_Image_Options extends XD_Type_Base {

	/**
	 * XD_Image_Options constructor.
	 *
	 * @param array $props type properties.
	 */
	public function __construct( $props = array() ) {
		$this->use_image_size = null;
		parent::__construct( $props );
	}

}

