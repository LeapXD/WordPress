<?php
/**
 * XD_Slider type
 *
 * @package Kicks.
 * @phpcs:disable WordPress.NamingConventions.ValidVariableName.PropertyNotSnakeCase
 */

namespace XD\Types;

/**
 * XD_Slider type definition
 *
 * @property string $id slider id.
 * -
 *
 * @property string[]|XD_Slide[] $slides slides array.
 * -
 * - Use string for rendered slide content
 * - Use XD_slide if properties are needed for each slide
 *
 * @property XD_Slider_Options $options slider options.
 * -
 *
 * @property bool $show_nav show nav.
 * -
 *
 * @property bool $show_dotnav show nav.
 * -
 *
 * @property string $show_nav_on_desktop show nav on desktop.
 * -
 *
 * @property string $arrow show icon name to use for arrows.
 * -
 *
 * @property string $left_arrow icon name to use for left arrow overrides $arrow.
 * -
 *
 * @property string $right_arrow icon name to use for right arrow overrides $arrow.
 * -
 *
 * @property XD_Slider_Options $options slider options.
 * -
 */
class XD_Slider extends XD_Template_Props {

	/**
	 * Retrieve type data.
	 */
	public function get_data() {
		$this->dataset['slider'] = $this->options;
		$this->dataset['ui']     = 'slider';
		return parent::get_data();
	}

	/**
	 * Initialize slider.
	 *
	 * @param array $props slider props.
	 */
	public function __construct( $props = array() ) {
		$this->slides  = array();
		$this->options = new XD_Slider_Options();
		parent::__construct( $props );
	}
}
