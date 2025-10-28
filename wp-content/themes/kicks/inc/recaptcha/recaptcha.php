<?php

namespace Leap\Captcha;

add_action( 'wp_ajax_nopriv_verify_captcha', __NAMESPACE__ . '\verify_captcha' );
add_action( 'wp_ajax_verify_captcha', __NAMESPACE__ . '\verify_captcha' );

function verify_captcha() {

	if ( isset( $_SERVER['HTTP_CF_CONNECTING_IP'] ) ) {
		// incase distributed by cloudflare get the real remote ip.
		$_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_CF_CONNECTING_IP'];
	}

	$secret = RECAPTCHA_PRIVATE_KEY;

	$request = new \WP_Http();
	$body    = array(
		'secret'   => $secret,
		'response' => $_POST['token'],
		'remoteip' => $_SERVER['REMOTE_ADDR'],
	);
	$url     = 'https://www.google.com/recaptcha/api/siteverify';
	$result  = json_decode(
		wp_remote_retrieve_body(
			$request->request(
				$url,
				array(
					'method' => 'POST',
					'body'   => $body,
				)
			)
		)
	);
	header( 'content-type:application/json' );
	// var_dump($result);
	exit( json_encode( array( 'recaptcha' => ! empty( $result->success ) ) ) );
}
