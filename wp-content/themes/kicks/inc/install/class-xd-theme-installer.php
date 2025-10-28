<?php
/**
 * Theme init.
 *
 * @package Kicks
 */

 // phpcs:disabled
/**
 * Installer class
 */
class Xd_Theme_Installer {


	private $config_file;

	private $definitions;

	private $gravity_forms;

	private $gravity_form_json_file;

	/**
	 * Fire up.
	 */
	public static function init() {
		new self();
	}

	public function __construct() {

		$this->gravity_form_json_file = get_template_directory() . '/inc/install/gravity-forms.json';
		$this->config_file   = WPMU_PLUGIN_DIR . '/xd-config.php';
		$this->definitions   = (array) json_decode( file_get_contents( __DIR__ . '/definitions.json' ) );
		$this->gravity_forms = (array) json_decode( file_get_contents( $this->gravity_form_json_file ) );

		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		if ( wp_verify_nonce( filter_input( INPUT_POST, 'xd-theme-options' ), 'xd-theme-options' ) ) {
			$this->handle_submit();
		}
		$req = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '';

		if ( ! $this->check_config() ) {
			$this->write_config();
			wp_safe_redirect($req);
			exit;
		}

		if ( strpos( $req, 'activate-plugins' ) !== false ) {
			add_action('admin_init',function(){
				$plugins = filter_input( INPUT_GET, 'activate-plugins', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
				$this->activate_plugins( $plugins );
				if(in_array('advanced-custom-fields-pro/acf.php',$plugins)){
					$this->import_acf_fields();
				}
						// delete_option( 'gf_imported_theme_file' );
				delete_option( 'rg_form_version' );
				delete_option( 'rg_gforms_key' );
				delete_option( 'rg_gforms_message' );
				wp_safe_redirect( remove_query_arg( 'activate-plugins' ) );
				exit;
			});
		}
		if ( strpos( $req, 'flush-rewrite-rules' ) !== false ) {
			flush_rewrite_rules();
			wp_safe_redirect( remove_query_arg( 'flush-rewrite-rules' ) );
		}
	}

	public function admin_menu() {
		global $wp_registered_widgets;
		add_menu_page(
			__( 'XD Theme Setup', 'textdomain' ),
			'XD Theme Init',
			'developer',
			'xd-theme-options',
			array( $this, 'menu_html' ),
			'dashicons-admin-settings'
		);
	}

	public function menu_html() {
		include __DIR__ . '/xd-theme-options.php';
	}

	private function handle_submit() {

		$privacy_policy  	= filter_input( INPUT_POST, 'privacy_policy' );
		$company_name    	= filter_input( INPUT_POST, 'company_name' );
		$contact_page    	= filter_input( INPUT_POST, 'contact_page' );
		$front_page      	= filter_input( INPUT_POST, 'front_page' );
		$blog_page       	= filter_input( INPUT_POST, 'blog_page' );
		$thank_you_page  	= filter_input( INPUT_POST, 'thank_you_page' );
		$style_guide_page = filter_input( INPUT_POST, 'style_guide_page' );
		$from            	= filter_input( INPUT_POST, 'notification_from' );
		$to              	= filter_input( INPUT_POST, 'notification_to' );
		if ( $from || $to ) {
			$this->update_gravity_form_notification(
				array(
					'from' => $from,
					'to'   => $to,
				)
			);
		}
		if ( $company_name ) {
			update_option( 'options_option_company_legal_name', $company_name );
			if ( $privacy_policy ) {
				$this->update_privacy_policy( $company_name );
			}
		}
		if ( $contact_page ) {
			$this->create_contact_page();
		}
		if ( $front_page ) {
			$this->create_front_page();
		}
		if ( $blog_page ) {
			$this->create_blog_page();
		}
		if ( $thank_you_page ) {
			$this->create_thank_you_page();
		}
		if ( $style_guide_page ) {
			$this->create_style_guide_page();
		}
		$menus            = filter_input( INPUT_POST, 'install_menu', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$menu_items       = filter_input( INPUT_POST, 'menu_items', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$plugins          = filter_input( INPUT_POST, 'plugins', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		if ( ! empty( $menus ) ) {
			$this->install_menus(
				$menus,
				$menu_items,
			);
		}

		$definitions = array_replace(
			$this->definitions,
			filter_input( INPUT_POST, 'definitions', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY )
		);
		$this->write_config( $definitions );
		$query_args = array( 'flush-rewrite-rules' => true );
		if ( $plugins ) {
			$query_args['activate-plugins'] = $plugins;
		}
		update_option( 'permalink_structure', '/%postname%/' );
		update_option( 'xd_initialized', true );
		wp_safe_redirect( add_query_arg( $query_args ) );
		exit;
	}

	private function get_definition( $key ) {
		return defined( $key ) ? constant( $key ) : '';
	}

	private function install_menus(	$menus,	$menu_items ) {
		$saved_menu_locations   = get_theme_mod( 'nav_menu_locations' );
		foreach ( $menus as $menu_slug => $menu_title ) {
			$menu_obj = wp_get_nav_menu_object( $menu_slug );
			if ( $menu_obj ) {
				$menu_id = $menu_obj->term_id;
			} else {
				$menu_id = wp_create_nav_menu( $menu_title );
				if ( isset( $menu_items[ $menu_slug ] ) ) {
					$this->insert_menu_items( $menu_id, $menu_items[ $menu_slug ] );
				}
			}
			if( 'primary-menu' === $menu_slug ){
				$saved_menu_locations['primary-menu'] = $menu_id;
			}
		}

		set_theme_mod( 'nav_menu_locations', $saved_menu_locations );
	}

	private function insert_menu_items( $menu_id, $menu_items ) {
		$title = '';
		$items = explode( "\n", $menu_items );
		foreach ( $items as $item ) {
			$object = null;
			$parts  = explode( '=', $item );
			if ( isset( $parts[1] ) ) {
				$title = trim( $parts[0] );
				$link  = trim( $parts[1] );
				if ( strpos( $link, '[' ) !== false ) {
					$link   = str_replace( array( '[', ']' ), '', $link );
					$object = get_post( get_page_by_path( $link ) );
				}
			}
			if ( ! empty( $object ) ) {
				wp_update_nav_menu_item(
					$menu_id,
					0,
					array(
						'menu-item-title'     => esc_html( $title ),
						'menu-item-object-id' => $object->ID,
						'menu-item-object'    => 'page',
						'menu-item-status'    => 'publish',
						'menu-item-type'      => 'post_type',
						'menu-item-url'       => get_the_permalink( $object->ID ),
						'menu-item-type'      => 'post_type',
					)
				);
			} else {
				wp_update_nav_menu_item(
					$menu_id,
					0,
					array(
						'menu-item-title'  => esc_html( $title ),
						'menu-item-url'    => $link,
						'menu-item-status' => 'publish',
					)
				);
			}
		}
	}


	private function the_definition( $key ) {
		echo $this->get_definition( $key );
	}

	private function the_gravity_form_notification( $field ) {
		$notification = array(
			'to'   => '',
			'from' => '',
		);
		if ( isset( $this->gravity_forms[0]->notifications[0] ) ) {
			$notification['to']   = $this->gravity_forms[0]->notifications[0]->to;
			$notification['from'] = $this->gravity_forms[0]->notifications[0]->from;
		}
		echo esc_html( $notification[ $field ] );
	}

	private function update_gravity_form_notification( $fields ) {
		if ( is_array( $fields ) ) {
			foreach ( $fields as $key => $val ) {
				if ( isset( $this->gravity_forms[0]->notifications[0]->$key ) ) {
					$this->gravity_forms[0]->notifications[0]->$key = $val;
				}
			}
		}
		$this->write_gravity_form_config();
	}

	private function write_gravity_form_config() {
		file_put_contents( $this->gravity_form_json_file,wp_json_encode( $this->gravity_forms, JSON_PRETTY_PRINT ) );
	}

	private function update_privacy_policy( $company_name ) {
		$privacy_policy_page = get_post( get_option( 'wp_page_for_privacy_policy' ) );
		$content             = str_replace( '<companyname />', $company_name, file_get_contents( __DIR__ . '/privacy-policy-content.html' ) );
		if ( $privacy_policy_page ) {
			wp_update_post(
				array(
					'ID'           => $privacy_policy_page->ID,
					'post_content' => $content,
					'post_status'  => 'publish',
				)
			);
		}
	}

	private function create_contact_page() {

		$post_id = wp_insert_post(
			array(
				'post_status' => 'publish',
				'post_title'  => 'Contact',
				'post_type'   => 'page',
			)
		);

		if ( $post_id ) {
			update_option( 'xd_contact_page', $post_id );
		}
	}

	private function create_front_page() {

		$post_id = wp_insert_post(
			array(
				'post_status' => 'publish',
				'post_title'  => 'Front Page',
				'post_type'   => 'page',
			)
		);
		if ( $post_id ) {
			update_option( 'page_on_front', $post_id );
			update_option( 'show_on_front', 'page' );
		}
	}

	private function create_blog_page() {

		$post_id = wp_insert_post(
			array(
				'post_status' => 'publish',
				'post_title'  => 'Blog',
				'post_type'   => 'page',
			)
		);
		if ( $post_id ) {
			update_option( 'page_for_posts', $post_id );
		}
	}

	private function create_thank_you_page() {

		$post_id = wp_insert_post(
			array(
				'post_status' => 'publish',
				'post_title'  => 'Thank You',
				'post_type'   => 'page'
			)
		);

		if ( $post_id ) {
			update_option( 'xd_thank_you_page', $post_id );
		}

	}

	private function create_style_guide_page() {

		wp_insert_post(
			array(
				'post_status'  => 'publish',
				'post_title'   => 'Style guide',
				'post_type'    => 'page',
				'post_content' => file_get_contents( __DIR__ . '/styleguide-content.html' ),
			)
		);

	}

	private function get_menu_locations() {
		return array_filter(
			get_theme_mod( 'nav_menu_locations' ),
			function( $menu ) {
				return ! empty(
					wp_get_nav_menu_object( $menu )
				);
			}
		);
	}

	private function get_is_contact_page_create_checked() {
		if ( $this->is_initialized() ) {
			return false;
		}
		return ! get_page_by_path( 'contact' );
	}

	private function get_is_front_page_create_checked() {
		if ( $this->is_initialized() ) {
			return false;
		}
		$front_page = get_post( get_option( 'page_on_front' ) );
		return ! $front_page;
	}

	private function get_is_privacy_policy_checked() {
		if ( $this->is_initialized() ) {
			return false;
		}
		$privacy_policy_page = get_post( get_option( 'wp_page_for_privacy_policy' ) );
		return ! $privacy_policy_page || ( $privacy_policy_page && 'publish' !== $privacy_policy_page->post_status );
	}

	private function get_is_thank_you_page_create_checked() {
		if ( $this->is_initialized() ) {
			return false;
		}
		return ! get_page_by_path( 'thank-you' );
	}

	private function is_initialized() {
		return get_option( 'xd_initialized' );
	}

	private function write_config( $definitions = null ) {
		if( ! file_exists( WPMU_PLUGIN_DIR ) ){
			mkdir(WPMU_PLUGIN_DIR);
		}
		$theme_version = wp_get_theme()->get( 'Version' );
		$output      = array(
			'<?php',
			'/**',
			' * Plugin Name:     Leap Config',
			' * Plugin URI:      https://leapxd.com/',
			' * Description:     Installs plugin licenses, settings, and api keys',
			' * Author:          Leap XD',
			' * Author URI:      https://leapxd.com/',
			' * Version:         '. $theme_version,
			' *', 
			' * Theme constants.',
			' * Auto generated - do not edit',
			' *',
			' * @package Kicks',
			' */',
			'',
		);
		$definitions = ! empty( $definitions ) ? $definitions : $this->definitions;
		foreach ( $definitions as $key => $definition ) {
			switch ( true ) {
				case is_int( $definition ):
					$val = $definition;
					break;
				case true === $definition || false === $definition:
					$val = $definition ? 'true' : 'false';
					break;
				case 'true' === $definition || 'false' === $definition:
					$val = filter_var( $definition, FILTER_VALIDATE_BOOLEAN ) ? 'true' : 'false';
					break;
				case is_string( $definition ) && empty( $definition ):
					$val = "''";
					break;
				case strtoupper( $definition ) === $definition:
					$val = $definition;
					break;
				default:
					$val = "'$definition'";
			}
			$output[] = "define( '$key', $val );";
		}
		$output[] = '';
		file_put_contents( $this->config_file, implode( "\n", $output ) );
		sleep(3);

	}

	private function check_config() {
		return file_exists( $this->config_file );
	}

	private function import_acf_fields() {
		if ( ! get_option('acf_initialized') ) {
			acf_update_setting( 'json', false );
			foreach ( glob( get_template_directory() . '/acf-json/*.json' ) as $field_group ) {
				$local_field_group       = json_decode( file_get_contents( $field_group ), true );
				$local_field_group['ID'] = $local_field_group['key'];
				acf_import_field_group( $local_field_group );
			}
			update_option('acf_initialized',true);
		}
	}

	private function activate_plugins( $plugins ) {
		require_once ABSPATH . '/wp-admin/includes/plugin.php';
		if ( is_array( $plugins ) ) {
			foreach ( $plugins as $plugin ) {
				activate_plugin( $plugin );
			}
		}
	}

	private function get_plugins() {
		require_once ABSPATH . '/wp-admin/includes/plugin.php';
		$plugins = array();
		foreach ( get_plugins() as $plugin_file => $plugin ) {
			if ( ! is_plugin_active( $plugin_file ) ) {
				$plugins[ $plugin_file ] = $plugin;
			}
		}
		return $plugins;
	}
}
