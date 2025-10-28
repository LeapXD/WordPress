<?php
/**
 * Twig template context.
 *
 * @package Kicks
 *
 * @var \XD\Types\XD_Template_Props $template_props
 * @var \XD\Types\XD_Post $post
 * @var \Timber\PostQuery<\XD\Types\XD_Post> $posts
 * @var \Timber\Theme $theme
 * @var \Timber\Site $site
 * @var \Timber\User $user
 * @var \Timber\Request $request
 * @phpcs:disable WordPress.WP.GlobalVariablesOverride.Prohibited
 */

use XD\Types\XD_Link;
use XD\Types\XD_Template_Props;
use XD\Types\XD_Type;

use function Leap\Editor\Block_Render\xd_render_block;

$template_parts_query = new WP_Query(
	array(
		'post_type'      => 'wp_template_part',
		'posts_per_page' => -1,
		'order'          => 'ASC',
	)
);

$template_parts          = array();
$parsed_template_parts   = array();
$original_template_props = new XD_Template_Props();

foreach ( $template_parts_query->get_posts() as $template_part ) {
	$parsed_blocks = parse_blocks( $template_part->post_content );
	$option_field  = new XD_Type();

	foreach ( $parsed_blocks as $parsed_block ) {
		$block = new WP_Block( $parsed_block );
		if ( empty( $block->name ) ) {
			continue;
		}
		$block_name = preg_replace( '|([^/]+/)|', '', $block->name );
		$option_field->import( $block->attributes );
		$option_field->import( array( 'content' => $block->render() ) );
		$template_parts[ $block_name ] = $option_field;
		// Allow child theme to access the parsed template parts since some of the template parts are modified below.
		$parsed_template_parts[ $block_name ] = $option_field;
	}
}

if ( ! empty( $template_parts['company-details']->social_share_image['url'] ) ) {
	/**
	 * Globally replaces the social share image
	 * from featured image to a specifically selected image.
	 */
	$change_opengraph_image_url = function () use ( $template_parts ) {
		return $template_parts['company-details']->social_share_image['url'];
	};
	add_filter( 'wpseo_opengraph_image', $change_opengraph_image_url );
}

if ( ! empty( $template_parts['footer'] ) ) {

	if ( ! empty( $template_parts['footer']->footer_logo['link'] ) &&
	! empty( $template_parts['footer']->footer_logo['image'] ) ) {
		$logo = new XD_Link();
		$logo->import(
			array(
				'image' => xd_get_picture(
					$template_parts['footer']->footer_logo['image']['id'],
					array(
						'breakpoints' => array( 350 ),
					)
				),
			)
		);
		$logo->import( $template_parts['footer']->footer_logo['link'] );
		$template_parts['footer']->footer_logo = $logo;
	} else {
		$template_parts['footer']->footer_logo = null;
	}
}

foreach ( $template_parts as $key => $value ) {
	$template_props->import( array( $key => $value ) );
	// Allow child theme to access the original template_props since some fields are modified below.
	$original_template_props->import( array( $key => $value ) );
}

if ( ! empty( $template_parts['footer'] ) ) {
	if ( 'footer-top-small' === $template_props->footer->footer_type ) {
		$template_props->footer_top   = true;
		$template_props->footer_small = true;
	}

	if ( 'footer-top-full' === $template_props->footer->footer_type ) {
		$template_props->footer_top   = true;
		$template_props->footer_small = false;
	}
}

// Nav button 1.
if ( ! empty( $template_props->global_buttons->nav_buttons['navButton1'] ) ) {
	$nav_button_1 = xd_render_block(
		'xd/button',
		array_merge(
			$template_props->global_buttons->nav_buttons['navButton1'],
			array(
				'text'        => $template_props->global_buttons->nav_buttons['navButton1']['title'],
				'buttonStyle' => 'xd-button--inverse',
			)
		)
	);
	$needle       = 'class="';
	$pos          = strpos( $nav_button_1, $needle );
	$template_props->global_buttons->nav_buttons['navButton1']['content'] = substr_replace(
		$nav_button_1,
		'role="menuitem" class="',
		$pos,
		strlen( $needle )
	);
	if ( ! empty( $template_props->global_buttons->nav_buttons['navButton1']['entityId'] ) &&
	! empty( $template_props->global_buttons->nav_buttons['navButton1']['type'] ) ) {
		if ( 'modal' === $template_props->global_buttons->nav_buttons['navButton1']['type'] ) {
			$popups[] = $template_props->global_buttons->nav_buttons['navButton1']['entityId'];
		}
		if ( 'flyout' === $template_props->global_buttons->nav_buttons['navButton1']['type'] ) {
			$flyouts[] = $template_props->global_buttons->nav_buttons['navButton1']['entityId'];
		}
	}
}

// Nav button 2.
if ( ! empty( $template_props->global_buttons->nav_buttons['navButton2'] ) ) {
	$nav_button_2 = xd_render_block(
		'xd/button',
		array_merge(
			$template_props->global_buttons->nav_buttons['navButton2'],
			array(
				'text'        => $template_props->global_buttons->nav_buttons['navButton2']['title'],
				'buttonStyle' => 'xd-button--inverse-ghost',
			)
		)
	);
	$needle       = 'class="';
	$pos          = strpos( $nav_button_2, $needle );
	$template_props->global_buttons->nav_buttons['navButton2']['content'] = substr_replace(
		$nav_button_2,
		'role="menuitem" class="',
		$pos,
		strlen( $needle )
	);
	if ( ! empty( $template_props->global_buttons->nav_buttons['navButton2']['entityId'] ) &&
	! empty( $template_props->global_buttons->nav_buttons['navButton2']['type'] ) ) {
		if ( 'modal' === $template_props->global_buttons->nav_buttons['navButton2']['type'] ) {
			$popups[] = $template_props->global_buttons->nav_buttons['navButton2']['entityId'];
		}
		if ( 'flyout' === $template_props->global_buttons->nav_buttons['navButton2']['type'] ) {
			$flyouts[] = $template_props->global_buttons->nav_buttons['navButton2']['entityId'];
		}
	}
}

	// Side sticky button.
if ( ! empty( $template_props->global_buttons->side_sticky_button ) ) {
	$template_props->global_buttons->side_sticky_button['content'] = xd_render_block(
		'xd/button',
		array_merge(
			$template_props->global_buttons->side_sticky_button,
			array( 'text' => $template_props->global_buttons->side_sticky_button['title'] )
		)
	);
	if ( ! empty( $template_props->global_buttons->side_sticky_button['entityId'] ) &&
	! empty( $template_props->global_buttons->side_sticky_button['type'] ) ) {
		if ( 'modal' === $template_props->global_buttons->side_sticky_button['type'] ) {
			$popups[] = $template_props->global_buttons->side_sticky_button['entityId'];
		}
		if ( 'flyout' === $template_props->global_buttons->side_sticky_button['type'] ) {
			$flyouts[] = $template_props->global_buttons->side_sticky_button['entityId'];
		}
	}
}

		// Sticky button 1.
if ( ! empty( $template_props->global_buttons->sticky_buttons['sticky1'] ) ) {
	$template_props->global_buttons->sticky_buttons['sticky1']['content'] = xd_render_block(
		'xd/button',
		array_merge(
			$template_props->global_buttons->sticky_buttons['sticky1'],
			array(
				'text' => $template_props->global_buttons->sticky_buttons['sticky1']['title'],
			)
		)
	);
	if ( ! empty( $template_props->global_buttons->sticky_buttons['sticky1']['entityId'] ) &&
	! empty( $template_props->global_buttons->sticky_buttons['sticky1']['type'] ) ) {
		if ( 'modal' === $template_props->global_buttons->sticky_buttons['sticky1']['type'] ) {
			$popups[] = $template_props->global_buttons->sticky_buttons['sticky1']['entityId'];
		}
		if ( 'flyout' === $template_props->global_buttons->sticky_buttons['sticky1']['type'] ) {
			$flyouts[] = $template_props->global_buttons->sticky_buttons['sticky1']['entityId'];
		}
	}
}

// Sticky button 2.
if ( ! empty( $template_props->global_buttons->sticky_buttons['sticky2'] ) ) {
	$template_props->global_buttons->sticky_buttons['sticky2']['content'] = xd_render_block(
		'xd/button',
		array_merge(
			$template_props->global_buttons->sticky_buttons['sticky2'],
			array(
				'text' => $template_props->global_buttons->sticky_buttons['sticky2']['title'],
			)
		)
	);
	if ( ! empty( $template_props->global_buttons->sticky_buttons['sticky2']['entityId'] ) &&
	! empty( $template_props->global_buttons->sticky_buttons['sticky2']['type'] ) ) {
		if ( 'modal' === $template_props->global_buttons->sticky_buttons['sticky2']['type'] ) {
			$popups[] = $template_props->global_buttons->sticky_buttons['sticky2']['entityId'];
		}
		if ( 'flyout' === $template_props->global_buttons->sticky_buttons['sticky2']['type'] ) {
			$flyouts[] = $template_props->global_buttons->sticky_buttons['sticky2']['entityId'];
		}
	}
}

// Flyout button (in menu).
if ( ! empty( $template_props->global_buttons->flyout_button ) ) {
	$template_props->global_buttons->flyout_button['content'] = xd_render_block(
		'xd/button',
		array_merge(
			$template_props->global_buttons->flyout_button,
			array( 'text' => $template_props->global_buttons->flyout_button['title'] )
		)
	);
	if ( ! empty( $template_props->global_buttons->flyout_button['entityId'] ) &&
	! empty( $template_props->global_buttons->flyout_button['type'] ) ) {
		if ( 'modal' === $template_props->global_buttons->flyout_button['type'] ) {
			$popups[] = $template_props->global_buttons->flyout_button['entityId'];
		}
		if ( 'flyout' === $template_props->global_buttons->flyout_button['type'] ) {
			$flyouts[] = $template_props->global_buttons->flyout_button['entityId'];
		}
	}
}

// Footer newsletter button.
if ( ! empty( $template_props->footer->footer_buttons['newsletterLink'] ) ) {
	// for backward compatibility with newsletterLink, we render to newsletterButton field.
	$template_props->footer->footer_buttons['newsletterButton'] = array(
		'attributes' => $template_props->footer->footer_buttons['newsletterLink'],
		'content'    => xd_render_block(
			'xd/button',
			array_merge(
				$template_props->footer->footer_buttons['newsletterLink'],
				array( 'text' => $template_props->footer->footer_buttons['newsletterLink']['title'] )
			)
		),
	);

	if ( ! empty( $template_props->footer->footer_buttons['newsletterLink']['entityId'] ) &&
	! empty( $template_props->footer->footer_buttons['newsletterLink']['type'] ) ) {
		if ( 'modal' === $template_props->footer->footer_buttons['newsletterLink']['type'] ) {
			$popups[] = $template_props->footer->footer_buttons['newsletterLink']['entityId'];
		}
		if ( 'flyout' === $template_props->footer->footer_buttons['newsletterLink']['type'] ) {
			$flyouts[] = $template_props->footer->footer_buttons['newsletterLink']['entityId'];
		}
	}
}
