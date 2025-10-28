<?php
/**
 * XD_Accordion_Options type
 *
 * @package Kicks.
 */

namespace XD\Types;

/**
 * XD_Accordion_Options type definition
 *
 * @property array<XD_Accordion_Element> $elements Accordion footer content.
 * -
 *
 * @property XD_Accordion_Options $options Accordion options.
 * -
 */
class XD_Accordion extends XD_Template_Props {

	/**
	 * Retrieve type data.
	 */
	public function get_data() {
		$this->dataset['accordion'] = $this->options;
		$this->dataset['ui']        = 'accordion';
		return parent::get_data();
	}
	/**
	 * Initialize Accordion.
	 *
	 * @param array $props accordion props.
	 */
	public function __construct( $props = array() ) {
		$this->options  = new XD_Accordion_Options();
		$this->elements = array();
		parent::__construct( $props );
	}
}

















