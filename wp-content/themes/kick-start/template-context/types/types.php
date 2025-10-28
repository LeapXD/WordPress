<?php
/**
 * Bootstrap type includes.
 *
 * @package Kicks
 */

/**
 * Include type interfaces.
 */
function xdc_load_type_classes() {

	if ( ! class_exists( 'XD\Types\XD_Template_Props' ) ) {
		return;
	}

}

add_action( 'after_setup_theme', 'xdc_load_type_classes' );
