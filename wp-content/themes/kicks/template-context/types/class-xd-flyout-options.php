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
 * @property boolean $esc_close Close the off-canvas when the Esc key is pressed.
 * -
 *
 * @property boolean $bg_close Close the off-canvas when the background is clicked.
 * -
 *
 * @property string $mode Off-canvas animation mode: slide, reveal, push or none.
 * -
 *
 * @property string $container Define a target container via a selector to specify where the off-canvas should be appended in the DOM. Setting it to false will prevent this behavior.behavior.
 * -
 *
 * @property array $cls_page Class to add to <html> when modal is active.
 * -
 *
 * @property boolean $flip Flip off-canvas to the right side.
 * -
 *
 * @property boolean $overlay Display the off-canvas together with an overlay.
 * -
 */
class XD_Flyout_Options extends XD_Type_Base {


	/**
	 * Retrieve type data.
	 */
	public function get_data() {
		if ( ! in_array( 'uk-offcanvas-page', $this->cls_page, true ) ) {
			$this->cls_page[] = 'uk-offcanvas-page';
		}
		if ( ! in_array( 'modal-open', $this->cls_page, true ) ) {
			$this->cls_page[] = 'modal-open';
		}
		return parent::get_data();
	}

	/**
	 * Initialize flyout options.
	 *
	 * @param array $props flyout props.
	 */
	public function __construct( $props = array() ) {
		$this->cls_page = array();
		$this->flip     = true;
		$this->overlay  = true;
		parent::__construct( $props );
	}
}
