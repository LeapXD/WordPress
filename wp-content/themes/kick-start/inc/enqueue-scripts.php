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
function xdc_enqueue_scripts() {

	$assets = xdc_get_assets();

	// Theme fonts.
	// setting the version to null prevents wp from removing multiple family= parameters.
	// phpcs:ignore.
	wp_enqueue_style( 'xd_fonts', 'https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700&display=swap', array(), null );

	if ( ! empty( $_SERVER['HTTP_X_PROXIEDBY_WEBPACK_CHILD'] ) ) {
		// remove the parent theme style in development mode.
		// the parent theme styles are loaded via webpack in the child theme's main.js in dev mode.
		// this allows for hot module replacement, while still having the ability to override the parent theme styles.
		wp_deregister_style( 'kicks-style' );
	}

	// Enqueue theme style.
	if ( ! empty( $assets['main.css'] ) ) {
		wp_enqueue_style( 'xdc-styles', $assets['main.css'], array( 'kicks-style' ), THEME_VER );
		foreach ( $assets as $name => $asset ) {
			if ( str_ends_with( $name, '.css' ) ) {
				$name = str_replace( '.css', '', $name );
				if ( str_starts_with( $name, 'block-styles' ) ) {
					// override the parent theme block styles.
					wp_deregister_style( $name );
					// with the child theme block styles.
					wp_register_style( $name, $asset, array( 'xdc-styles' ), THEME_VER );
				} elseif ( str_starts_with( $name, 'block-overrides' ) ) {
					// Block override style are those that generally override the parent theme block styles with minor tweaks.
					// They have the block style as a dependency to ensure they are loaded after the block styles.

					// The block being overridden.
					$override = str_replace( 'overrides', 'styles', $name );
					// Check that the block being overridden is enqueued.
					$enqueued = wp_styles()->query( $override, 'enqueued' );
					if ( $enqueued ) {
						// The override.
						wp_enqueue_style( $name, $asset, array( $override ), THEME_VER );
					}
				}
			}
		}
	}

	foreach ( $assets as $name => $asset ) {
		if ( str_starts_with( $name, 'blocks' ) || str_starts_with( $name, 'imports' ) ) {
			if ( str_ends_with( $name, '.js' ) ) {
				$name = str_replace( '.js', '', $name );
				wp_deregister_script( $name );
				wp_register_script( $name, $asset, array( 'xd_main_js' ), THEME_VER, true );
			}
		}
	}

	$dependencies = array( 'xd_main_js' );

	wp_enqueue_script( 'xdc-js', $assets['main.js'], $dependencies, THEME_VER, true );

}
add_action( 'wp_enqueue_scripts', 'xdc_enqueue_scripts', 20 );


/****************************************
 * ENQUEUE EDITOR ASSETS.               *
 ***************************************/
function xdc_enqueue_editor_scripts() {
	$assets = xdc_get_assets();

	$version = str_contains( '.min', $assets['editor.css'] ) ? THEME_VER : time();

	$style_deps = array( 'wp-edit-blocks', 'xdf-editor-styles' );

	foreach ( $assets as $name => $asset ) {
		if ( str_ends_with( $name, '.css' ) ) {
			$name = str_replace( '.css', '', $name );
			if ( str_starts_with( $name, 'block-styles' ) ) {
				// override the parent theme block styles.
				wp_deregister_style( $name );
				// with the child theme block styles.
				wp_register_style( $name, $asset, array( 'wp-edit-blocks' ), $version );
				$style_deps[] = $name;
			} elseif ( str_starts_with( $name, 'block-overrides' ) ) {
				// The block being overridden.
				$override = str_replace( 'overrides', 'styles', $name );
				// The override.
				wp_enqueue_style( $name, $asset, array( $override ), THEME_VER );
			}
		}
	}

	wp_enqueue_style( 'xdc-editor-styles', $assets['editor.css'], $style_deps, $version );

	$deps = array( 'wp-i18n', 'wp-element', 'wp-blocks', 'wp-components', 'wp-block-editor', 'wp-plugins', 'wp-edit-post', 'wp-data', 'wp-core-data', 'wp-blob' );

	if ( empty( $_SERVER['HTTP_X_PROXIEDBY_WEBPACK_CHILD'] ) ) {
		$deps[] = 'xdf-blocks-js';
	} else {
		wp_dequeue_style( 'xdf-blocks-js' );
	}

	if ( 'widgets' === get_current_screen()->id || 'customize' === get_current_screen()->id ) {
		$deps = array( 'wp-i18n', 'wp-element', 'wp-blocks', 'wp-components', 'wp-block-editor', 'wp-plugins', 'wp-data', 'wp-core-data', 'wp-blob', 'xdf-blocks-js' );

	}

		wp_enqueue_script(  //phpcs:ignore WordPress.WP.EnqueuedResourceParameters.NotInFooter
			'xdc-blocks-js',
			$assets['editor.js'],
			$deps,
			defined( ( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? time() : THEME_VER
		);

	// Editor fonts.
	// setting the version to null prevents wp from removing multiple family= parameters.
	// phpcs:ignore.
	wp_enqueue_style( 'xd_fonts', 'https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700&display=swap', array(), null );
}
add_action( 'enqueue_block_editor_assets', 'xdc_enqueue_editor_scripts' );

/**
 * Retrieves the filenames from manifest.json
 */
function xdc_get_assets() {

	$assets = array(
		'editor.css'      => STYLESHEET_URI . '/build/css/editor.css',
		'editor.js'       => STYLESHEET_URI . '/build/js/editor.js',
		'main.css'        => STYLESHEET_URI . '/build/css/main.css',
		'main.js'         => STYLESHEET_URI . '/build/js/main.js',
		'uikit.js'        => STYLESHEET_URI . '/build/js/main.js',
		'icons/icons.svg' => get_stylesheet_directory() . '/build/icons/icons.svg',
	);

	if ( ! empty( $_SERVER['HTTP_X_PROXIEDBY_WEBPACK_CHILD'] ) ) {

		unset( $assets['main.css'] );
		$assets['main.js'] = '/main.js';
		//phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
	} elseif ( @file_exists( get_stylesheet_directory() . '/build/manifest.json' ) ) {
		// webpack will produce a manifest.json which overrides the default assets.
		$manifest = file_get_contents( get_stylesheet_directory() . '/build/manifest.json' );

		$decoded = json_decode( $manifest );
		if ( $decoded ) {
			$assets = (array) $decoded;
		}
		$assets['icons/icons.svg'] = get_stylesheet_directory() . '/build/icons/icons.svg';
	}
	return $assets;
}

/**
 * Add inline scripts to body.
 */
function xdc_print_inline_body_scripts() {
	$assets = xdc_get_assets();
  //phpcs:ignore 
	if ( @file_exists( $assets['icons/icons.svg'] ) ) {
    //phpcs:ignore 
		echo '<div style="display:none">' . file_get_contents( $assets['icons/icons.svg'] ) . '</div>';
	}
}
add_action( 'xd_body', 'xdc_print_inline_body_scripts', 0 );



/**
 * Remove preloads in development mode.
 *
 * @param array $urls the urls to hint.
 */
function xdc_resource_hint_preload( $urls = array() ) {
	if ( ! empty( $_SERVER['HTTP_X_PROXIEDBY_WEBPACK_CHILD'] ) ) {
		$urls = array();
	}
	return $urls;
}


add_filter( 'wp_resource_hints', 'xdc_resource_hint_preload', 20 );
add_filter( 'wp_preload_resources', 'xdc_resource_hint_preload', 20 );
