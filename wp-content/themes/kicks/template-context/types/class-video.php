<?php
/**
 * Video type
 *
 * @package Kicks.
 * @phpcs:disable WordPress.NamingConventions.ValidVariableName.PropertyNotSnakeCase
 */

namespace XD\Types;

/**
 * Video type definition
 *
 * @property int $id video id.
 * -
 *
 * @property string $url video src.
 * -
 */
class Video extends XD_Type_Base {

	/**
	 * Video is considered empty when it has no id.
	 */
	public function is_empty() {
		return empty( $this->id );
	}
	/**
	 * Initialize video.
	 *
	 * @param array $props image props.
	 */
	public function __construct( $props = array() ) {
		$this->url = '';
		parent::__construct( $props );
	}
}
