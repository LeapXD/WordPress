<?php
/**
 * Block context
 *
 * @package Leap Floorplans
 *
 * @var array $attributes
 * @var WP_Block $block
 * @var \XD\Types\XD_Template_Props $block_props
 * @var string $content
 * @var array $context
 * @var array<WP_Block> $inner_blocks
 * @var array $parent
 * @var array $templates
 * @var string $variation
 * @var \XD\Types\XD_Block_Wrap $wrap
 * @var array $xd_context
 * @phpcs:disable WordPress.WP.GlobalVariablesOverride.Prohibited
 */

use Timber\Timber;
use XD\Types\XD_Image;
use XD\Types\XD_Modal;
use XD\Types\XD_Slider;

use function Leap\Editor\Block_Render\xd_attribute;

$element           = Timber::get_post( xd_attribute( 'postId', $block ) );
$floor_plan_blocks = parse_blocks( $element->post_content );
$details_block     = $floor_plan_blocks[0];

$floor_plate  = xd_attribute( 'floorPlate', $details_block );
$floor_plates = xd_attribute( 'floorPlates', $details_block );

if ( ! empty( $floor_plate ) ) {
	$floor_plates = array( $floor_plate );
}

$floor_plate_images = array();

foreach ( $floor_plates as $floor_plate ) {
	$floor_plate_image                          = new XD_Image();
	$floor_plate_image->primary                 = $floor_plate;
	$floor_plate_image->options->lazyload       = false;
	$floor_plate_image->options->use_image_size = true;
	$floor_plate_images[]                       = $floor_plate_image->get_data();
}

$slider              = new XD_Slider();
$slider->show_dotnav = false;
foreach ( xd_attribute( 'floorPlans', $details_block ) as $floor_plan ) {
	$floor_plan_image          = new XD_Image();
	$floor_plan_image->primary = $floor_plan;
	$slider->slides[]          = Timber::compile(
		'components/image.twig',
		array(
			'image'     => $floor_plan_image,
			'className' => 'xd-floorplan__image',
		)
	);
}

if ( ! empty( $element->virtual_tour ) ) {
	$modal      = new XD_Modal();
	$modal->id  = $element->id;
	$modal->src = $element->virtual_tour;
	$element->import( array( 'modal' => $modal ) );
}

$element->import(
	array(
		'floor_plates' => $floor_plate_images,
		'slider'       => $slider,
		'units'        => xd_attribute( 'units', $details_block ),
	)
);

// Note: this is for the backend floorplans block only, as flooplan
// elements are rendered directly via server-side render, not via the accordion.
if ( ! empty( $context['request']->get['editor'] ) ) {
	foreach ( $element->terms() as $term ) {
		$element->import( array( $term->taxonomy => $term->name ) );
	}
	$element->import(
		array(
			'summary' => array(
				! empty( $element->floor_plan_label )
				? $element->floor_plan_label
				: 'From $' . number_format( floatval( $element->price ) ),
			),
		)
	);
}
$context['accordionElement'] = $element;
