<?php
/**
 * XD_Video type
 *
 * @package Kicks.
 * @phpcs:disable WordPress.NamingConventions.ValidVariableName.PropertyNotSnakeCase
 */

namespace XD\Types;

/**
 * XD_Video type definition
 *
 * @property int $id video id.
 * -
 *
 * @property Video $sm video small.
 * -
 *
 * @property Video $md video medium.
 * -
 *
 * @property Video $lg video large.
 * -
 *
 * @property XD_Modal $modal modal video.
 * -
 *
 * @property string|boolean $autoplay autoplay video. 'inview' or true/false.
 * -
 *
 * @property bool $show_modal_button include a modal toggle link in video template.
 * -
 */
class XD_Video extends XD_Template_Props {

	/**
	 * XD_video is considered empty when it has no large video id.
	 */
	public function is_empty() {

		$lg        = (array) $this->lg;
		$modal     = new XD_Modal( $this->modal );
		$modal_src = (array) $modal->src;
		return $this->empty( $lg ) && empty( $modal_src['url'] );

	}

	/**
	 * Check if video has a large video.
	 */
	public function has_video() {
		$lg = (array) $this->lg;
		return ! $this->empty( $lg );
	}

	/**
	 * Retrieve type data.
	 */
	public function get_data() {

		$modal = (array) $this->modal;
		if ( empty( $modal['src'] ) || $this->empty( $modal['src'] ) ) {
			$this->modal             = null;
			$this->show_modal_button = false;
		}

		$lg = (array) $this->lg;

		if ( ! empty( $lg['url'] ) ) {

			$md = (array) $this->md;
			$sm = (array) $this->sm;

			if ( empty( $md['url'] ) ) {
				$md['url'] = $lg['url'];
			}

			if ( empty( $sm['url'] ) ) {
				$sm['url'] = $md['url'];
			}

			$this->dataset['src-lg'] = $lg['url'];
			$this->dataset['src-md'] = $md['url'];
			$this->dataset['src-sm'] = $sm['url'];
			$this->dataset['ui']     = 'video';
			$this->dataset['video']  = array();

			if ( $this->autoplay ) {
				if ( 'inview' === $this->autoplay ) {
					$this->dataset['video'] = array( 'autoplay' => true );
					unset( $this->autoplay );
				} else {
					$this->autoplay = 'autoplay';
				}
			}
		}

		return parent::get_data();
	}
	/**
	 * Initialize video.
	 *
	 * @param array $props image props.
	 */
	public function __construct( $props = array() ) {
		$this->autoplay = 'inview';
		$this->sm       = new Video();
		$this->md       = new Video();
		$this->lg       = new Video();
		$this->modal    = new XD_Modal();
		parent::__construct( $props );
	}

}
