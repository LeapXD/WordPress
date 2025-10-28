<?php
/**
 * Scrollspy setup.
 *
 * @package Kicks
 */

/**
 * Scrollspy class
 */
class Xd_Scrollspy {

	/**
	 * Stores the singleton instance.
	 *
	 * @var Xd_Scrollspy.
	 */
	private static $instance;

	/**
	 * Stores the scrollspy rules.
	 *
	 * @var array[].
	 */
	private $rules = array();

	/**
	 * Retrieves scrollspy class instance.
	 *
	 * @return Xd_Scrollspy
	 */
	public static function get_instance() {
		if ( empty( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Registers a scrollspy rule
	 *
	 * @param string $element viewport element.
	 * @param array  $rule rule
	 *   ['repeat'] => bool.
	 *   ['delay'] => int.
	 *   ['cls'] => class to add to target element.
	 *   ['target'] => target element.
	 */
	public function register( $element, $rule = array() ) {

		$defaults = array(
			'element' => $element,
			'repeat'  => true,
			'delay'   => 100,
			'cls'     => 'xd-fade-up',
			'target'  => '> *',
		);

		$args = array_merge( $defaults, $rule );

		$this->rules[ $element ] = $args;
	}

	/**
	 * Retrieves scrollspy rules.
	 */
	public function get_rules() {
		return array_values( $this->rules );
	}

	/**
	 * De-registers a scrollspy rule
	 *
	 * @param string $element viewport element.
	 */
	public function deregister( $element ) {
		unset( $this->rules[ $element ] );
	}

	/**
	 * Adds scrollspy directives to theme javascript and inline styles to header.
	 */
	public static function enqueue() {
		$instance = self::get_instance();
		$instance->print_styles();
		wp_localize_script( 'xd_main_js', 'xd_scrollspy', $instance->get_rules() );
	}

	/**
	 * Adds inline styles to page header.
	 * Priming elements for scrollspy avoids them being
	 * displayed while javascript loads and reduces cumulative layout shift.
	 */
	private function print_styles() {
		$instance         = self::get_instance();
		$rules            = array();
		$rules['hidden']  = array_unique(
			array_map(
				function( $rule ) {
					$targets = explode( ',', $rule['target'] );
					return implode(
						",\n",
						array_map(
							function( $target ) use ( $rule ) {
								return $rule['element'] . ' ' . trim( $target );
							},
							$targets
						)
					);
				},
				$instance->rules
			)
		);
		$rules['visible'] = array_unique(
			array_map(
				function( $rule ) {
					$targets = explode( ',', $rule['target'] );
					return implode(
						",\n",
						array_map(
							function( $target ) use ( $rule ) {
								return $rule['element'] . ' ' . trim( $target ) . '.uk-scrollspy-inview';
							},
							$targets
						)
					);
				},
				$instance->rules
			)
		);

		echo sprintf(
			"<style type=\"text/css\">\n%s\n%s\n%s\n%s\n\n</style>\n",
			implode( ",\n", $rules['hidden'] ), //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'{ visibility:visible !important; opacity: 0 }',
			implode( ",\n", $rules['visible'] ), //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'{ opacity: 1 }'
		);
	}
}

add_action( 'wp_head', 'xd_scrollspy::enqueue' );

	/**
	 * Registers a scrollspy rule
	 *
	 * @param string $element viewport element.
	 * @param array  $rule rule
	 *   ['repeat'] => bool.
	 *   ['delay'] => int.
	 *   ['cls'] => class to add to target element.
	 *   ['target'] => target element.
	 */
function xd_register_scrollspy_rule( $element, $rule = array() ) {
	$instance = Xd_Scrollspy::get_instance();
	$instance->register( $element, $rule );
}

/**
 * De-registers a scrollspy rule
 *
 * @param string $element viewport element.
 */
function xd_deregister_scrollspy_rule( $element ) {
	$instance = Xd_Scrollspy::get_instance();
	$instance->deregister( $element );
}

// hero & footer.
xd_register_scrollspy_rule( '.hero-full', array( 'target' => '.hero-full__foreground-body-contents > *, .hero-full__foreground-footer' ) );
xd_register_scrollspy_rule( '.hero__content > div' );
xd_register_scrollspy_rule( '.xd-footer .row' );

// Standard text components.
xd_register_scrollspy_rule( '.xd-block-title' );
xd_register_scrollspy_rule( '.xd-container', array( 'target' => '[class*="__inner"] > *' ) );

// Misc components.
xd_register_scrollspy_rule( '.xd-columns', array( 'target' => '.xd-column' ) );
xd_register_scrollspy_rule( '.xd-post-cards:not(.xd-post-cards--slider)', array( 'target' => '.xd-post-card' ) );
xd_register_scrollspy_rule( '.xd-thumbnail-cards', array( 'target' => '.xd-thumbnail-card' ) );
xd_register_scrollspy_rule( '.xd-cta', array( 'target' => '.xd-cta__inner' ) );
xd_register_scrollspy_rule( '.xd-two-tile', array( 'target' => '.xd-two-tile__inner > *' ) );
xd_register_scrollspy_rule( '.xd-timeline', array( 'target' => '.xd-timeline__inner, .xd-timeline__image' ) );

xd_register_scrollspy_rule( '.uk-switcher', array( 'target' => '.xd-tab-section' ) );
