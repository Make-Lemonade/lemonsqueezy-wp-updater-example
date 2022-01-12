<?php

class ExamplePluginSettings {
	private $example_plugin_settings_options;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'example_plugin_settings_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'example_plugin_settings_page_init' ) );
	}

	public function example_plugin_settings_add_plugin_page() {
		add_options_page(
			'Example Plugin Settings', // page_title
			'Example Plugin Settings', // menu_title
			'manage_options', // capability
			'example-plugin-settings', // menu_slug
			array( $this, 'example_plugin_settings_create_admin_page' ) // function
		);
	}

	public function example_plugin_settings_create_admin_page() {
		$this->example_plugin_settings_options = get_option( 'example_plugin_settings_option_name' ); ?>

		<div class="wrap">
			<h2>Example Plugin Settings</h2>
			<p></p>
			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php
					settings_fields( 'example_plugin_settings_option_group' );
					do_settings_sections( 'example-plugin-settings-admin' );
					submit_button();
				?>
			</form>
		</div>
		<?php
	}

	public function example_plugin_settings_page_init() {
		register_setting(
			'example_plugin_settings_option_group', // option_group
			'example_plugin_settings_option_name', // option_name
			array( $this, 'example_plugin_settings_sanitize' ) // sanitize_callback
		);

		add_settings_section(
			'example_plugin_settings_setting_section', // id
			'Settings', // title
			array( $this, 'example_plugin_settings_section_info' ), // callback
			'example-plugin-settings-admin' // page
		);

		add_settings_field(
			'api_key_0', // id
			'API Key', // title
			array( $this, 'api_key_0_callback' ), // callback
			'example-plugin-settings-admin', // page
			'example_plugin_settings_setting_section' // section
		);
	}

	public function example_plugin_settings_sanitize( $input ) {
		$sanitary_values = array();
		if ( isset( $input['api_key_0'] ) ) {
			$sanitary_values['api_key_0'] = sanitize_text_field( $input['api_key_0'] );
		}

		return $sanitary_values;
	}

	public function example_plugin_settings_section_info() {

	}

	public function api_key_0_callback() {
		printf(
			'<input class="regular-text" type="text" name="example_plugin_settings_option_name[api_key_0]" id="api_key_0" value="%s">',
			isset( $this->example_plugin_settings_options['api_key_0'] ) ? esc_attr( $this->example_plugin_settings_options['api_key_0'] ) : ''
		);
	}

}

if ( is_admin() ) {
	$example_plugin_settings = new ExamplePluginSettings();
}
