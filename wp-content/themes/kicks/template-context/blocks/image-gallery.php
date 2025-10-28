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
 * @phpcs:disable Squiz.PHP.CommentedOutCode.Found
 */

use Timber\Timber;
use XD\Types\XD_Image_Options;
use XD\Types\XD_Modal;
use XD\Types\XD_Slider;

use function Leap\Editor\Block_Render\xd_attribute;
use function Leap\Editor\Block_Render\xd_render_block;

$content                        = '';
$layout                         = xd_attribute( 'galleryLayout', $block );
$show_modal                     = xd_attribute( 'hasModal', $block );
$images                         = array();
$slider                         = new XD_Slider();
$grid_image_options             = new XD_Image_Options();
$modal                          = new XD_Modal();
$slider->options->cls_page      = array( 'modal-slider-open' );
$slider->class_name             = array( 'xd-slider', 'slider-theme-dark' );
$slider->id                     = xd_attribute( 'id' );
$modal->id                      = xd_attribute( 'id' );
$modal->class_name              = array( 'xd-modal', 'xd-modal--full' );
$modal->options->cls_page       = array( 'header-theme-transparent' );
$modal->dataset['modal-slider'] = $slider->id;

// We can't do array_slice on a WP_Block_List, so we have to pull the images out into an array first.
foreach ( $inner_blocks as $inner_block ) {
	$images[] = $inner_block;
}

$additional_images = false;

switch ( true ) {
	case ( '1' === $layout || '1l' === $layout || '1r' === $layout ):
		$grid_images = $images;
		break;
	case ( '4' === $layout ):
		$grid_images = array_slice( $images, 0, 4 );
		if ( count( $images ) > 4 ) {
			$additional_images = true;
		}
		break;
	case '5l' === $layout || '5c' === $layout || '5r' === $layout:
		if ( count( $images ) > 5 ) {
			$additional_images = true;
		}
		$grid_images = array_slice( $images, 0, 5 );
		break;
	case ( '8' === $layout ):
		if ( count( $images ) > 8 ) {
			$additional_images = true;
		}
		$grid_images = array_slice( $images, 0, 8 );
		break;
}

$grid_image_options->size = 'img-square';


foreach ( $grid_images as $key => $grid_image ) {
	$image_content                   = '';
	$grid_image_options->breakpoints = array(
		'xs' => '50vw',
		'lg' => '25vw',
	);

	if ( (
		'5l' === $layout && 0 === $key ) ||
		( '5c' === $layout && 2 === $key ||
		( '5r' === $layout && 4 === $key ) ) ) {
			$grid_image_options->breakpoints = array(
				'xs' => '100vw',
				'lg' => '50vw',
			);
	}

	if ( '1' === $layout ) {
		$grid_image_options->size        = 'img';
		$grid_image_options->breakpoints = array(
			'xs' => '100vw',
		);
	}

	if ( '1l' === $layout || '1r' === $layout ) {
		$grid_image_options->size        = 'img';
		$grid_image_options->breakpoints = array(
			'xs' => '95vw',
		);
	}

	if ( $show_modal && $additional_images ) {
		if ( '5l' === $layout || '5c' === $layout || '5r' === $layout ) {
			if ( 2 === $key ) {
				$image_content = Timber::compile(
					'components/image-gallery-count.twig',
					array(
						'className' => 'xd-image-gallery__count xd-image-gallery__count--tablet',
					)
				);
			}

			if ( 4 === $key ) {
				$image_content = Timber::compile( 'components/image-gallery-count.twig', array( 'className' => 'xd-image-gallery__count' ) );
			}
		}

		if ( '4' === $layout ) {
			if ( 3 === $key ) {
				$image_content = Timber::compile( 'components/image-gallery-count.twig', array( 'className' => 'xd-image-gallery__count' ) );
			}
		}

		if ( '8' === $layout ) {
			if ( 7 === $key ) {
				$image_content = Timber::compile( 'components/image-gallery-count.twig', array( 'className' => 'xd-image-gallery__count' ) );
			}
		}
	}

	$grid_image_attributes = array();
	$dataset               = array();
	if ( $show_modal && empty( $grid_image->attributes['videoModal']['url'] ) ) {
		$grid_image_attributes = array( 'url' => '#' );
		$dataset               = array(
			'ui-toggle' => $modal->id,
			'index'     => $key,
		);
	}

	$content .= xd_render_block(
		$grid_image,
		$grid_image_attributes,
		array(
			'options' => $grid_image_options,
			'dataset' => $dataset,
			'content' => $image_content,
		)
	);
}

if ( $show_modal ) {
	$modal_image_options       = new XD_Image_Options();
	$modal_image_options->size = 'img-modal';
	$modal_images              = array();
	$block_props->class_name[] = 'xd-image-gallery--additional';
	foreach ( $inner_blocks as $inner_block ) {
		// This will invoke the the render function of the xd/image block.
		// The data below will be passed into the image context file, and it will render using the image template.
		$modal_images[] =
		'<div class="xd-modal__image">' .
		xd_render_block(
			$inner_block,
			array( 'videoModal' => null ),
			array( 'options' => $modal_image_options )
		) .
		'</div>';
	}

	$slider->slides = $modal_images;

	$context['slider'] = $slider;
	$context['modal']  = $modal;
}
