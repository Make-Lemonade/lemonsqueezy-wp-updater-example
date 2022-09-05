<?php

class ExamplePluginSettings {
	private $example_plugin_settings_options;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'example_plugin_settings_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'example_plugin_settings_page_init' ) );
		add_action( "update_option_example_plugin_settings_option_name", array( $this, 'handle_license_activation' ), 10, 3 );
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

		$license_message = json_decode(get_option( 'example_plugin_license_message' ));
		$message = false;

		if ( isset( $license_message->data->activated ) ) {
			if ( $license_message->data->activated ) {
				$message = "License is active. You have {$license_message->data->license_key->activation_usage}/{$license_message->data->license_key->activation_limit} instances activated.";
			} else {
				$message = $license_message->error ?: "License for this site is not active. Click the button below to activate.";
			}
		}

		$license_key = get_option( 'example_plugin_settings_option_name' );
		if ( isset( $license_key['api_key_0'] ) && ! empty( $license_key['api_key_0'] ) && $message ) {
			echo "<p class='description'>{$message}</p>";
		}
	}

	public function handle_license_activation( $old_value, $new_value, $option ) {
		if ( isset( $new_value['api_key_0'] ) && ! empty( $new_value['api_key_0'] ) && $old_value['api_key_0'] !== $new_value['api_key_0'] ) {
			$this->activate_license( $new_value['api_key_0'] );
		}

		if ( isset( $new_value['api_key_0'] ) && empty( $new_value['api_key_0' ] ) && ! empty( $old_value['api_key_0'] ) ) {
			$license_message = json_decode( get_option( 'example_plugin_license_message' ) );
			if ( isset( $license_message->data->instance->id ) ) {
				$this->deactivate_license( $old_value['api_key_0'], $license_message->data->instance->id );
			}
		}
	}

	public function activate_license( $license_key ) {
		$activation_url = add_query_arg(
			[
				'license_key' => $license_key,
				'instance_name' => home_url(),
			],
			EXAMPLE_PLUGIN_API_URL . '/activate'
		);

		$response = wp_remote_get( $activation_url, [
			'sslverify' => false,
			'timeout' => 10,
		] );

		if (
			is_wp_error( $response )
			|| ( 200 !== wp_remote_retrieve_response_code( $response ) && 400 !== wp_remote_retrieve_response_code( $response ) )
			|| empty( wp_remote_retrieve_body( $response ) )
		) {
			return;
		}

		update_option( 'example_plugin_license_message', wp_remote_retrieve_body( $response ) );
	}

	public function deactivate_license( $license_key, $instance_id ) {
		$activation_url = add_query_arg(
			[
				'license_key' => $license_key,
				'instance_id' => $instance_id,
			],
			EXAMPLE_PLUGIN_API_URL . '/deactivate'
		);

		$response = wp_remote_get( $activation_url, [
			'sslverify' => false,
			'timeout' => 10,
		] );

		if ( 200 === wp_remote_retrieve_response_code( $response ) ) {
			delete_option( 'example_plugin_license_message' );
		}
	}
}

if ( is_admin() ) {
	$example_plugin_settings = new ExamplePluginSettings();
}
