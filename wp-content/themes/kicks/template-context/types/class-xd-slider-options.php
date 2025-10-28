<?php
/**
 * XD_Slider_Options type
 *
 * @package Kicks.
 */

namespace XD\Types;

/**
 * XD_Slider_Options type definition
 *
 * @property string $autoplay Slider autoplays.
 * -
 *
 * @property float $autoplay_interval The delay between switching slides in autoplay mode.
 * -
 *
 * @property bool $center Center the active slide.
 * -
 *
 * @property bool $draggable Enable pointer dragging.
 * -
 *
 * @property string $easing The animation easing (CSS timing functions or cubic-bezier).
 * -
 *
 * @property bool $finite Disable infinite sliding.
 * -
 *
 * @property int $index Slider item to show. 0 based index.
 * -
 *
 * @property bool $pause_on_hover Pause autoplay mode on hover.
 * -
 *
 * @property bool $sets Slide in sets.
 * -
 *
 * @property float $velocity The animation velocity (pixel/ms).
 * -
 */
class XD_Slider_Options extends XD_Type_Base {

	/**
	 * Animation velocity
	 *
	 * @var float The animation velocity (pixel/ms).
	 */
	public $velocity = 0.7;

	/**
	 * Initialize slider options.
	 *
	 * @param array $props slider props.
	 */
	public function __construct( $props = array() ) {
		$this->sets   = false;
		$this->finite = true;
		parent::__construct( $props );
	}
}
