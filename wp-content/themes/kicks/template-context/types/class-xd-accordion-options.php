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
 * @property int $active Index of the element to open initially.
 * -
 *
 * @property bool $animation Reveal item directly or with a transition.
 * -
 *
 * @property bool $collapsible Allow all items to be closed.
 * -
 *
 * @property string $content The content selector, which selects the accordion content elements.
 * -
 *
 * @property int $duration Animation duration in milliseconds.
 * -
 *
 * @property bool $multiple Allow multiple open items.
 * -
 *
 * @property string $targets CSS selector of the element(s) to toggle.
 * -
 *
 * @property string $toggle The toggle selector, which toggles accordion items.
 * -
 *
 * @property string $transition The transition to use when revealing items. use keyword for easing functions.
 * -
 *
 * @property int $offset Pixel offset added to scroll top.
 * -
 */
class XD_Accordion_Options extends XD_Type_Base {

	/**
	 * Animation duration
	 *
	 * @var int Animation duration in milliseconds.
	 */
	public $duration = 550;

	/**
	 * Animation transition
	 *
	 * @var int The transition to use when revealing items. use keyword for easing functions.
	 */
	public $transition = 'cubic-bezier(.55,.36,.19,1)';
}
