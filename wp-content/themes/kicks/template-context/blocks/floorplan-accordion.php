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
use XD\Types\XD_Accordion;

use function Leap\Editor\Block_Render\xd_attribute;

$accordion = new XD_Accordion();

$accordion->class_name[] = 'xd-floorplans';

$elements     = array();
$price_labels = array();

foreach ( $inner_blocks as $inner_block ) {
	$element = Timber::get_post( xd_attribute( 'postId', $inner_block ) );
	foreach ( $element->terms() as $term ) {
		$element->import( array( $term->taxonomy => $term->name ) );
	}

	$price          = ! empty( $element->price ) ? 'From $' . number_format( $element->price ) : '';
	$price_label    = ! empty( $element->floor_plan_label ) ? $element->floor_plan_label : $price;
	$price_labels[] = $price_label;

	$element->import(
		array(
			'content'    => $inner_block->render(),
			'class_name' => 'xd-floorplan',
			'summary'    => array(
				$element->bedroom,
				$element->bathroom,
				$element->sq_ft . ' Sq. Ft.',
				$price_label,
			),
		),
		true
	);

	$elements[] = $element;
}

if ( empty( array_filter( $price_labels ) ) ) {
	foreach ( $elements as &$element ) {
		unset( $element->summary[3] );
	}
}

$accordion->elements  = $elements;
$context['accordion'] = $accordion;
