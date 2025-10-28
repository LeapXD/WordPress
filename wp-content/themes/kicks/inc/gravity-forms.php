<?php
/**
 * Gravity forms customizations.
 *
 * @package Kicks
 * phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
 */

use Symfony\Component\DomCrawler\Crawler;

/**
 * Adds chevron icon from sprite to gravity form select inputs
 *
 * @param string $content    The field content.
 * @param object $field      The Field Object.
 * @param string $value      The field value.
 * @param int    $lead_id The entry ID.
 * @param int    $form_id The form ID.
 */
function xd_gform_field_content( $content, $field, $value, $lead_id, $form_id ) {

	if ( is_admin() ) {
		return $content;
	}

	if ( str_contains( $content, '</select>' ) && ! str_contains( $content, 'multiple' ) ) {
		$content = str_replace( '</select>', '</select>' . get_icon( 'chevron-down' ), $content );
	}

	if ( 'list' === $field->type ) {
		$content = str_replace( '>Add<', '><span class="add list-button"></span><', $content );
		$content = str_replace( '>Remove<', '><span class="remove list-button"></span><', $content );
		return $content;
	}

	if ( 'checkbox' === $field->type || 'radio' === $field->type || 'multi_choice' === $field->type || 'consent' === $field->type ) {

		$type = $field->type;

		if ( 'multi_choice' === $field->type ) {
			$type = 'checkbox';
			if ( 'single' === $field->choiceLimit ) {
				$type = 'radio';
			}
		}

		if ( 'consent' === $field->type ) {
			$type = 'checkbox';
		}

		$crawler    = new Crawler( $content );
		$checkboxes = $crawler->filter( '.gchoice' );
		if ( 'consent' === $field->type ) {
			$checkboxes = $crawler->filter( '.ginput_container_consent' );
		}

		foreach ( $checkboxes as $checkbox ) {
			$label     = null;
			$input     = null;
			$node_text = '';
			foreach ( $checkbox->childNodes as $child ) {
				if ( '#text' === $child->nodeName ) {
					continue;
				}
				if ( 'input' === $child->nodeName ) {
					$input           = $child;
					$input_classes   = explode( ' ', $input->getAttribute( 'class' ) );
					$input_classes[] = "xd-{$type}-input";
					$input->setAttribute( 'class', implode( ' ', $input_classes ) );
				}
				if ( 'label' === $child->nodeName ) {
					$label            = $child;
					$node_text        = $label->nodeValue;
					$label->nodeValue = '';
					$label_classes    = explode( ' ', $label->getAttribute( 'class' ) );
					$label_classes[]  = "xd-{$type}";
					$label->setAttribute( 'class', implode( ' ', $label_classes ) );
				}
				if ( $label && $input && $node_text ) {
					$label->appendChild( $input->cloneNode() );
					$input->parentNode->removeChild( $input );
					$xd_checkmark = $label->ownerDocument->createElement( 'span' );
					$xd_checkmark->setAttribute( 'class', "xd-{$type}__checkmark" );
					$label->appendChild( $xd_checkmark );
					$label->appendChild( $label->ownerDocument->createTextNode( $node_text ) );
					break;
				}
			}
		}
		return $crawler->html();
	}

	return $content;
}

add_filter( 'gform_field_content', 'xd_gform_field_content', 99, 5 );

/**
 * Removes the gform_button class from the submit button.
 *
 * @param string $button The button markup.
 * @param object $form   The form object.
 */
function xd_gform_remove_submit_button_class( $button, $form ) {
	return str_replace( 'gform_button button', 'gform_button', $button );
}


add_filter( 'gform_submit_button', 'xd_gform_remove_submit_button_class', 10, 2 );

/**
 * Apply redirect to parent frame.
 *
 * @param string $confirmation The confirmation message.
 * @param object $form         The form object.
 * @param object $entry        The entry object.
 * @param bool   $ajax         Whether the form was submitted via ajax.
 */
function xd_gform_confirmation( $confirmation, $form, $entry, $ajax ) {
	if ( is_page( 'get-form' ) ) {
		if ( ! empty( $confirmation['redirect'] ) ) {
			$confirmation = "
				<script>
					window.parent.location.href = '{$confirmation['redirect']}';
				</script>";
		}
	}
	return $confirmation;
}

add_filter( 'gform_confirmation', 'xd_gform_confirmation', 10, 4 );

/**
 * Apply page template to get-form page.
 *
 * @param string $template the OG.
 */
function xd_apply_gravityform_template( $template ) {
	if ( is_page( 'get-form' ) ) {
		return 'template-form.twig';
	}
	return $template;
}
add_action( 'template_include', 'xd_apply_gravityform_template', 5 );

/**
 * Remove admin bar on get-form page.
 *
 * @param bool $show_admin_bar Whether to show the admin bar.
 */
function xd_remove_admin_bar_on_get_form( $show_admin_bar ) {
	if ( is_page( 'get-form' ) ) {
		return false;
	}
	return $show_admin_bar;
}
add_filter( 'show_admin_bar', 'xd_remove_admin_bar_on_get_form' );


/**
 * Add custom theme to gravity forms.
 */
function xd_gform_form_theme_slug() {
	return 'kicks';
}

add_filter( 'gform_form_theme_slug', 'xd_gform_form_theme_slug' );

if ( apply_filters( 'xd_gravity_form_page', true ) ) {
	// is_page() doesn't work on init hook.
	if ( '/get-form/' !== wp_parse_url( add_query_arg( array() ), PHP_URL_PATH ) && ! is_admin() && ! filter_input( INPUT_GET, 'context' ) && ! filter_input( INPUT_GET, 'gf-download' ) ) {
		if ( function_exists( 'xd_theme_version_compare' ) && xd_theme_version_compare( '<', '1.1.01', false ) ) {
			remove_action( 'init', array( 'GFForms', 'init' ) );
		}
	}
	add_filter( 'gform_disable_form_theme_css', '__return_true' );

	add_action(
		'enqueue_block_assets',
		function() {
			wp_dequeue_style( 'gform_basic' );
		}
	);
	xd_add_static_page(
		array(
			'slug'        => 'get-form',
			'query_vars'  => array( 'form_id', 'display_form_title', 'display_form_desc' ),
			'title'       => 'Form',
			'disable_seo' => true,
		),
	);

	/**
	 * Enqueue scripts for gravity form iframe.
	 */
	function xd_gravity_form_iframe_scripts() {
		if ( is_page( 'get-form' ) ) {
			wp_add_inline_script(
				'xd_main_js',
				"
				document.body.classList.add('xd-gravityform-iframe__body');
				function sendHeight() {
						const height = document.querySelector('.gform_wrapper, .gform_confirmation_wrapper')?.offsetHeight + 8;
						window.parent.postMessage({
								type: 'iframeHeight',
								height: height
						}, '*');
				}
				const observer = new MutationObserver(sendHeight);
				observer.observe(document.body, { childList: true, subtree: true, attributes: true });
				window.addEventListener('load', sendHeight);
				window.addEventListener('resize', sendHeight);
				const iframe = [...window.parent.document.querySelectorAll('iframe')].find(iframe => iframe.contentWindow === window);
				const colorTheme = [...(iframe.closest('[class*=\"is-style-\"]')?.classList || [])].find(className => className.includes('is-style-'));
				if(iframe.closest('.xd-modal')) {
					document.body.classList.add('xd-modal__iframe');
				}
				if(iframe.closest('.xd-flyout')) {
					document.body.classList.add('xd-flyout__iframe');
				}
				document.body.classList.add(colorTheme);
			"
			);
		} else {
			wp_add_inline_script(
				'xd_main_js',
				"
				function adjustIframeHeight(event) {
						if (event.data.type === 'iframeHeight') {
								const iframe = event.source.frameElement;
								if (iframe.contentWindow === event.source) {
										iframe.setAttribute('height', event.data.height);
								}
						}
				}
				window.addEventListener('message', adjustIframeHeight, false);
			"
			);
		}
	}

	add_action( 'wp_enqueue_scripts', 'xd_gravity_form_iframe_scripts' );

	/**
	 * Add body class to gravity form iframe.
	 *
	 * @param array $classes The body classes.
	 */
	function xd_gravityform_body_class( $classes ) {
		if ( is_page( 'get-form' ) ) {
			$classes = array( 'xd-gravityform-iframe__html' );
		}
		return $classes;
	}

	add_filter( 'body_class', 'xd_gravityform_body_class' );
}
