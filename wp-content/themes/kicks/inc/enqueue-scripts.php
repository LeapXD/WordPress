<?php
/**
 * Enqueue scripts and styles.
 *
 * Note: theme style and scripts are being loaded in header and footer
 * respectively for optimization purposes.
 *
 * @package Kicks.
 */

// phpcs:disable Squiz.PHP.CommentedOutCode.Found
// phpcs:disable Squiz.Commenting.InlineComment.InvalidEndChar

/*******************************************
 * ENQUEUE FRONT-END ASSETS.               *
 ******************************************/
function xd_enqueue_scripts() {

	$assets = xd_get_template_assets();

	// The widget section of the customizer does not like having jQuery dequeued.
	// is_admin() is true when the widget section is loaded.
	if ( ! is_admin() ) {
		// Update jQuery from insecure 1.12.4.
		wp_dequeue_script( 'jquery' );
		wp_dequeue_script( 'jquery-migrate' );
	}

	// TODO: change to register script once map has been converted from jquery
	// wp_register_script( //phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NotInFooter
	// 	'jquery',
	// 	$assets['jquery.js'],
	// 	null,
	// 	THEME_VER
	// );

	// Theme fonts.
	// setting the version to null prevents wp from removing multiple family= parameters.
	// wp_enqueue_style( 'xd_fonts', 'https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700&display=swap', array(), null );

	// Enqueue theme style.
	if ( ! empty( $assets['main.css'] ) ) {
		wp_enqueue_style( 'kicks-style', $assets['main.css'], array(), THEME_VER );
		// This class is removed by javascript once the page has loaded.
		wp_add_inline_style( 'kicks-style', '.suppress-animations * { animation: none !important; transition: none !important; }' );

		if ( function_exists( 'xd_theme_version_compare' ) && xd_theme_version_compare( '>=', '1.1.01', false ) ) {
			foreach ( $assets as $name => $asset ) {
				if ( str_starts_with( $name, 'block-styles' ) ) {
					if ( str_ends_with( $name, '.css' ) ) {
						$name = str_replace( '.css', '', $name );
						// Register block styles.
						// the styles are enqueued using the block.json file from 2.6.01 (1.1.01 of the child theme).
						// this allows for overriding block styles, and allows WordPress to load only the necessary styles.
						wp_register_style( $name, $asset, array( 'kicks-style' ), THEME_VER );
					}
				}
			}
			if ( apply_filters( 'xd_menu_flyout_enabled', false ) ) {
				// the menu needs the accordion block styles.
				wp_enqueue_style( 'block-styles/accordion' );
			}
		} elseif ( ! empty( $assets['blocks.css'] ) ) {
			// Enqueue block styles.
			// blocks.css is a single file that contains all block styles.
			// this was added 2.6.01 to for backwards compatibility.
			wp_enqueue_style( 'kicks-blocks', $assets['blocks.css'], array( 'kicks-style' ), THEME_VER );
		}
	}

	wp_enqueue_script( 'xd_main_js', $assets['main.js'], array(), THEME_VER, true );

	if ( defined( 'RECAPTCHA_PUBLIC_KEY' ) && RECAPTCHA_PUBLIC_KEY ) {
		wp_add_inline_script( 'xd_main_js', 'var recaptcha_site_key = \'' . RECAPTCHA_PUBLIC_KEY . '\';' );
	}

	if ( apply_filters( 'xd_map_pin_post_type_enabled', false ) &&
		apply_filters( 'xd_map_pin_taxonomies_enabled', false ) ) {
		wp_localize_script( 'xd_main_js', 'LeapMapPins', get_js_object() );
	}

	foreach ( $assets as $name => $asset ) {
		if ( str_starts_with( $name, 'blocks' ) ) {

			if ( str_ends_with( $name, '.js' ) ) {
				$name = str_replace( '.js', '', $name );
				wp_register_script( $name, $asset, array( 'xd_main_js' ), THEME_VER, true );
			}
		}
	}

	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );

	// In case you ever need comments, uncomment below.
	// if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
	// wp_enqueue_script( 'comment-reply' );
	// }
}
add_action( 'wp_enqueue_scripts', 'xd_enqueue_scripts' );


/****************************************
 * ENQUEUE EDITOR ASSETS.               *
 ***************************************/
function xdf_enqueue_editor_scripts() {
	$assets = xd_get_template_assets();

	$version = str_contains( '.min', $assets['editor.css'] ) ? THEME_VER : time();

	$style_deps = array( 'wp-edit-blocks' );

	if ( function_exists( 'xd_theme_version_compare' ) && xd_theme_version_compare( '>=', '1.1.01', false ) ) {
		foreach ( $assets as $name => $asset ) {
			if ( str_starts_with( $name, 'block-styles' ) ) {
				if ( str_ends_with( $name, '.css' ) ) {
					$name = str_replace( '.css', '', $name );
					// Register block styles.
					// the styles are enqueued using the block.json file. from 2.6.01 (1.1.01 of the child theme).
					// this allows for overriding block styles, and allows WordPress to load only the necessary styles.
					wp_register_style( $name, $asset, array( 'wp-edit-blocks' ), $version );
					$style_deps[] = $name;
				}
			}
		}
	} elseif ( ! empty( $assets['blocks.css'] ) ) {
		// Enqueue block styles.
		// blocks.css is a single file that contains all block styles.
		// this was added 2.6.01 to for backwards compatibility.
		wp_enqueue_style( 'kicks-blocks', $assets['blocks.css'], array( 'wp-edit-blocks' ), $version );
		$style_deps[] = 'kicks-blocks';
	}

	// Editor styles contains blocks sty;es which are dependent on the front-end blocks
	// styles so that front end block styles can be overridden.
	wp_enqueue_style( 'xdf-editor-styles', $assets['editor.css'], $style_deps, $version );

	$deps = array( 'wp-i18n', 'wp-element', 'wp-blocks', 'wp-components', 'wp-block-editor', 'wp-plugins', 'wp-edit-post', 'wp-data', 'wp-core-data', 'wp-blob' );

	if ( 'widgets' === get_current_screen()->id || 'customize' === get_current_screen()->id ) {
		$deps = array( 'wp-i18n', 'wp-element', 'wp-blocks', 'wp-components', 'wp-block-editor', 'wp-plugins', 'wp-data', 'wp-core-data', 'wp-blob' );

	}

		wp_enqueue_script(  //phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NotInFooter
			'xdf-blocks-js',
			$assets['editor.js'],
			$deps,
			defined( ( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? time() : THEME_VER
		);

	// Editor fonts.
	// setting the version to null prevents wp from removing multiple family= parameters.
	// wp_enqueue_style( 'xd_fonts', 'https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700&display=swap', array(), null );
}
add_action( 'enqueue_block_editor_assets', 'xdf_enqueue_editor_scripts' );

/**
 * Retrieves the filenames from manifest.json
 */
function xd_get_template_assets() {

	$assets = array(
		'editor.css'      => TEMPLATE_DIR . '/build/css/editor.css',
		'editor.js'       => TEMPLATE_URI . '/build/js/editor.js',
		'main.css'        => TEMPLATE_URI . '/build/css/main.css',
		'main.js'         => TEMPLATE_URI . '/build/js/main.js',
		'jquery.js'       => TEMPLATE_URI . '/build/js/jquery.js',
		'icons/icons.svg' => TEMPLATE_DIR . '/build/icons/icons.svg',
	);

	if ( ! empty( $_SERVER['HTTP_X_PROXIEDBY_WEBPACK'] ) ) {

		unset( $assets['main.css'] );
		$assets['main.js'] = '/main.js';
    //phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
	} elseif ( @file_exists( get_template_directory() . '/build/manifest.json' ) ) {
		// webpack will produce a manifest.json which overrides the default assets.
		$assets                    = wp_json_file_decode(
			get_template_directory() . '/build/manifest.json',
			array( 'associative' => true )
		);
		$assets['icons/icons.svg'] = TEMPLATE_DIR . '/build/icons/icons.svg';
	}
	return $assets;
}

/**
 * Remove plugin scripts.
 */
function xd_dequeue_plugin_scripts() {
	// remove instagram plugin js.
	wp_deregister_script( 'sb_instagram_scripts' );
	wp_deregister_script( 'wp-embed' );

}
add_action( 'wp_enqueue_scripts', 'xd_dequeue_plugin_scripts', 9999 );


/**
 * Remove plugin styles.
 */
function xd_dequeue_plugin_styles() {
	// remove instagram plugin css.
	wp_deregister_style( 'sb_instagram_styles' );
	wp_deregister_style( 'sb-font-awesome' );

	// remove core blocks css.
	wp_deregister_style( 'wp-block-library' );
}
add_action( 'wp_enqueue_scripts', 'xd_dequeue_plugin_styles', 99 );

/**
 * Enqueue block view scripts.
 *
 * @param array $blocks the blocks to enqueue scripts for.
 */
function xd_enqueue_block_view_scripts( $blocks = array() ) {
	$block_registry = \WP_Block_Type_Registry::get_instance();
	foreach ( $blocks as $block ) {
		if ( empty( $block['blockName'] ) ) {
			continue;
		}
		$block_type = $block_registry->get_registered( $block['blockName'] );

		if ( empty( $block_type->name ) ) {
			continue;
		}

		if ( 'gravityforms/form' === $block_type->name ) {
			continue;
		}
		if ( ! empty( $block_type->view_script_handles ) ) {
			foreach ( $block_type->view_script_handles as $view_script_handle ) {
				wp_enqueue_script( $view_script_handle );
			}
		}
		xd_enqueue_block_view_scripts( $block['innerBlocks'] );
	}
};

/**
 * Add resource hints for Google Fonts.
 *
 * @param array  $urls the urls to hint.
 * @param string $relation_type the relation type.
 */
function xd_resource_hint_preload( $urls = array(), $relation_type = 'preload' ) {
	// $post = get_post();
	// if ( ! empty( $post->post_content ) ) {
	// $blocks = parse_blocks( $post->post_content );
	// enqueue block view scripts for preloading.
	// xd_enqueue_block_view_scripts( $blocks );
	// }
	if ( 'preconnect' === $relation_type || 'preload' === $relation_type || 'dns-prefetch' === $relation_type ) {
		$scripts        = wp_scripts();
		$queued_scripts = array_intersect_key( $scripts->registered, array_flip( $scripts->queue ) );
		foreach ( $queued_scripts as $script ) {
			$host = wp_parse_url( $script->src, PHP_URL_HOST );
			//phpcs:ignore
			if ( empty( $script->src ) || empty( $host ) || $_SERVER['HTTP_HOST'] === $host
			) {
				continue;
			}
			$src = $script->src;
			if ( ! empty( $script->ver ) ) {
				$src .= str_contains( $script->src, '?' ) ? '&ver=' . $script->ver : '?ver=' . $script->ver;
			}
			$preload = array(
				'href' => $src,
				'as'   => 'script',
			);
			$urls[]  = $preload;
		}
		$styles        = wp_styles();
		$queued_styles = array_intersect_key( $styles->registered, array_flip( $styles->queue ) );
		foreach ( $queued_styles as $style ) {
			$host = wp_parse_url( $style->src, PHP_URL_HOST );
			//phpcs:ignore
			if ( empty( $style->src ) 
			// || empty( $host )
			// || $_SERVER['HTTP_HOST'] === $host
			) {
				continue;
			}
			$src = $style->src;
			if ( ! empty( $style->ver ) ) {
				$src .= str_contains( $style->src, '?' ) ? '&ver=' . $style->ver : '?ver=' . $style->ver;
			}
			$preload = array(
				'href' => $src,
				'as'   => 'style',
			);
			if ( str_contains( $style->handle, 'xd_fonts' ) ) {
				$preload['crossorigin'] = '';
				if ( 'dns-prefetch' === $relation_type && str_contains( $style->src, 'fonts.googleapis.com' ) ) {
					$urls[] = array(
						'href'        => 'https://fonts.gstatic.com',
						'crossorigin' => '',
						'as'          => 'font',
					);
				}
				if ( 'preconnect' === $relation_type && str_contains( $style->src, 'use.typekit.net' ) ) {
					$urls[] = array(
						'href' => 'https://p.typekit.net',
					);
				}
			}
			$urls[] = $preload;
		}
	}
	return $urls;
}

add_filter( 'wp_resource_hints', 'xd_resource_hint_preload', 10, 2 );
add_filter( 'wp_preload_resources', 'xd_resource_hint_preload' );


/**
 * Add inline scripts to head.
 */
function xd_print_inline_head_scripts() {
	the_field( 'option_scripts_header', 'option' );
}
add_action( 'wp_head', 'xd_print_inline_head_scripts', 0 );

/**
 * Add inline scripts to body.
 */
function xd_print_inline_body_scripts() {
	$assets = xd_get_template_assets();
	the_field( 'option_scripts_body', 'option' );
  //phpcs:ignore 
	if ( @file_exists( $assets['icons/icons.svg'] ) ) {
    //phpcs:ignore 
		echo '<div style="display:none">' . file_get_contents( $assets['icons/icons.svg'] ) . '</div>';
	}
}
add_action( 'xd_body', 'xd_print_inline_body_scripts', 0 );

/**
 * Add inline scripts to footer.
 */
function xd_print_inline_footer_scripts() {
	the_field( 'option_scripts_footer', 'option' );
}
add_action( 'wp_footer', 'xd_print_inline_footer_scripts', 99 );


/**
 * Modifies the style tags.
 *
 * @param string $tag the pre-rendered <link /> tag.
 * @param string $handle the style identifier.
 */
function xd_filter_style_tags( $tag, $handle ) {
	if ( in_array(
		$handle,
		array( 'xd_fonts' ),
		true
	) ) {
		$tag = str_replace( '/>', 'crossorigin />', $tag );
	}
	return $tag;
}
add_filter( 'style_loader_tag', 'xd_filter_style_tags', 10, 2 );

/**
 * Modifies the script tags.
 *
 * @param string $tag the pre-rendered <script></script> tag.
 * @param string $handle the script identifier.
 */
function xd_filter_script_tags( $tag, $handle ) {
	// defer main and critical js.
	if ( in_array(
		$handle,
		array(
			'xd_main_js',
		),
		true
	) || str_starts_with( $handle, 'blocks' )
		|| str_contains( $handle, 'defer' )
	) {
		$tag = str_replace( '></script>', ' defer ></script>', $tag );
	}
	return $tag;
}
add_filter( 'script_loader_tag', 'xd_filter_script_tags', 10, 3 );


