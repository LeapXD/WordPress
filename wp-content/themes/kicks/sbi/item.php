<?php

/**
 * Smash Balloon Instagram Feed Item Template
 * Adds an image, link, and other data for each post in the feed
 *
 * @version 2.2 Instagram Feed by Smash Balloon
 */

// Don't load directly
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
$classes                 = SB_Instagram_Display_Elements::get_item_classes( $settings, $offset );
$post_id                 = SB_Instagram_Parse::get_post_id( $post );
$timestamp               = SB_Instagram_Parse::get_timestamp( $post );
$media_type              = SB_Instagram_Parse::get_media_type( $post );
$permalink               = SB_Instagram_Parse::get_permalink( $post );
$maybe_carousel_icon     = $media_type === 'carousel' ? SB_Instagram_Display_Elements::get_icon( 'carousel', $icon_type ) : '';
$maybe_video_icon        = $media_type === 'video' ? SB_Instagram_Display_Elements::get_icon( 'video', $icon_type ) : '';
$media_url               = SB_Instagram_Display_Elements::get_optimum_media_url( $post, $settings, $resized_images );
$media_full_res          = SB_Instagram_Parse::get_media_url( $post );
$sbi_photo_style_element = SB_Instagram_Display_Elements::get_sbi_photo_style_element( $post, $settings ); // has already been escaped
$media_all_sizes_json    = SB_Instagram_Parse::get_media_src_set( $post, $resized_images );

/**
 * Text that appears in the "alt" attribute for this image
 *
 * @param string $img_alt full caption for post
 * @param array $post api data for the post
 *
 * @since 2.1.5
 */
$img_alt = SB_Instagram_Parse::get_caption( $post, sprintf( __( 'Instagram post %s', 'instagram-feed' ), $post_id ) );
$img_alt = apply_filters( 'sbi_img_alt', $img_alt, $post );

/**
 * Text that appears in the visually hidden screen reader element
 *
 * @param string $img_screenreader first 50 characters for post
 * @param array $post api data for the post
 *
 * @since 2.1.5
 */
$img_screenreader = substr( SB_Instagram_Parse::get_caption( $post, sprintf( __( 'Instagram post %s', 'instagram-feed' ), $post_id ) ), 0, 50 );
$img_screenreader = apply_filters( 'sbi_img_screenreader', $img_screenreader, $post );

?>
<li class="sbi_item sbi_type_<?php echo esc_attr( $media_type ); ?><?php echo esc_attr( $classes ); ?>"
	id="sbi_<?php echo esc_html( $post_id ); ?>" data-date="<?php echo esc_html( $timestamp ); ?>">
	<div class="sbi_photo_wrap">
		<a class="sbi_photo" href="<?php echo esc_url( $permalink ); ?>" target="_blank" rel="noopener" style="overflow: hidden;"	<?php echo $sbi_photo_style_element; ?>>
			<span class="sbi-screenreader uk-hidden"><?php echo esc_html( $img_screenreader ); ?></span>
			<img src="<?php echo get_template_directory_uri() . '/build/images/png/lazy-loading.png'; ?>" alt="<?php echo esc_attr( $img_alt ); ?>" data-src="<?php echo $media_all_sizes_json['d']; ?>" class="lazyload">
			<div class="logo">
				<svg width="24px" height="24px" viewBox="0 0 24 24" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink"
					xmlns="http://www.w3.org/2000/svg">
					<path
						d="M17.04 0L6.96 0C3.1161 0 0 3.1161 0 6.96L0 17.04C0 20.88 3.12 24 6.96 24L17.04 24C20.8839 24 24 20.8839 24 17.04L24 6.96C24 3.12 20.88 0 17.04 0ZM6.7199 2.40002C4.33403 2.40002 2.3999 4.33415 2.3999 6.72002L2.3999 17.28C2.3999 19.668 4.3319 21.6 6.7199 21.6L17.2799 21.6C19.6658 21.6 21.5999 19.6659 21.5999 17.28L21.5999 6.72002C21.5999 4.33202 19.6679 2.40002 17.2799 2.40002L6.7199 2.40002ZM19.7998 5.69995C19.7998 4.87152 19.1282 4.19995 18.2998 4.19995C17.4714 4.19995 16.7998 4.87152 16.7998 5.69995C16.7998 6.52838 17.4714 7.19995 18.2998 7.19995C19.1282 7.19995 19.7998 6.52838 19.7998 5.69995ZM12 6C15.3137 6 18 8.68629 18 12C18 15.3137 15.3137 18 12 18C8.68629 18 6 15.3137 6 12C6 8.68629 8.68629 6 12 6ZM8.3999 12C8.3999 10.0118 10.0117 8.40002 11.9999 8.40002C13.9881 8.40002 15.5999 10.0118 15.5999 12C15.5999 13.9882 13.9881 15.6 11.9999 15.6C10.0117 15.6 8.3999 13.9882 8.3999 12Z"
						id="Shape" fill="currentColor" fill-rule="evenodd" stroke="none"></path>
				</svg>
			</div>
		</a>
	</div>
</li>
