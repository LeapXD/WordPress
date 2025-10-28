<?php
/**
 * XD_Modal_Options type
 *
 * @package Kicks.
 */

namespace XD\Types;

/**
 * XD_Modal_Options type definition
 *
 * @property bool $esc_close Close the modal when the Esc key is pressed.
 * -
 *
 * @property bool $bg_close Close the modal when the background is clicked.
 * -
 *
 * @property bool $stack Stack modals, when more than one is open. By default, the previous modal will be hidden..
 * -
 *
 * @property string $container Define a target container via a selector to specify where the modal should be appended in the DOM. Setting it to false will prevent this behavior.
 * -
 *
 * @property array $cls_page Class to add to <html> when modal is active.
 * -
 *
 * @property array $cls_panel Class of the element to be considered the panel of the modal.
 * -
 *
 * @property string $sel_close '.uk-modal-close, .uk-modal-close-default, .uk-modal-close-outside, .uk-modal-close-full'.
 * -
 */
class XD_Modal_Options extends XD_Type_Base {

	/**
	 * Retrieve type data.
	 */
	public function get_data() {
		if ( ! in_array( 'uk-modal-page', $this->cls_page, true ) ) {
			$this->cls_page[] = 'uk-modal-page';
		}
		return parent::get_data();
	}

	/**
	 * Initialize modal.
	 */
	public function __construct() {
		$this->cls_page  = array();
		$this->cls_panel = array();
		$this->stack     = false;
	}
}
