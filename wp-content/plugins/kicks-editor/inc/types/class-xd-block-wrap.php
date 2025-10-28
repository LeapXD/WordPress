<?php
/**
 * XD_Block_Wrap type
 *
 * @package Kicks.
 * @phpcs:disable WordPress.NamingConventions.ValidVariableName.PropertyNotSnakeCase
 */

namespace XD\Types;

/**
 * Type definition for XD_Block_Wrap.
 *
 * @property XD_Template_Props $block block wrapper attributes.
 * -
 *
 * @property XD_Template_Props $outer outer wrapper attributes.
 * -
 *
 * @property XD_Template_Props $inner inner wrapper attributes.
 * -
 *
 * @property XD_Template_Props $pre_inner_blocks pre inner blocks wrapper attributes.
 * -
 *
 * @property XD_Template_Props $inner_blocks inner blocks wrapper attributes.
 * -
 *
 * @property XD_Template_Props $post_inner_blocks post inner blocks wrapper attributes.
 * -
 */
#[\AllowDynamicProperties]
class XD_Block_Wrap extends XD_Type_Base {

	/**
	 * Initialize block.
	 *
	 * @param array $props attributes.
	 */
	public function __construct( $props = array() ) {
		foreach ( array(
			'block',
			'outer',
			'inner',
			'pre_inner_blocks',
			'inner_blocks',
			'post_inner_blocks',
		) as $template ) {
			$this->$template = new XD_Template_Props();
		}
		foreach ( $props as $template => $attributes ) {
			if ( ! isset( $this->$template ) ) {
				$this->$template = new XD_Template_Props();
			}
			foreach ( $attributes as $attribute_name => $attribute_value ) {
				$this->$template->$attribute_name = $attribute_value;
			}
		}
	}
}
