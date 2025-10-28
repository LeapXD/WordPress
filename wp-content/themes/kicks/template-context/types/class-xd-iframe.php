<?php
/**
 * XD_Iframe type
 *
 * @package Kicks.
 * @phpcs:disable WordPress.NamingConventions.ValidVariableName.PropertyNotSnakeCase
 */

namespace XD\Types;

use WP_HTML_Tag_Processor;

/**
 * XD_Iframe type definition.
 *
 * @property string $html iframe html
 * -
 */
class XD_Iframe extends XD_Link {

	/**
	 * XD_Iframe is considered empty when it has no url or html.
	 */
	public function is_empty() {
		return empty( $this->url ) && empty( $this->html );
	}

	/**
	 * Retrieve type data.
	 */
	public function get_data() {
		if ( ! empty( $this->url ) && ! empty( $this->html ) && preg_match( '/youtube|vimeo/', $this->url ) ) {
			$iframe = new WP_HTML_Tag_Processor( $this->html );
			if ( $iframe->next_tag( 'iframe' ) ) {
				$iframe->set_attribute( 'data-ui', 'video' );
				$iframe->set_attribute( 'data-video', wp_json_encode( array( 'autoplay' => true ) ) );
			}
			$this->html = $iframe->get_updated_html();
		}

		return parent::get_data();
	}

}
