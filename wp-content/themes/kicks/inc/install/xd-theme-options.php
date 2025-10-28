<?php
/**
 * Config Form Html.
 *
 * @package Kicks
 */

?>
<form method="POST">
	<br>
	<h2>
		XD Theme Init
	</h2>
	<br>
	<br>
	<h3>Google Maps</h3>
	<table
		class="form-table"
		role="presentation"
	>
		<tbody>
			<tr>
				<th><label for="api_key">Api key</label></th>
				<td><input
						type="text"
						name="definitions[GOOGLE_MAPS_API_KEY]"
						value="<?php $this->the_definition( 'GOOGLE_MAPS_API_KEY' ); ?>"
						id="api_key"
						class="regular-text"
					></td>
			</tr>

		</tbody>
	</table>

	<h3>Recaptcha</h3>
	<table
		class="form-table"
		role="presentation"
	>
		<tbody>
			<tr>
				<th><label for="site_key">Site key</label></th>
				<td><input
						type="text"
						name="definitions[RECAPTCHA_PUBLIC_KEY]"
						value="<?php $this->the_definition( 'RECAPTCHA_PUBLIC_KEY' ); ?>"
						id="site_key"
						class="regular-text"
					></td>
			</tr>
			<tr>
				<th><label for="secret_key">Secret key</label></th>
				<td><input
						type="text"
						name="definitions[RECAPTCHA_PRIVATE_KEY]"
						id="secret_key"
						value="<?php $this->the_definition( 'RECAPTCHA_PRIVATE_KEY' ); ?>"
						class="regular-text"
					></td>
			</tr>

		</tbody>
	</table>

	<h3>Mailgun</h3>
	<table
		class="form-table"
		role="presentation"
	>
		<tbody>
			<tr>
				<th><label for="mailgun_from">Mailgun from name</label></th>
				<td><input
						type="text"
						name="definitions[MAILGUN_FROM_NAME]"
						value="<?php $this->the_definition( 'MAILGUN_FROM_NAME' ); ?>"
						id="mailgun_from"
						class="regular-text"
					></td>
			</tr>
			<tr>
				<th><label for="mailgun_address">Mailgun from email</label></th>
				<td><input
						type="text"
						name="definitions[MAILGUN_FROM_ADDRESS]"
						id="mailgun_address"
						value="<?php $this->the_definition( 'MAILGUN_FROM_ADDRESS' ); ?>"
						class="regular-text"
					></td>
			</tr>

		</tbody>
	</table>

	<h3>Gravity Forms</h3>
	<table
		class="form-table"
		role="presentation"
	>
		<tbody>
			<tr>
				<th><label for="notification_from">Notifications from email</label></th>
				<td><input
						type="text"
						name="notification_from"
						value="<?php $this->the_gravity_form_notification( 'from' ); ?>"
						id="notification_from"
						class="regular-text"
					></td>
			</tr>
			<tr>
				<th><label for="notification_to">Notifications to email</label></th>
				<td><input
						type="text"
						name="notification_to"
						id="notification_to"
						value="<?php $this->the_gravity_form_notification( 'to' ); ?>"
						class="regular-text"
					></td>
			</tr>

		</tbody>
	</table>


	<h3>
		Menus
	</h3>
	<table
		class="form-table"
		role="presentation"
	>
		<tbody>
			<?php if ( empty( wp_get_nav_menu_object( 'primary-menu' ) ) ) : ?>
			<tr>
				<th scope="row">Primary menu</th>
				<td>
					<label for="primary_menu">
						<input
							name="install_menu[primary-menu]"
							type="checkbox"
							id="primary_menu"
							value="Primary Menu"
							<?php checked( ! $this->is_initialized() ); ?>

						>
						Install Primary Menu
					</label>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="primary_menu_items">Primary menu items</label></th>
				<td>
					<textarea
						rows="4"
						type="text"
						name="menu_items[primary-menu]"
						id="primary_menu_items"
						class="regular-text"
					>Home=[front-page]<?php echo "\n"; ?>Blog=[blog]<?php echo "\n"; ?>Contact=[contact]<?php echo "\n"; ?>StyleGuide=[style-guide]</textarea>
				</td>
			</tr>
			<?php endif; ?>
			<?php if ( empty( wp_get_nav_menu_object( 'navbar-menu' ) ) ) : ?>
			<tr>
				<th scope="row">Nav menu</th>
				<td>
					<label for="nav_menu">
						<input
							name="install_menu[nav-menu]"
							type="checkbox"
							id="nav_menu"
							value="Nav Menu"
							<?php checked( ! $this->is_initialized() ); ?>
						>
						Install Nav Menu
					</label>
				</td>
			</tr>
			<?php endif; ?>
			<?php if ( empty( wp_get_nav_menu_object( 'footer-menu' ) ) ) : ?>
			<tr>
				<th scope="row">Footer menu</th>
				<td>
					<label for="footer_menu">
						<input
							name="install_menu[footer-menu]"
							type="checkbox"
							id="footer_menu"
							value="Footer Menu"
							<?php checked( ! $this->is_initialized() ); ?>
						>
						Install Footer Menu
					</label>
				</td>
			</tr>
			<?php endif; ?>
			<?php if ( empty( wp_get_nav_menu_object( 'footer-menu-2' ) ) ) : ?>
			<tr>
				<th scope="row">Footer menu 2</th>
				<td>
					<label for="footer_menu_2">
						<input
							name="install_menu[footer-menu-2]"
							type="checkbox"
							id="footer_menu_2"
							value="Footer Menu 2"
						>
						Install Footer Menu 2
					</label>
				</td>
			</tr>
			<?php endif; ?>
			<?php if ( empty( wp_get_nav_menu_object( 'footer-nav-menu' ) ) ) : ?>
			<tr>
				<th scope="row">Footer Nav Menu</th>
				<td>
					<label for="footer_nav_menu">
						<input
							name="install_menu[footer-nav-menu]"
							type="<?php echo ! empty( wp_get_nav_menu_object( 'footer-nav-menu' ) ) ? 'hidden' : 'checkbox'; ?>"
							id="footer_nav_menu"
							value="Footer Nav Menu"
							<?php checked( ! $this->is_initialized() ); ?>
						>
						Install Footer Nav Menu
					</label>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="footer_menu_items">Footer nav menu items</label></th>
				<td>
					<textarea
						rows="4"
						type="text"
						name="menu_items[footer-nav-menu]"
						id="footer_menu_items"
						class="regular-text"
					>Privacy Policy=[privacy-policy]<?php echo "\n"; ?>Website by Leap XD=https://leapxd.com</textarea>
				</td>
			</tr>
			<?php endif; ?>

		</tbody>
	</table>

	<h3>
		Create Pages
	</h3>
	<table
		class="form-table"
		role="presentation"
	>
		<tbody>
			<tr>
				<th scope="row">Front page</th>
				<td>
					<label for="front_page">
						<input
							name="front_page"
							type="checkbox"
							id="front_page"
							<?php checked( $this->get_is_front_page_create_checked() ); ?>
						>
						Insert front page and set as front page
					</label>
				</td>
			</tr>
			<tr>
				<th>Blog page</th>
				<td>
					<label for="blog_page">
						<input
							name="blog_page"
							type="checkbox"
							id="blog_page"
							<?php checked( ! $this->is_initialized() ); ?>
						>
						Insert blog page and set as posts page
					</label>
				</td>
			</tr>
			<tr>
				<th>Contact page</th>
				<td>
					<label for="contact_page">
						<input
							name="contact_page"
							type="checkbox"
							id="contact_page"
							<?php checked( $this->get_is_contact_page_create_checked() ); ?>
						>
						Insert contact page and set as contact page
					</label>
				</td>
			</tr>
			<tr>
				<th>Thank you page</th>
				<td>
					<label for="thank_you_page">
						<input
							name="thank_you_page"
							type="checkbox"
							id="thank_you_page"
							<?php checked( $this->get_is_thank_you_page_create_checked() ); ?>
						>
						Insert thank you page and set as thank you page
					</label>
				</td>
			</tr>
			<tr>
				<th>Style guide page</th>
				<td>
					<label for="style_guide_page">
						<input
							name="style_guide_page"
							type="checkbox"
							id="style_guide_page"
							<?php checked( ! $this->is_initialized() ); ?>
						>
						Insert style guide page
					</label>
				</td>
			</tr>

		</tbody>
	</table>

	<h3>
		Privacy policy
	</h3>
	<table
		class="form-table"
		role="presentation"
	>
		<tbody>
			<tr>
				<th><label for="company_name">Company name</label></th>
				<td><input
						type="text"
						name="company_name"
						value="<?php echo esc_html( get_option( 'options_option_company_name' ) ); ?>"
						id="company_name"
						class="regular-text"
					></td>
			</tr>
			<tr>
				<th scope="row">Update</th>
				<td>
					<label for="privacy_policy">
						<input
							name="privacy_policy"
							type="checkbox"
							id="privacy_policy"
							<?php checked( $this->get_is_privacy_policy_checked() ); ?>
						>
						Update privacy policy from template and publish
					</label>
				</td>
			</tr>
		</tbody>
	</table>

	<h3>
		Plugins
	</h3>
	<table
		class="form-table"
		role="presentation"
	>
		<tbody>
			<tr>
				<th scope="row">Activate plugins</th>
				<td>
					<?php foreach ( $this->get_plugins() as $plugin_file => $plugin_array ) : ?>
					<div>
						<label for="<?php echo esc_attr( $plugin_file ); ?>">
							<input
								name="plugins[<?php echo esc_attr( $plugin_file ); ?>]"
								type="checkbox"
								id="<?php echo esc_attr( $plugin_file ); ?>"
								value="<?php echo esc_attr( $plugin_file ); ?>"
							>
							<?php echo esc_html( $plugin_array['Name'] ); ?>
						</label>
					</div>
					<?php endforeach ?>
				</td>
			</tr>
		</tbody>
	</table>


	<table
		class="form-table"
		role="presentation"
	>
		<tbody>
			<tr>
				<th scope="row"><label></label></th>
				<td>
					<input
						class="button button-primary"
						name="submit"
						value="Submit"
						type="submit"
					>
				</td>
			</tr>

		</tbody>
	</table>

	<?php wp_nonce_field( 'xd-theme-options', 'xd-theme-options' ); ?>
</form>
