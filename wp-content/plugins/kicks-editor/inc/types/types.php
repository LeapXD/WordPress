<?php
/**
 * Bootstrap type includes.
 *
 * @package Kicks
 */

/**
 * Include type interfaces.
 */
add_action(
	'plugins_loaded',
	function() {

		require 'class-xd-type-base.php';
		require 'class-xd-block-wrap.php';
		require 'class-xd-template-props.php';
		require 'class-xd-type.php';
	}
);

