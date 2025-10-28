<?php
/**
 * Image setup and tools.
 *
 * @package Kicks
 */

add_filter(
	'big_image_size_threshold',
	function( $max, $imagesize, $file, $attachment_id ) {

		$width  = $imagesize[0];
		$height = $imagesize[1];

		$max = 4096;
		// If the width is less than 1600, we'll assume it's a mobile cropped image
		// and cap the size so that raw image uploads are never shown on mobile.
		if ( 1600 > $width ) {
			$max = $width - 2;
		}
		return $max;
	},
	10,
	4
);


add_filter(
	'webp_uploads_discard_larger_generated_images',
	'__return_false'
);

add_filter(
	'webp_uploads_image_sizes_with_additional_mime_type_support',
	function( $allowed_sizes ) {
		foreach ( $allowed_sizes as $key => $allowed_size ) {
			$allowed_sizes[ $key ] = true;
		}
		return $allowed_sizes;
	}
);

/**
 * Setup theme image sizes.
 */
function xdf_add_image_sizes() {

	$width_mobile = 428;

	$width_md  = $width_mobile * 2;
	$width_lg  = 1024;
	$width_xl  = 1440;
	$width_xxl = 1920;

	$modal_height_sm = 650;
	$modal_height_lg = 900;

	$max_height = 4096;

	// default image size suite - note WordPress also adds  768, 1536, and 2048 sizes.
	add_image_size( 'img', $width_mobile, $max_height );
	// Additional sizes - Don't use directly in templates, allow srcset to generate sizes.
	add_image_size( 'img-md', $width_md, $max_height );
	add_image_size( 'img-lg', $width_lg, $max_height );
	add_image_size( 'img-xl', $width_xl, $max_height );
	add_image_size( 'img-xxl', $width_xxl, $max_height );

	// Media galleries / two-tile (cropped square).
	add_image_size( 'img-square', $width_mobile, $width_mobile, true );
	// Additional sizes - Don't use directly in templates, allow srcset to generate sizes.
	add_image_size( 'img-square-md', $width_md, $width_md, true );
	add_image_size( 'img-square-lg', $width_lg, $width_lg, true );
	add_image_size( 'img-square-xl', $width_xl, $width_xl, true );

	// Modal gallery - height restricted to fit screen + allow room for slider nav & header.
	add_image_size( 'img-modal', $width_mobile, $modal_height_sm );
	// Additional sizes - Don't use directly in templates, allow srcset to generate sizes .
	add_image_size( 'img-modal-md', $width_md, $modal_height_lg );
	add_image_size( 'img-modal-lg', $width_xxl, $modal_height_lg );
	add_image_size( 'img-modal-2x', $width_xl * 2, $modal_height_lg * 2 );

}
add_action( 'init', 'xdf_add_image_sizes' );


/**
 * Register the three useful image sizes for use in Add Media modal
 *
 * @param array $sizes the original sizes.
 */
function xdf_image_sizes( $sizes ) {
	return array_merge(
		$sizes,
		array(
			'img'           => __( 'Standard', 'xd' ),
			'img-md'        => __( 'Medium', 'xd' ),
			'img-lg'        => __( 'L', 'xd' ),
			'img-xl'        => __( 'XL', 'xd' ),
			'img-xxl'       => __( 'XXL', 'xd' ),

			'img-square-md' => __( 'Square Medium', 'xd' ),
			'img-square-lg' => __( 'Square L', 'xd' ),
			'img-square-xl' => __( 'Square XL', 'xd' ),

			'img-modal'     => __( 'Portrait', 'xd' ),
			'img-modal-lg'  => __( 'Portrait L', 'xd' ),
			'img-modal-2x'  => __( 'Portrait @2X', 'xd' ),
		)
	);
}
add_filter( 'image_size_names_choose', 'xdf_image_sizes' );

/**
 * Gets a webp srcset from the original srcset.
 *
 *  @param string $srcset is the original image source set string.
 *  @param int    $id the image id.
 */
function xd_get_webp_srcset( $srcset, $id ) {
	if ( ! function_exists( 'webp_uploads_get_content_image_mimes' ) ) {
		return $srcset;
	}

	$metadata     = wp_get_attachment_metadata( $id );
	$context      = 'the_content';
	$target_mimes = webp_uploads_get_content_image_mimes( $id, $context );

	if ( empty( $metadata ) ) {
		return $srcset;
	}

	if ( ! isset( $metadata['sources'] ) ) {
		return $srcset;
	}

	if ( ! array_key_exists( 'image/webp', $metadata['sources'] ) ) {
		return $srcset;
	}

	foreach ( $metadata['sizes'] as $size_data ) {
		if ( empty( $size_data['file'] ) ) {
			continue;
		}

		foreach ( $target_mimes as $target_mime ) {
			if ( 'image/webp' !== $target_mime ) {
				continue;
			}

			if ( ! isset( $size_data['sources'][ $target_mime ]['file'] ) ) {
				continue;
			}

			if ( $size_data['file'] === $size_data['sources'][ $target_mime ]['file'] ) {
				continue;
			}

			if ( $size_data['sources'][ $target_mime ]['file'] ) {
				$srcset = str_replace(
					$size_data['file'],
					$size_data['sources'][ $target_mime ]['file'],
					$srcset
				);
			}
		}
	}

	$sources = $metadata['sources'];

	if ( ! empty( $sources['image/webp'] ) && ! empty( $sources['image/jpeg'] ) ) {
		// replace the source (largest) image source file with webp.
		$srcset = str_replace(
			$sources['image/jpeg']['file'],
			$sources['image/webp']['file'],
			$srcset
		);
	}

	return $srcset;
}


/**
 * Generate HTML 5 Picture element
 *
 * @param int   $primary_id The primary image ID.
 * @param mixed $mobile_id The mobile image ID.
 * @param array $args Args
 *                          [size] => 'img' / 'img-square' / 'img-modal'
 *
 *                          [size_mobile] => 'img' / 'img-square' / 'img-modal'
 *
 *                          [breakpoint] => When to switch to large image eg. 'lg'
 *
 *                          [breakpoints] => width of image in viewport eg. ['lg' => '50vw', 'xxl' => '33vw']
 *
 *                          [media] => manually set media query
 *
 *                          [class] => picture wrapper classes
 *
 *                          [img_class] => img element classes
 *
 *                          [attributes] => picture wrapper attributes
 *
 *                          [img_attributes] => img wrapper attributes.
 * @return string HTML5 Picture element markup.
 */
function xd_get_picture( $primary_id, $mobile_id = null, $args = null ) {

	if ( empty( $primary_id ) && empty( $mobile_id ) ) {
		return '';
	}

	// Allow args to be passed as 2nd parameter when no mobile id is set.
	if ( is_array( $mobile_id ) && ! empty( $mobile_id ) ) {
		$args      = $mobile_id;
		$mobile_id = null;
	}

	// Allow mobile id to be used as fallback for primary id when primary id is not set.
	if ( empty( $primary_id ) && ! empty( $mobile_id ) ) {
		$primary_id = $mobile_id;
	}

	$args = wp_parse_args(
		$args,
		array(
			'size'           => 'img',
			'size_mobile'    => null,
			'breakpoint'     => 'md',
			'breakpoints'    => array(),
			'class'          => '',
			'img_class'      => '',
			'attributes'     => array(),
			'img_attributes' => array(),
			'lazyload'       => true,
			'use_image_size' => false,
		)
	);

	$media_queries = array(
		'sm'     => '575px',
		'md'     => '767px',
		'lg'     => '991px',
		'xl'     => '1199px',
		'xxl'    => '1439px',
		'xxxl'   => '1799px',
		'hd'     => '1919px',
		'retina' => '2559px',
		'4k'     => '4055px',
	);

	if ( empty( $mobile_id ) ) {
		$args['breakpoint'] = null;
	}

	if ( empty( $args['size_mobile'] ) ) {
		$args['size_mobile'] = $args['size'];
	}

	$attributes     = '';
	$img_attributes = '';
	$breakpoints    = array();
	$primary_type   = get_post_mime_type( $primary_id );

	if ( ! empty( $args['lazyload'] ) ) {
		$args['img_attributes']['loading'] = 'lazy';
		$args['img_class']                .= ' lazyload ';
	}

	foreach ( $args['img_attributes'] as $attribute => $value ) {
		$img_attributes .= $attribute . '=' . esc_attr( $value ) . ' ';
	}

	foreach ( $args['attributes'] as $attribute => $value ) {
		$attributes .= $attribute . '=' . esc_attr( $value );
	}

	$primary_img = wp_get_attachment_image_src( $primary_id, 'full' );
	$mobile_img  = null;

	if ( ! empty( $mobile_id ) ) {
		$mobile_img = wp_get_attachment_image_src( $mobile_id, 'full' );
	}

	if ( ! empty( $args['use_image_size'] ) ) {
		$primary_img_width = isset( $primary_img[1] ) ? $primary_img[1] / 2 : 0;
		$primary_sizes     = $primary_img_width . 'px';

		if ( ! empty( $mobile_id ) ) {
			$mobile_img_width = isset( $mobile_img[1] ) ? $mobile_img[1] / 2 : 0;
			$mobile_sizes     = $mobile_img_width . 'px';
		}
	} else {
		foreach ( $args['breakpoints'] as $key => $size ) {
			if ( isset( $media_queries[ $key ] ) ) {
				$breakpoints[] = "(min-width: {$media_queries[$key]}) $size";
			}
		}
		if ( ! empty( $args['breakpoints'][0] ) ) {
			$breakpoints[] = $args['breakpoints'][0];
			$sizes         = implode( ', ', $breakpoints );
		} elseif ( ! empty( $args['breakpoints']['xs'] ) ) {
			$breakpoints[] = $args['breakpoints']['xs'];
			$sizes         = implode( ', ', $breakpoints );
		} elseif ( ! empty( $breakpoints ) ) {
			$breakpoints[] = 'auto';
			$sizes         = implode( ', ', $breakpoints );
		} elseif ( strpos( $args['size'], '-modal' ) ) {
			$sizes = wp_get_attachment_image_sizes( $primary_id, 'img-modal-2x' );
		} else {
			$sizes = 'auto';
		}
		$primary_sizes = $sizes;
		$mobile_sizes  = $sizes;
	}

	$picture_sources = array();
	$media           = ! empty( $args['breakpoint'] ) ? "media=\"(max-width: {$media_queries[$args['breakpoint']]})\"" : '';
	if ( ! empty( $args['media'] ) ) {
		$media = "media=\"{$args['media']}\"";
	}
	$primary_srcset      = wp_get_attachment_image_srcset( $primary_id, $args['size'] );
	$primary_webp_srcset = xd_get_webp_srcset( $primary_srcset, $primary_id );
	$primary_img_width   = isset( $primary_img[1] ) ? $primary_img[1] / 2 : 0;
	$primary_img_height  = isset( $primary_img[2] ) ? $primary_img[2] / 2 : 0;

	if ( str_contains( $primary_type, 'svg' ) && ! empty( $primary_img ) ) {
		$primary_srcset = $primary_img[0] . ' ' . $primary_img[1] . 'w';
	}

	if ( ! empty( $mobile_id ) ) {
		$mobile_srcset      = wp_get_attachment_image_srcset( $mobile_id, $args['size_mobile'] );
		$mobile_webp_srcset = xd_get_webp_srcset( $mobile_srcset, $mobile_id );
		$mobile_type        = get_post_mime_type( $mobile_id );
		$mobile_img_width   = isset( $mobile_img[1] ) ? $mobile_img[1] / 2 : 0;
		$mobile_img_height  = isset( $mobile_img[2] ) ? $mobile_img[2] / 2 : 0;

		if ( str_contains( $mobile_type, 'svg' ) && ! empty( $mobile_img ) ) {
			$mobile_srcset = $mobile_img[0] . ' ' . $mobile_img[1] . 'w';
		}

		if ( ! empty( $mobile_webp_srcset ) && $mobile_webp_srcset !== $mobile_srcset ) {
			$picture_sources[] = sprintf(
				'<source data-id="%s" %s srcset="%s" sizes="%s" width="%s" height="%s" type="image/webp" />',
				$mobile_id,
				$media,
				$mobile_webp_srcset,
				$mobile_sizes,
				$mobile_img_width,
				$mobile_img_height
			);
		}

		if ( ! empty( $mobile_srcset ) ) {
			$picture_sources[] = sprintf(
				'<source data-id="%s" %s srcset="%s" sizes="%s" width="%s" height="%s" type="%s" />',
				$mobile_id,
				$media,
				$mobile_srcset,
				$mobile_sizes,
				$mobile_img_width,
				$mobile_img_height,
				$mobile_type
			);
		}
	}

	if ( ! empty( $primary_webp_srcset ) && $primary_webp_srcset !== $primary_srcset ) {
		$picture_sources[] = sprintf(
			'<source data-id="%s" srcset="%s" sizes="%s" width="%s" height="%s" type="image/webp" />',
			$primary_id,
			$primary_webp_srcset,
			$primary_sizes,
			$primary_img_width,
			$primary_img_height
		);
	}

	if ( ! empty( $primary_srcset ) ) {
		$picture_sources[] = sprintf(
			'<source data-id="%s" srcset="%s" sizes="%s" width="%s" height="%s" type="%s" />',
			$primary_id,
			$primary_srcset,
			$primary_sizes,
			$primary_img_width,
			$primary_img_height,
			$primary_type
		);
	}

	$sources = implode( "\n", $picture_sources );

	$markup = xd_picture_markup(
		xd_img_markup( $primary_id, $args['class'], $img_attributes ),
		$args['class'],
		$attributes,
		$sources
	);

	return apply_filters( 'xd_get_picture', $markup, $primary_id, $mobile_id, $args );
}

/**
 * Gets The picture markup
 *
 * @param string $img the nested image string.
 * @param string $class picture class list space delimited.
 * @param string $attributes picture attributes.
 * @param string $sources picture sources.
 */
function xd_picture_markup( $img, $class, $attributes = '', $sources = '' ) {

	return <<<PICTURE
<picture
class="$class"
$attributes
>
$sources
$img
</picture>
PICTURE;
}

/**
 * Gets the image markup.
 *
 * @param int    $img_id the id of the image.
 * @param string $class image class list space delimited.
 * @param string $attributes picture attributes.
 */
function xd_img_markup( $img_id, $class = '', $attributes = '' ) {

	$img_dimensions = wp_get_attachment_image_src( $img_id, 'full' );
	$img_src        = wp_get_attachment_image_url( $img_id, 'img' );
	$img_src_md     = wp_get_attachment_image_url( $img_id, 'img-md' );
	$img_srcset     = wp_get_attachment_image_srcset( $img_id, 'full' );
	$img_sizes      = wp_get_attachment_image_sizes( $img_id, 'full' );
	$img_alt        = get_post_meta( $img_id, '_wp_attachment_image_alt', true );
	$img_width      = isset( $img_dimensions[1] ) ? $img_dimensions[1] : 0;
	$img_height     = isset( $img_dimensions[2] ) ? $img_dimensions[2] : 0;
	return <<<IMG
<img
src="$img_src"
data-src="$img_src_md"
data-srcset="$img_srcset"
sizes="$img_sizes"
class="$class"
alt="$img_alt"
width="$img_width"
height="$img_height"
$attributes
/>
IMG;
}
