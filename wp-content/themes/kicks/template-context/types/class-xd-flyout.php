<?php
/**
 * XD_Flyout type
 *
 * @package Kicks.
 * @phpcs:disable WordPress.NamingConventions.ValidVariableName.PropertyNotSnakeCase
 */

namespace XD\Types;

/**
 * XD_Flyout type definition
 *
 * @property string $id flyout id.
 * -
 *
 * @property XD_Flyout_Options $options flyout options.
 * -
 *
 * @property boolean $show_on_load Show flyout on load.
 * -
 *
 * @property bool $has_navbar_spacing
 * -
 *
 * @property bool $embed_template defer template rendering including scripts.
 * -
 */
class XD_Flyout extends XD_Template_Props {

	/**
	 * Retrieve type data.
	 */
	public function get_data() {
		$this->dataset['offcanvas'] = $this->options;
		$this->dataset['ui']        = 'offcanvas';
		if ( $this->show_on_load ) {
			$this->dataset['show-on-load'] = true;
		}
		return parent::get_data();
	}

	/**
	 * Initialize slider.
	 *
	 * @param array $props flyout props.
	 */
	public function __construct( $props = array() ) {
		$this->options = new XD_Flyout_Options();
		parent::__construct( $props );
	}
}
