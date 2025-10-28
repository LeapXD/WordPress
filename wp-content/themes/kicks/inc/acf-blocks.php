<?php
/**
 * ACF block registration.
 *
 * @package Kicks
 */

/**
 * ACF Register ACF blocks.
 */
function xd_register_acf_blocks() {

	$acf_blocks = array(

		'instagram-feed'      => array(
			'name'            => 'instagram-feed',
			'title'           => __( 'Instagram Feed' ),
			'description'     => __( 'Displays your Instagram feed' ),
			'render_callback' => 'render_instagram_feed',
			'category'        => 'standard',
			'icon'            => 'instagram',
			'keywords'        => array( 'carousel' ),
			'mode'            => 'edit',
			'supports'        => array(
				'mode'  => false,
				'align' => false,
			),
		),
		'amenities-map'       => array(
			'name'        => 'amenities-map',
			'title'       => __( 'Amenities Map' ),
			'description' => __( 'Displays a Google map with amenities' ),
			'category'    => 'standard',
			'icon'        => 'admin-site-alt2',
			'keywords'    => array( 'amenities map', 'google map' ),
			'mode'        => 'edit',
			'supports'    => array(
				'mode'  => false,
				'align' => false,
			),
		),
		'single-location-map' => array(
			'name'        => 'single-location-map',
			'title'       => __( 'Single Location Map' ),
			'description' => __( 'Single Location Map' ),
			'mode'        => 'edit',
			'category'    => 'standard',
			'icon'        => 'admin-site-alt2',
			'keywords'    => array( 'map', 'google map' ),
		),
	);

	$acf_blocks = apply_filters( 'xd_register_acf_blocks', $acf_blocks );
	foreach ( $acf_blocks as $acf_block ) {
		acf_register_block( $acf_block );
	}

}

add_action( 'acf/init', 'xd_register_acf_blocks' );
