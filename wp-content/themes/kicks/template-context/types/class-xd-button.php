<?php
/**
 * XD_Button type
 *
 * @package Kicks.
 * @phpcs:disable WordPress.NamingConventions.ValidVariableName.PropertyNotSnakeCase
 */

namespace XD\Types;

/**
 * XD_button type.
 *
 * @property XD_Link $link button link
 * -
 *
 * @property string $text text
 * -
 *
 * @property string $entity_type target entity type (post_type, taxonomy, etc)
 * -
 *
 * @property string $entity_subtype target entity sub type (post, category, etc)
 * -
 *
 * @property string $entity_id target entity id
 * -
 *
 * @property string $icon button icon markup
 * -
 *
 * @property string $icon_position button icon position (before, after)
 */
class XD_Button extends XD_Template_Props {

	/**
	 * Initialize button.
	 *
	 * @param array $props button props.
	 */
	public function __construct( $props ) {
		$this->link = new XD_Link();
		parent::__construct( $props );
	}
}

