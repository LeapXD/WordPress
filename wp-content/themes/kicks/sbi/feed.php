<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
$username       = SB_Instagram_Parse::get_username( $header_data );
$num_setting    = $settings['num'];
$shortcode_atts = json_decode( $shortcode_atts );
$display        = isset( $shortcode_atts->display ) ? $shortcode_atts->display : '';
$feed_title     = isset( $shortcode_atts->title ) ? $shortcode_atts->title : '';
$number         = isset( $shortcode_atts->number ) ? $shortcode_atts->number : '';


?>
<div class="instagram-feed">
	<div class="ig_handle xd-pt--sm">
		<a href="https://www.instagram.com/<?php echo urlencode( $username ); ?>/" target="_blank"
			rel="noopener noreferrer">
			<h5><?php echo esc_html( $feed_title ); ?></h5>
		</a>
	</div>
	<div id="sb_instagram--<?php echo esc_attr( $feed_id ); ?>" class="sb_instagram_grid sbi"
		data-num="4">
		<ul class="uk-grid" uk-grid>
			<?php	$this->posts_loop( $posts, $settings ); ?>
		</ul>
	</div>
</div>
