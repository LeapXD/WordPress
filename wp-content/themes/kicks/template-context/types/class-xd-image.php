<?php
/**
 * XD_Image type
 *
 * @package Kicks.
 * @phpcs:disable WordPress.NamingConventions.ValidVariableName.PropertyNotSnakeCase
 */

namespace XD\Types;

/**
 * XD_Image type definition
 *
 * @property string $alt image alt text
 * -
 *
 * @property Image $primary primary image
 * -
 *
 *  Usually large image
 *
 * @property Image $secondary secondary image
 * -
 *
 * Usually mobile image
 *
 * @property XD_video $video video
 * -
 *
 * Video
 *
 * @property XD_Image_Options $options Image options
 * -
 *
 * image options
 *
 * @property XD_Link $link Image link props
 * -
 *
 * image link
 *
 * @property string $caption Image caption
 * -
 *
 * image caption
 *
 * @property bool $show_caption include the caption in the image template.
 * -
 *
 * @property string $portrait use a portrait media query.
 * -
 *
 * image caption
 */
class XD_Image extends XD_Template_Props {

	/**
	 * Get image data
	 */
	public function get_data() {
		$primary = (array) $this->primary;

		if ( ! empty( $this->options->use_image_size ) && ! empty( $primary['id'] ) ) {

			$image_src = wp_get_attachment_image_src( $primary['id'], 'full' );
			if ( ! empty( $image_src ) ) {
				$this->css_vars                          = $this->css_vars ?? array();
				$this->css_vars['--xd-image-src-width']  = $image_src[1] . 'px';
				$this->css_vars['--xd-image-src-height'] = $image_src[2] . 'px';
			}
		}
		if ( isset( $this->options->portrait ) && $this->options->portrait ) {
			$this->options->media             = '(orientation: portrait)';
			$this->video->dataset['portrait'] = 'true';
		} else {
			unset( $this->video->dataset['portrait'] );
		}
		$video            = new XD_Video( $this->video );
		$this->class_name = (array) $this->class_name;
		if ( $video ) {
			$video = $video->get_data();
			if ( ! empty( $video['modal']['src']['url'] ) ) {
				$this->class_name['xd-video--modal']       = 'xd-video--modal';
				$this->class_name['xd-image--modal-video'] = 'xd-image--modal-video';
			}
		}
		if ( $this->caption && $this->show_caption ) {
			$this->class_name['xd-image--caption'] = 'xd-image--caption';
		}
		$this->class_name['xd-image'] = 'xd-image';
		if ( in_array( 'xd-background-image', $this->class_name, true ) ) {
				unset( $this->class_name['xd-image'] );
		}
		return parent::get_data();
	}

	/**
	 * Check if image has a primary, secondary image, or video.
	 */
	public function has_image() {
		$primary   = (array) $this->primary;
		$secondary = (array) $this->secondary;
		$video     = new XD_Video( (array) $this->video );
		return ! $this->empty( $primary ) || ! $this->empty( $secondary ) || $video->has_video();
	}

	/**
	 * XD_Image is considered empty if it has neither a primary, secondary image, or video.
	 */
	public function is_empty() {
		$primary   = (array) $this->primary;
		$secondary = (array) $this->secondary;
		$video     = new XD_Video( (array) $this->video );
		return $this->empty( $primary ) && $this->empty( $secondary ) && $video->is_empty();
	}

	/**
	 * Initialize image.
	 *
	 * @param array $props image props.
	 */
	public function __construct( $props = array() ) {
		$this->primary   = new Image();
		$this->secondary = new Image();
		$this->video     = new XD_Video();
		$this->options   = new XD_Image_Options();
		$this->link      = new XD_Link();
		parent::__construct( $props );
	}
}
