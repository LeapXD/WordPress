<?php
/**
 * Block context
 *
 * @package Kicks
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

use XD\Types\XD_Accordion;
use XD\Types\XD_Accordion_Element;

use function Leap\Editor\Block_Render\xd_attribute;
use function Leap\Editor\Block_Render\xd_get_block_props;
use function Leap\Editor\Block_Render\xd_render_block;

$accordion                    = new XD_Accordion();
$accordion->options->multiple = $attributes['accordionMultipleOpen'];

foreach ( $inner_blocks as $inner_block ) {
	$element               = new XD_Accordion_Element();
	$element->class_name   = xd_get_block_props( $inner_block )->block->class_name;
	$element->title        = xd_attribute( 'accordionTitle', $inner_block );
	$element->content      = xd_render_block( $inner_block );
	$accordion->elements[] = $element;
}

$context['accordion'] = $accordion;

