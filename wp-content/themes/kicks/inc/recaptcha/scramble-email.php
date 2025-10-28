<?php
/**
 * Email obfuscation.
 *
 * @package Kicks
 */

/**
 * Obfuscates the email in page body or menu items.
 *
 * @param string $content string containing emails to be obfuscated.
 */
function obfuscate_email_link( $content ) {
	if ( ! defined( 'RECAPTCHA_PUBLIC_KEY' ) ) {
		return $content;
	}
	return preg_replace_callback(
		'/href="mailto:([^"]+)"/',
		function( $email ) {
			//PHPCS:ignore
			$rotated = str_rot13( $email[1] );
			return "href=\"#\" data-mailto=\"{$rotated}\"";
		},
		$content
	);
}

/**
 * User function to obfuscate emails.
 *
 * @param string $email the email to be obfuscated.
 */
function obfuscate_email( $email ) {
	if ( ! defined( 'RECAPTCHA_PUBLIC_KEY' ) ) {
		return $email;
	}
	//PHPCS:ignore
	return str_rot13( $email );
}


add_filter( 'the_content', 'obfuscate_email_link' );
add_filter(
	'obfuscate_email_link',
	function( $item_output ) {
		return obfuscate_email( $item_output );
	}
);
