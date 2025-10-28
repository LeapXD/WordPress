<?php
/**
 * Bootstrap type includes.
 *
 * @package Kicks
 */

/**
 * Include type interfaces.
 */
function xd_load_type_classes() {

	if ( ! class_exists( 'XD\Types\XD_Template_Props' ) ) {
		return;
	}

	require 'class-image.php';
	require 'class-xd-button.php';
	require 'class-xd-accordion.php';
	require 'class-xd-accordion-element.php';
	require 'class-xd-accordion-options.php';
	require 'class-xd-card.php';
	require 'class-xd-flyout.php';
	require 'class-xd-flyout-options.php';
	require 'class-xd-image-options.php';
	require 'class-xd-image.php';
	require 'class-xd-background-image.php';
	require 'class-xd-link.php';
	require 'class-xd-iframe.php';
	require 'class-xd-modal.php';
	require 'class-xd-modal-options.php';
	require 'class-xd-slide.php';
	require 'class-xd-slider.php';
	require 'class-xd-slider-options.php';
	require 'class-video.php';
	require 'class-xd-video.php';

}

add_action( 'after_setup_theme', 'xd_load_type_classes' );
