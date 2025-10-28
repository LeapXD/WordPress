<?php
/**
 * Primary menu walker class.
 *
 * @package Kicks.
 */

use Timber\Timber;
use XD\Types\XD_Accordion;
use XD\Types\XD_Accordion_Element;

/**
 * Accordion menu walker class
 */
class XD_Menu extends Walker_Nav_Menu {

	/**
	 * Recursively visit each menu item and build the child accordions.
	 *
	 * @param WP_Post[] $elements child menu items.
	 */
	public function build_accordions( $elements = array() ) {
		if ( ! empty( $elements ) ) {
			foreach ( $elements as $index => &$element ) {
				$accordion               = new XD_Accordion();
				$accordion->class_name[] = 'xd-accordion';
				$accordion_element       = new XD_Accordion_Element(
					array(
						'title' => $element->title,
						'url'   => $element->url,
					)
				);
				if ( $element->current ) {
					$accordion_element->class_name[] = 'xd-menu__link--current';
				}
				if ( $element->current_item_ancestor || $element->current ) {
					// 0 is the first element in the accordion. All accordions only contain one accordion element.
					$accordion->options->active = 0;
				}
				if ( ! empty( $element->children ) ) {
					$accordion->elements          = $this->build_accordions( $element->children );
					$accordion_element->accordion = $accordion->get_data();
				}
				$element = $accordion_element->get_data();
			}
		}
		return $elements;
	}

	/**
	 * Performs a double nested foreach loop over the flat array of elements,
	 * adding a hierarchial relationship to each child element's respective parent
	 * and creates a top level node array containing the first level children of the n-ary tree.
	 *
	 * @param WP_Post[] $elements the nav_menu post items that makeup the menu.
	 * @param int       $max_depth max menu depth.
	 * @param mixed[]   ...$args the remaining arguments.
	 */
	public function walk( $elements, $max_depth, ...$args ) {
		$parent_field = $this->db_fields['parent'];
		$toplevel     = array();
		foreach ( $elements as $parent_element ) {
			$parent_element->children = array();
			foreach ( $elements as $e ) {
				if ( (int) $e->$parent_field === $parent_element->ID ) {
					$parent_element->children[] = $e;
				}
			}
			if ( ! $parent_element->$parent_field ) {
				$toplevel[] = $parent_element;
			}
		}
		$menu = $this->build_accordions( $toplevel );
		return Timber::compile( 'partials/nav/menu.twig', array( 'menu' => $menu ) );
	}
}
