<?php

/**
 * Timber Admin Settings Page
 *
 * @package Timber
 */
add_action( 'admin_menu', 'timber_loader_add_admin_menu' );
function timber_loader_add_admin_menu() {
	add_options_page(
		'Timber Loader',
		'Timber Loader',
		'manage_options',
		'timber-loader',
		'timber_loader_settings_page'
	);
}

function timber_loader_settings_page() {    ?>
	<div class="wrap">
		<h1>Timber Loader Settings</h1>
		<form method="post" action="options.php">
			<?php
			settings_fields( 'timber_loader_settings' );
			do_settings_sections( 'timber-loader' );
			submit_button();
			?>
		</form>
	</div>
	<?php
}


add_action( 'admin_init', 'timber_loader_admin_init' );
function timber_loader_admin_init() {
	register_setting( 'timber_loader_settings', 'timber_loader_version' );
	add_settings_section( 'timber_loader_main', '', null, 'timber-loader' );
	add_settings_field(
		'timber_loader_version',
		'Select Timber Loader Version',
		'timber_loader_version_field',
		'timber-loader',
		'timber_loader_main'
	);
}

function timber_loader_version_field() {
	$value = get_option( 'timber_loader_version', 'v1' );
	?>
	<select name="timber_loader_version">
		<option value="v1" <?php selected( $value, 'v1' ); ?>>v1</option>
		<option value="v2" <?php selected( $value, 'v2' ); ?>>v2</option>
	</select>
	<?php
}
