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
 * @property string $type modal type.
 * -
 * - Container (default)
 * - full
 * - dialog
 *
 * @property string $has_close_button Modal has internal close button.
 * -
 *
 * @property string $header_content Modal header content.
 * -
 *
 * @property string $body_content Modal body content.
 * -
 *
 * @property string $footer_content Modal footer content.
 * -
 *
 * @property boolean $show_on_load Show modal on load.
 * -
 *
 * @property XD_Modal_Options $options Modal options.
 * -
 *
 * @property XD_Iframe $src modal source.
 * -
 *
 * @property XD_template_Props $dialog modal template props.
 * -
 */
class XD_Modal extends XD_Template_Props {

	/**
	 * Retrieve type data.
	 */
	public function get_data() {

		if ( ! $this->empty( $this->src ) ) {
			$this->src = new XD_Iframe( $this->src );
		}

		$this->dataset['modal']        = $this->options;
		$this->dataset['ui']           = 'modal';
		$this->dataset['show-on-load'] = $this->show_on_load;
		if ( 'full' === $this->type || 'container' === $this->type ) {
			$this->class_name[] = 'xd-modal--' . $this->type;
		}
		if ( 'full' === $this->type ) {
			if ( ! in_array( 'modal-open', $this->options->cls_page, true ) ) {
				$this->options->cls_page[] = 'modal-open';
			}
		}
		return parent::get_data();
	}
	/**
	 * Initialize modal.
	 *
	 * @param array $props modal props.
	 */
	public function __construct( $props = array() ) {
		$this->options = new XD_Modal_Options();
		$this->src     = new XD_Iframe();
		$this->dialog  = new XD_Template_Props();
		$this->type    = 'full';
		parent::__construct( $props );
	}
}
