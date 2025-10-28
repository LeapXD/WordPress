<?php
/**
 * Functions which enhance the theme by hooking into WordPress, and helper functions
 *
 * @package Kicks
 */

if ( ! is_admin() ) {
	// These noop functions prevent template errors if ACF is not activated.
	if ( ! function_exists( 'get_field' ) ) {
		/**
		 * Noop get_field
		 */
		function get_field() {
			return false;
		}
	}
	if ( ! function_exists( 'get_fields' ) ) {
		/**
		 * Noop get_fields
		 */
		function get_fields() {
			return array();
		}
	}
	if ( ! function_exists( 'the_field' ) ) {
		/**
		 * Noop the_field
		 */
		function the_field() {
			return false;
		}
	}
	if ( ! function_exists( 'have_rows' ) ) {
		/**
		 * Noop have_rows
		 */
		function have_rows() {
			return false;
		}
	}
}

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function xd_body_classes( $classes ) {
	$classes[] = 'suppress-animations';
	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	if ( ! is_front_page() ) {
		$queried_object = get_queried_object();
		$classes[]      = 'not-front';
		if ( ! empty( $queried_object->post_name ) ) {
			$classes[] = 'slug-' . $queried_object->post_name;
		}
	}

	if ( get_query_var( 'thank-you' ) ) {
		$classes[] = 'template-thank-you';
		$inverted  = array_flip( $classes );
		unset( $inverted['blog'] );
		$classes = array_flip( $inverted );
	}

	return $classes;
}

add_filter( 'body_class', 'xd_body_classes' );

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function xd_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}

add_action( 'wp_head', 'xd_pingback_header' );

/**
 * Returns a reference to an svg sprite icon.
 *
 * @param string  $icon_name The name of the icon id.
 * @param int     $width the width of the svg icon.
 * @param int     $height the height of the svg icon.
 * @param boolean $load_direct loads the svg over http.
 */
function get_icon( $icon_name, $width = '', $height = '', $load_direct = false ) {
	if ( ! $icon_name ) {
		return '';
	}
	$slug     = strtolower( $icon_name );
	$icon_url = '#' . $slug;
	if ( $load_direct ) {
		$template_sprite_path = get_template_directory() . '/build/icons/icons.svg';
		if ( file_exists( $template_sprite_path ) ) {
			// phpcs:ignore
			$sprite = file_get_contents( $template_sprite_path );
			if ( strpos( $sprite, "id=\"$slug\"" ) !== false ) {
				$icon_url = esc_url( get_template_directory_uri() . '/build/icons/icons.svg#' . $slug );
			}
		}
		$stylesheet_sprite_path = get_stylesheet_directory() . '/build/icons/icons.svg';
		if ( file_exists( $stylesheet_sprite_path ) ) {
			// phpcs:ignore
			$sprite = file_get_contents( $stylesheet_sprite_path );
			if ( strpos( $sprite, "id=\"$slug\"" ) !== false ) {
				$icon_url = esc_url( get_stylesheet_directory_uri() . '/build/icons/icons.svg#' . $slug );
			}
		}
	}
	if ( ! empty( $width ) ) {
		$width = "width=\"$width\"";
	}
	if ( ! empty( $height ) ) {
		$height = "height=\"$height\"";
	}
	return "
	<svg class=\"xd-icon\" $width $height>
						<title>$icon_name icon</title><use xlink:href=\"$icon_url\"></use>
	</svg>";
}

/**
 * Add UIkit class to logo.
 *
 * @param String $html logo html.
 * @return String modified logo html.
 */
function xd_filter_custom_logo_class( $html ) {
	return str_replace( 'custom-logo-link', 'custom-logo-link uk-navbar-item uk-logo', $html );
}
add_filter( 'get_custom_logo', 'xd_filter_custom_logo_class', 99 );

/**
 * Add menuitem role logo link.
 *
 * @param String $html logo html.
 * @return String modified logo html.
 */
function xd_filter_custom_logo_role( $html ) {
	$needle = 'class="custom-logo-link';
	$pos    = strpos( $html, $needle );
	if ( ! $pos ) {
		return $html;
	}
	return substr_replace( $html, 'role="menuitem" class="custom-logo-link', $pos, strlen( $needle ) );
}
add_filter( 'get_custom_logo', 'xd_filter_custom_logo_role', 99 );

/**
 * Get the id of the alternate logo.
 */
function get_alt_logo_id() {
	return get_theme_mod( 'xd_alt_logo' );
}

/**
 * Check for alternate logo.
 */
function has_alt_logo() {
	return (bool) get_theme_mod( 'xd_alt_logo' );
}

/**
 * Get the alternate logo.
 */
function get_alt_logo() {
	add_filter( 'theme_mod_custom_logo', 'get_alt_logo_id' );
	$logo = str_replace( 'custom-logo-link', 'custom-logo-alt', get_custom_logo() );
	remove_filter( 'theme_mod_custom_logo', 'get_alt_logo_id' );
	return $logo;
}

/**
 * Post footer
 * Lists the tags and categories
 */
function xd_entry_footer() {
	// Hide category and tag text for pages.
	if ( 'post' === get_post_type() ) {
		/* translators: used between list items, there is a space after the comma */
		$categories_list = get_the_category_list( esc_html__( ', ', 'xd' ) );
		if ( $categories_list ) {
			/* translators: 1: list of categories. */
			printf( '<span class="cat-links">' . esc_html__( ' in %1$s', 'xd' ) . '</span> ', $categories_list );// phpcs:ignore
		}

		/* translators: used between list items, there is a space after the comma */
		$tags_list = get_the_tag_list( '', esc_html_x( ', ', 'list item separator', 'xd' ) );
		if ( $tags_list ) {
			/* translators: 1: list of tags. */
			if ( $categories_list ) {
				print( '<br />' );
			}
			// phpcs:ignore
			printf( '<span class="tags-links">' . esc_html__( '%1$s', 'xd' ) . '</span>', $tags_list );
		}
	}

}

/**
 * Provides previous and next page links with wrapper.
 */
function xd_link_pages() {
	wp_link_pages(
		array(
			'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'xd' ),
			'after'  => '</div>',
		)
	);
}


/**
 * Splits an array into arrays of equal size.
 *
 * @param array $list list items.
 * @param int   $p column count.
 */
function array_partition( $list, $p ) {
	if ( empty( $list ) ) {
		return $list;
	}
	if ( ! ( $p > 0 ) ) {
		return array();
	}

	$listlen   = count( $list );
	$partlen   = floor( $listlen / $p );
	$partrem   = $listlen % $p;
	$partition = array();
	$mark      = 0;
	for ( $px = 0; $px < $p; $px++ ) {
			$incr             = ( $px < $partrem ) ? $partlen + 1 : $partlen;
			$partition[ $px ] = array_slice( $list, $mark, $incr );
			$mark            += $incr;
	}
	return $partition;
}

/**
 * Recursively converts an object to array.
 *
 * @param stdClass $obj the object to convert.
 */
function convert_object_to_array( $obj ) {
	if ( $obj instanceof stdClass || is_array( $obj ) ) {
		$arr = (array) $obj;
		foreach ( $arr as &$object ) {
			$object = convert_object_to_array( $object );
		}
		return $arr;
	}
	return $obj;
}

/**
 * Gets available terms and filters them by post type
 *
 * @param string $taxonomy the taxonomy.
 * @param string $post_type the post type.
 */
function get_terms_by_post_type( $taxonomy, $post_type ) {
	$terms = get_terms(
		$taxonomy,
		array(
			'hide_empty' => true,
		)
	);

	$terms = array_filter(
		$terms,
		function ( $term ) use ( $post_type, $taxonomy ) {
			$posts = get_posts(
				array(
					'fields'         => 'ids',
					'posts_per_page' => 1,
					'post_type'      => $post_type,
					// phpcs:ignore
					'tax_query'      => array(
						array(
							'taxonomy' => $taxonomy,
							'terms'    => $term,
						),
					),
				)
			);

			return ( count( $posts ) > 0 );
		}
	);

	return $terms;
}


/**
 * Creates a setting on the reading page to link a content type archive to a page.
 *
 * @param string $post_type post type.
 * @param string $option_name option name.
 * @return array Action and filter callbacks that can be removed with remove_filter if needed.
 */
function xd_register_archive_page( $post_type, $option_name = null ) {

	if ( empty( $option_name ) ) {
		$option_name = "page_for_{$post_type}s";
	}

	$option_updated = function( $old_value, $new_value ) use ( $post_type, $option_name ) {
		if ( $old_value !== $new_value ) {
			flush_rewrite_rules();
			update_option( 'xd_' . $post_type . '_page', $option_name );
		}
	};

	$add_archive_page_setting = function () use ( $option_name, $post_type ) {

		$post_type_object = get_post_type_object( $post_type );
		if ( empty( $post_type_object ) ) {
			return;
		}
		$label = $post_type_object->label;

		$render_settings_field = function() use ( $label, $option_name ) {

			printf(
				// PHPCS:ignore,
				__(  $label . ' page: %s' ),
				// PHPCS:ignore,
				wp_dropdown_pages(
					array(
						'name'              => $option_name,
						'echo'              => 0,
						'show_option_none'  => __( '&mdash; Select &mdash;' ),
						'option_none_value' => '0',
						'selected'          => get_option( $option_name ),
					)
				)
			);
		};

		add_settings_section( $option_name, '', function(){}, 'reading' );
		register_setting( 'reading', $option_name );
		add_settings_field(
			$option_name,
			'Page For ' . $label,
			$render_settings_field,
			'reading',
			$option_name,
			array(
				'label_for' => $option_name,
			)
		);
	};

	$filter_display_post_states = function( $post_states, $post ) use ( $option_name, $post_type ) {
		if ( (int) get_option( $option_name ) === $post->ID ) {
			$post_type_object            = get_post_type_object( $post_type );
			$post_states[ $option_name ] = $post_type_object->label . ' page';
		}
		return $post_states;
	};

	$add_admin_notice = function() use ( $option_name, $post_type ) {
		$post = get_post();
		if ( empty( $post ) ) {
			return;
		}
		$current_screen = get_current_screen();
		if ( ! $current_screen->is_block_editor() ) {
			return;
		}
		if ( (int) get_option( $option_name ) === $post->ID ) {
			$post_type_object = get_post_type_object( $post_type );
			wp_add_inline_script(
				'wp-notices',
				sprintf(
					'wp.data.dispatch( "core/notices" ).createWarningNotice( "%s", { isDismissible: false } )',
					// translators: Custom post type archive page label.
					sprintf( __( 'You are currently editing the page that shows your %s.' ), $post_type_object->labels->name )
				),
				'after'
			);
		}
	};

	$filter_post_state = function( $post_state ) use ( $option_name ) {
		$post = get_post();
		if ( ! empty( $post ) ) {
			if ( (int) get_option( $option_name ) === $post->ID ) {
				$post_state = $option_name;
			}
		}
		return $post_state;
	};

	$post_type_rewrite = function ( $args, $type ) use ( $option_name, $post_type ) {

		$page = get_option( $option_name );

		if ( $page ) {
			$post = get_post( $page );
		}

		if ( empty( $post ) ) {
			return $args;
		}

		if ( $type !== $post_type ) {
			return $args;
		}

		if ( ( ! empty( $args['public'] ) && ! isset( $args['publicly_queryable'] ) ) || $args['publicly_queryable'] ) {
			if ( is_array( $args['rewrite'] ) ) {
				$args['rewrite']['slug'] = $post->post_name;
			} else {
				$args['rewrite'] = array( 'slug' => $post->post_name );
			}
		}
		return $args;
	};

	$taxonomy_rewrite = function ( $args, $taxonomy_name, $post_types ) use ( $post_type ) {

		if ( ! in_array( $post_type, $post_types, true ) ) {
			return $args;
		}

		$post_type_object = get_post_type_object( $post_type );

		if ( empty( $post_type_object ) ) {
			trigger_error(
				'<strong>Could not set taxonomy rewrite</strong>. Post type  \'' . esc_html( $post_type ) . '\' should be registered before taxonomy \'' . esc_html( $taxonomy_name ),
				E_USER_WARNING
			);
			return $args;
		}

		if ( empty( $post_type_object->rewrite ) ) {
			return $args;
		}

		$slug = $post_type_object->rewrite['slug'];

		if ( ( ! empty( $args['public'] ) && ! isset( $args['publicly_queryable'] ) ) || $args['publicly_queryable'] ) {
			if ( empty( $args['rewrite'] ) || ( is_bool( $args['rewrite'] ) && $args['rewrite'] ) ) {
				$args['rewrite'] = array( 'slug' => $slug . '/' . sanitize_title( $taxonomy_name ) );
			} elseif ( is_array( $args['rewrite'] ) ) {
				$args['rewrite']['slug'] = $slug . '/' . ( empty( $args['rewrite']['slug'] ) ? sanitize_title( $taxonomy_name ) : $args['rewrite']['slug'] );
			} elseif ( is_string( $args['rewrite'] ) ) {
				$args['rewrite'] = array( 'slug' => $slug . '/' . $args['rewrite'] );
			}
		}

		return $args;

	};

	$save_page = function ( $post_id ) use ( $option_name ) {

		$post = get_post( $post_id );

		if ( empty( $post ) ) {
			return;
		}

		if ( ! in_array( $post->post_type, array( 'page', 'post' ), true ) ) {
			return;
		}

		if ( (int) get_option( $option_name ) === (int) $post_id ) {
			flush_rewrite_rules();
		}
	};

	$add_pagination = function ( $rewrite_rules ) use ( $option_name, $post_type ) {

		if ( ! function_exists( 'xd_theme_version_compare' ) ) {
			return $rewrite_rules;
		}

		$page = get_option( $option_name );
		if ( $page ) {
			$post = get_post( $page );
		}

		if ( empty( $post ) ) {
			return $rewrite_rules;
		}

		if ( function_exists( 'xd_theme_version_compare' ) && xd_theme_version_compare( '>=', '1.0.07', false ) ) {
			// This will use the archive page as the base for the rewrite rules.
			// This means that that paginated pages will have the same query vars as the archive page.

			$new_rules         = array( $post->post_name . '/page/([0-9]+)?/?$' => 'index.php?post_type=' . $post_type . '&paged=$matches[1]' );
			$object_taxonomies = get_object_taxonomies( $post_type, null );

			foreach ( $object_taxonomies as $taxonomy ) {

				if ( empty( $taxonomy->publicly_queryable ) || empty( $taxonomy->query_var ) ) {
					continue;
				}

				$new_rules[ $taxonomy->rewrite['slug'] . '/([^\/]+)/?$' ]                = 'index.php?post_type=' . $post_type . '&' . $taxonomy->query_var . '=$matches[1]';
				$new_rules[ $taxonomy->rewrite['slug'] . '/([^\/]+)/page/([0-9]+)?/?$' ] = 'index.php?post_type=' . $post_type . '&' . $taxonomy->query_var . '=$matches[1]&paged=$matches[2]';
			}

			$rewrite_rules = array_merge( $new_rules, $rewrite_rules );
		} else {
			// Previously, we used the post name as the base for the rewrite rules.
			// This means that paginated pages will resolve to the option page, have different conditionals and not load the correct posts automatically.
			$rewrite_rules = array_merge( array( $post->post_name . '/page/([0-9]+)?/?$' => 'index.php?pagename=' . $post->post_name . '&paged=$matches[1]' ), $rewrite_rules );
		}

		return $rewrite_rules;
	};

	$edit_page_link = function( $wp_admin_bar ) use ( $option_name, $post_type ) {
		if ( is_post_type_archive( $post_type ) ) {
			$post             = get_post( get_option( $option_name ) );
			$post_type_object = get_post_type_object( 'page' );
			if ( ! empty( $post ) && ! empty( $post_type_object ) ) {
				$wp_admin_bar->add_node(
					array(
						'id'    => 'edit',
						'title' => $post_type_object->labels->edit_item,
						'href'  => get_edit_post_link( $post->ID ),
					)
				);
			}
		}
	};

	add_action( 'admin_bar_menu', $edit_page_link, 80 );
	add_action( 'admin_init', $add_archive_page_setting );
	add_filter( 'display_post_states', $filter_display_post_states, 10, 2 );
	add_action( 'admin_enqueue_scripts', $add_admin_notice );
	add_filter( 'xd_post_state', $filter_post_state );
	add_filter( 'rewrite_rules_array', $add_pagination );
	add_filter( 'register_post_type_args', $post_type_rewrite, 10, 2 );
	add_filter( 'register_taxonomy_args', $taxonomy_rewrite, 10, 3 );
	add_action( 'save_post', $save_page );
	add_action( 'update_option_' . $option_name, $option_updated, 20, 2 );

	return array(
		'admin_init_cb'          => $add_archive_page_setting,
		'display_post_states_cb' => $filter_display_post_states,
		'add_admin_notice_cb'    => $add_admin_notice,
		'filter_post_state_cb'   => $filter_post_state,
		'add_pagination_cb'      => $add_pagination,
		'edit_page_link_cb'      => $edit_page_link,
		'post_type_rewrite_cb'   => $post_type_rewrite,
		'save_page_cb'           => $save_page,
		'option_updated_cb'      => $option_updated,
	);
}

	/**
	 * Creates a setting on the reading page for a collection page.
	 *
	 * @param string $post_type post type.
	 * @param string $type the type of page. (flyout or modal).
	 * @param bool   $soft_route_enabled if true, ui will show on current page, otherwise links will open on collection page.
	 * @param string $option_name option name.
	 * @return array Action and filter callbacks that can be removed with remove_filter if needed.
	 */
function xd_register_ui_page( $post_type, $type = null, $soft_route_enabled = false, $option_name = null ) {

	if ( empty( $option_name ) ) {
		$option_name = "page_for_{$post_type}s";
	}

	$callbacks         = xd_register_archive_page( $post_type, $option_name );
	$add_pagination_cb = $callbacks['add_pagination_cb'];
	remove_filter( 'rewrite_rules_array', $add_pagination_cb );

	$ui_types_filter = function( $post_types ) use ( $post_type ) {
		if ( ! is_array( $post_types ) ) {
			$post_types = array();
		}
		if ( ! in_array( $post_type, $post_types, true ) ) {
			$post_types[] = $post_type;
		}
		return $post_types;
	};

		$add_rewrite_rule = function ( $rewrite_rules ) use ( $post_type, $option_name ) {

			$page = get_option( $option_name );

			if ( $page ) {
				$post = get_post( $page );
			}

			if ( empty( $post ) ) {
				return $rewrite_rules;
			}

			unset( $rewrite_rules[ $post->post_name . '/([^/]+)/?$' ] );

			return array_merge(
				array(
					$post->post_name . '/([^/]+)/?$' => 'index.php?pagename=' . $post->post_name . '&' . $post_type . '-slug=$matches[1]',
				),
				$rewrite_rules,
			);

		};

		$add_tags = function () use ( $post_type ) {
			add_rewrite_tag( '%' . $post_type . '-slug%', '([^&]+)' );
		};

		$collection_meta = function () use ( $post_type, $option_name ) {
			$pagename = get_query_var( 'pagename' );
			$slug     = get_query_var( $post_type . '-slug' );

			if ( empty( $slug ) ) {
				return;
			}

			$page = get_option( $option_name );

			if ( $page ) {
				$post = get_post( $page );
			}

			if ( empty( $post ) ) {
				return;
			}

			if ( $pagename !== $post->post_name ) {
				return;
			}

			$posts = get_posts(
				array(
					'name'        => $slug,
					'post_type'   => $post_type,
					'post_status' => 'publish',
					'numberposts' => 1,
				)
			);

			if ( empty( $posts ) ) {
				global $wp_query;
				$wp_query->set_404();
				status_header( 404 );
				nocache_headers();
				return;
			}

			if ( ! function_exists( 'YoastSEO' ) ) {
				return;
			}

			YoastSEO()->meta->for_post( $posts[0]->ID );

		};

		add_filter( 'rewrite_rules_array', $add_rewrite_rule );
		add_action( 'init', $add_tags );
		add_action( 'template_redirect', $collection_meta );
	if ( $type ) {
		add_filter( 'xd_' . $type . '_post_types', $ui_types_filter );
	}
	if ( $type && $soft_route_enabled ) {
		add_filter( 'xd_embedded_' . $type . '_post_types', $ui_types_filter );
	}

		$callbacks = array_merge(
			$callbacks,
			array(
				'add_rewrite_rule_cb' => $add_rewrite_rule,
				'add_tags_cb'         => $add_tags,
				'collection_meta_cb'  => $collection_meta,
				'ui_types_filter_cb'  => $ui_types_filter,
			)
		);

	return $callbacks;
}

	/**
	 * Check if the current page is a collection page.
	 *
	 * @param string $post_type post type.
	 */
function xd_is_collection_page( $post_type ) {

	$option_name = get_option( 'xd_' . $post_type . '_page' );

	if ( empty( $option_name ) ) {
		$option_name = "page_for_{$post_type}s";
	}

	$pagename = get_query_var( 'pagename' );
	$slug     = get_query_var( $post_type . '-slug' );

	if ( empty( $pagename ) ) {
		return false;
	}

	if ( empty( $slug ) ) {
		return false;
	}

	$page = get_option( $option_name );

	if ( $page ) {
		$post = get_post( $page );
	}

	if ( empty( $post ) ) {
		return false;
	}

	return ( (int) $pagename === (int) $post->post_name );
}

	/**
	 * Get flyout post types.
	 */
function xd_flyout_post_types() {
	return apply_filters( 'xd_flyout_post_types', array( 'flyout' ) );
}

	/**
	 * Get embedded flyout post types.
	 */
function xd_embedded_flyout_post_types() {
	return apply_filters( 'xd_embedded_flyout_post_types', array( 'flyout' ) );
}

	/**
	 * Add a static page to WordPress.
	 *
	 * @param array{slug:string,title:string,content:string,disable_wpseo:bool} $args The arguments for the static page.
	 */
function xd_add_static_page( $args ) {

	$args = wp_parse_args(
		$args,
		array(
			'slug'        => '',
			'callback'    => fn() => null,
			'title'       => '',
			'content'     => '',
			'query_vars'  => array(),
			'disable_seo' => false,
		)
	);

	$slug              = $args['slug'];
	$callback          = $args['callback'];
	$title             = $args['title'];
	$content           = $args['content'];
	$public_query_vars = $args['query_vars'];
	$disable_seo       = $args['disable_seo'];

	$static_post = function ( $posts, $query ) use ( $slug, $callback, $title, $content ) {
		global $wp;

		if ( ! $query->is_main_query() ) {
			return $posts;
		}

		if ( ! empty( $posts ) ) {
			return $posts;
		}

		$post_fields = array(
			'title'   => '',
			'content' => '',
		);

		if ( is_callable( $callback ) ) {
			$post_fields = $callback();
			if ( isset( $post_fields['title'] ) ) {
				$title = $post_fields['title'];
			}
			if ( isset( $post_fields['content'] ) ) {
				$content = $post_fields['content'];
			}
		}

		if ( strtolower( $wp->request ) === $slug ) {
			$post = (object) array(
				'ID'             => 0,
				'post_author'    => 1,
				'post_name'      => $slug,
				'post_type'      => 'page',
				'post_status'    => 'static',
				'post_title'     => $title,
				'post_content'   => $content,
				'comment_status' => 'closed',
				'ping_status'    => 'open',
				'comment_count'  => 0,
				'post_date'      => current_time( 'mysql' ),
				'post_date_gmt'  => current_time( 'mysql', 1 ),
			);

			$posts = array( $post );

		}
		return $posts;
	};

		$pre_get_shortlink = function ( $return ) use ( $slug ) {
			if ( is_page( $slug ) ) {
				remove_filter( 'page_link', 'relevanssi_permalink', 10, 2 );
				return home_url( '?p=-1' );
			}

			return $return;
		};

		$disable_wpseo = function () use ( $slug ) {
			if ( is_page( $slug ) ) {
				if ( ! function_exists( 'YoastSEO' ) ) {
					return;
				}
				$front_end = YoastSEO()->classes->get( Yoast\WP\SEO\Integrations\Front_End_Integration::class );

				remove_action( 'wpseo_head', array( $front_end, 'present_head' ), -9999 );
			}
		};

		$query_vars = function ( $query_vars ) use ( $public_query_vars ) {
			if ( ! empty( $public_query_vars ) ) {
				$query_vars = array_merge( $query_vars, $public_query_vars );
			}
			return $query_vars;
		};

		$rewrite_rule = function () use ( $slug ) {
			add_rewrite_rule( $slug . '/?$', 'index.php?pagename=' . $slug, 'top' );
		};

		add_filter( 'the_posts', $static_post, -10, 2 );
		add_action( 'pre_get_shortlink', $pre_get_shortlink );
		add_action( 'init', $rewrite_rule );
		add_filter( 'query_vars', $query_vars );
	if ( $disable_seo ) {
		add_action( 'template_redirect', $disable_wpseo );
	}

		return array(
			$static_post,
			$pre_get_shortlink,
			$rewrite_rule,
			$disable_wpseo,
		);
}

	/**
	 * Alias for xd_add_static_page.
	 *
	 * @param array{slug:string,title:string,content:string,disable_wpseo:bool} $args The arguments for the static page.
	 */
function xd_register_static_page( $args ) {
	return xd_add_static_page( $args );
}

add_action( 'acf/the_field/allow_unsafe_html', '\__return_true' );
add_action( 'acf/admin/prevent_escaped_html_notice', '\__return_true' );

xd_register_ui_page( 'team_member' );
