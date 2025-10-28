<?php
/**
 * Block context
 *
 * @package Kicks
 *
 * ACF map context
 */

use Timber\Timber;

$fields['locations'] = is_array( $fields['locations'] ) ? $fields['locations'] : array();

$locations = array_map(
	function( $location ) {
		$fields   = get_fields( $location->ID );
		$terms    = get_the_terms( $location->ID, 'pin_category' );
		$category = null;
		$colour   = '';

		if ( isset( $terms[0] ) ) {
			$colour   = get_term_meta( $terms[0]->term_id, 'colour', true );
			$get_icon = get_term_meta( $terms[0]->term_id, 'icon', true );
			$icon     = wp_get_attachment_image_src( $get_icon );
		}

		if ( empty( $location->location ) ) {
			return null;
		}
		return array_merge(
			array( 'ID' => $location->ID ),
			array(
				'styles' => array(
					'icon'   => $icon ? $icon[0] : null,
					'width'  => $icon ? $icon[1] : null,
					'height' => $icon ? $icon[2] : null,
					'colour' => $colour,
				),
			),
			array(
				'content_string' => Timber::Compile(
					'partials/modal/google-maps-info-window.twig',
					array(
						'location' => array_merge(
							$location->location,
							array(
								'name'             => $location->post_title,
								'instagram_handle' => ! empty( $fields['instagram_handle'] ) ? $fields['instagram_handle'] : null,
								'instagram_link'   => ! empty( $fields['instagram_link'] ) ? $fields['instagram_link'] : null,
								'url'              => ! empty( $fields['url'] ) ? $fields['url'] : null,
							),
						),
					)
				),
			),
			$fields,
		);

	},
	$fields['locations']
);

$locations = array_values( array_filter( $locations ) );

if ( $fields['centre_pin'] ) {
	$centre_pin = array(
		'styles'         => array(
			'icon'   => ! empty( $fields['centre_pin_icon']['url'] ) ? $fields['centre_pin_icon']['url'] : '',
			'width'  => ! empty( $fields['centre_pin_icon']['width'] ) ? $fields['centre_pin_icon']['width'] : '',
			'height' => ! empty( $fields['centre_pin_icon']['height'] ) ? $fields['centre_pin_icon']['width'] : '',
			'colour' => $fields['centre_pin_color'],
		),
		'location'       => $fields['centre_pin'],
		'content_string' => Timber::Compile(
			'partials/modal/google-maps-info-window.twig',
			array(
				'location' => array_merge( $fields['centre_pin'], array( 'name' => $fields['centre_pin_label'] ) ),
			),
		),
	);
}

$categories = get_terms( array( 'taxonomy' => 'pin_category' ) );
if ( $categories ) {
	foreach ( $categories as &$category ) {
		if ( ! empty( $category->colour ) ) {
			$category->colour = get_term_meta( $category->term_id, 'colour', true );
		}
	};
}

$map_styles = get_field( 'snazzy_maps_json', 'options' );
$decoded    = json_decode( $map_styles );
if ( $decoded ) {
	$map_styles = $decoded;
}

$map_data = array(
	'locations'  => $locations,
	'centre_pin' => isset( $centre_pin ) ? $centre_pin : null,
	'min_zoom'   => $fields['min_zoom'],
	'max_zoom'   => $fields['max_zoom'],
	'map_styles' => $map_styles,
);

$context['locations']  = $locations;
$context['categories'] = $categories;
$context['map_data']   = $map_data;
