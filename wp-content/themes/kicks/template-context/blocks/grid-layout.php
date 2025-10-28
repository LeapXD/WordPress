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

use XD\Types\XD_Image_Options;
use XD\Types\XD_Modal;
use XD\Types\XD_Slider;

use function Leap\Editor\Block_Render\xd_render_block;

$show_modal = $attributes['hasModal'];

$block_props->css_vars = array(
	'--grid-rows'   => implode( ' ', $attributes['rowHeights']['mobile'] ),
	'--grid-rows-m' => implode( ' ', $attributes['rowHeights']['tablet'] ),
	'--grid-rows-l' => implode( ' ', $attributes['rowHeights']['desktop'] ),
);

if ( $show_modal ) {

	$id = uniqid( 'xd-block-gallery-' );

	$images             = array();
	$show_modal         = true;
	$key                = 0;
	$grid_image_options = new XD_Image_Options();

	$slider                    = new XD_Slider();
	$slider->options->cls_page = array( 'modal-slider-open' );
	$slider->class_name        = array( 'xd-slider', 'slider-theme-dark' );
	$slider->id                = $id;

	$modal                          = new XD_Modal();
	$modal->id                      = $id;
	$modal->class_name              = array( 'xd-modal', 'xd-modal--full' );
	$modal->options->cls_page       = array( 'header-theme-transparent' );
	$modal->dataset['modal-slider'] = $slider->id;


	/**
	 * Recursively wrap images in the block and its inner blocks.
	 * Also gets the modal images.
	 *
	 * @param WP_Block $block
	 */
	$get_and_update_images = function ( $block ) use ( &$images, &$get_and_update_images, &$key, $show_modal, $modal ) {
		$inner_content = array();
		if ( ! empty( $block->inner_blocks ) ) {
			foreach ( $block->inner_blocks as $inner_block ) {
				if ( 'xd/image' === $inner_block->name ) {
					$image_attributes = array();
					$dataset          = array();
					if ( $show_modal && empty( $inner_block->attributes['videoModal']['url'] ) ) {
						$image_attributes = array( 'url' => '#' );
						$dataset          = array(
							'ui-toggle' => $modal->id,
							'index'     => $key++,
						);
					}
					$inner_content[] = xd_render_block(
						$inner_block,
						$image_attributes,
						array( 'dataset' => $dataset )
					);
					$images[]        = $inner_block;
				} elseif ( ! empty( $inner_block->inner_blocks ) ) {
					// Non-image block with children: recurse and set its inner_content.
					$child_block     = $get_and_update_images( $inner_block );
					$inner_content[] = $child_block;
				} else {
					// Leaf non-image block: let WP render.
					$inner_content[] = null;
				}
			}
		}
		$block->inner_content = empty( $inner_content ) ? array( '' ) : $inner_content;
		return $block;
	};

	$content = '';
	foreach ( $get_and_update_images( $block )->inner_blocks as $inner_block ) {
		$content .= $inner_block->render();
	}

	$modal_images = array();

	if ( ! empty( $images ) ) {
		$image_options             = new XD_Image_Options();
		$modal_image_options       = new XD_Image_Options();
		$modal_image_options->size = 'img-modal';
		$modal_images              = array();
		$block_props->class_name[] = 'xd-image-gallery--additional';
		foreach ( $images as $image ) {
			$modal_images[] =
			'<div class="xd-modal__image">' .
			xd_render_block(
				$image,
				array( 'videoModal' => null ),
				array( 'options' => $modal_image_options )
			) .
			'</div>';
		}
	}

	$slider->slides = $modal_images;

	$context['slider'] = $slider;
	$context['modal']  = $modal;
}


