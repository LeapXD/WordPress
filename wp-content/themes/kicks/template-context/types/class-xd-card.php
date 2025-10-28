<?php
/**
 * XD_Card type
 *
 * @package Kicks.
 * @phpcs:disable WordPress.NamingConventions.ValidVariableName.PropertyNotSnakeCase
 */

namespace XD\Types;

/**
 * XD_Card type definition.
 *
 * @property XD_Image $image card image
 * -
 *
 * @property XD_Link $link card link.
 * -
 *
 * @property boolean $is_linked whole card is linked.
 * -
 */
class XD_Card extends XD_Template_Props {

	/**
	 * Initialize card.
	 *
	 * @param array $props card props.
	 */
	public function __construct( $props = array() ) {
		$this->image = new XD_Image();
		$this->link  = new XD_Link();
		parent::__construct( $props );
	}
}
