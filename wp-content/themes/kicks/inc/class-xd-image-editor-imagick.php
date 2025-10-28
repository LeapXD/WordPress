<?php
/**
 *
 * Image editing class.
 *
 * @package Kicks
 */

/**
 * Extends WP_Image_Editor_Imagick to add support for improved webp quality.
 * Currently this will not work on Lando because Lando uses the GD image library.
 */
require_once ABSPATH . WPINC . '/class-wp-image-editor.php';
require_once ABSPATH . WPINC . '/class-wp-image-editor-imagick.php';

/**
 * Extends WP_Image_Editor_Imagick to add support for improved webp quality.
 */
class XD_Image_Editor_Imagick extends WP_Image_Editor_Imagick {

	private const COMPRESSION_JPEG     = 8;
	private const COMPRESSION_JPEG2000 = 9;


	/**
	 * Sets Image Compression quality on a 1-100% scale.
	 *
	 * @param int $quality Compression Quality. Range: [1,100].
	 * @return true|WP_Error True if set successfully; WP_Error on failure.
	 */
	public function set_quality( $quality = null, $dims = array() ) {
		$quality_result = parent::set_quality( $quality );
		if ( is_wp_error( $quality_result ) ) {
			return $quality_result;
		} else {
			$quality = $this->get_quality();
		}

		try {

					// $this->image->setImageCompressionQuality( $quality );
					// $this->image->setImageCompression( self::COMPRESSION_JPEG );

					// // https://imagemagick.org/script/webp.php.
					// $this->image->setOption( 'webp:emulate-jpeg-size', 'true' );

					$this->image->setImageCompressionQuality( 50 );
					$this->image->setCompressionQuality( 50 );
						// $this->image->setOption( 'webp:lossless', 'true' );
					parent::set_quality( 50 );

		} catch ( Exception $e ) {
			return new WP_Error( 'image_quality_error', $e->getMessage() );
		}
		return true;
	}

}

/**
 * Adds for improved webp quality to image editor.
 *
 * @param array $implementations the image editor implementations.
 */
function xd_register_image_editor( $implementations ) {
	array_unshift( $implementations, 'XD_Image_Editor_Imagick' );
	return $implementations;
}

// add_filter( 'wp_image_editors', 'xd_register_image_editor' );
