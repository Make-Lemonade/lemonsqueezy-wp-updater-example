# Lemon Squeezy WordPress Updater Example

This repository contains an example WordPress plugin that demonstrates how to implement automatic updates for premium WordPress plugins using the [Lemon Squeezy WordPress plugin](https://wordpress.org/plugins/lemon-squeezy/) and the [Lemon Squeezy Licenses API](https://docs.lemonsqueezy.com/article/53-licenses-api).

## Usage

### 1. Set up your WordPress site

1. Install the [Lemon Squeezy WordPress plugin](https://wordpress.org/plugins/lemon-squeezy/) on your WordPress site and connect it to Lemon Squeezy.
1. Make sure external requests to the WordPress REST API (`http://example.com/wp-json`) aren't disabled or blocked by a firewall.

### 2. Set up the updater class

1. Copy the [`includes/class-plugin-updater.php`](includes/class-plugin-updater.php) file into your plugin.
1. Rename the `ExamplePluginUpdater` class so that it's unique to your plugin (e.g. `MyPluginUpdater`)
1. Override the `get_license_key()` method to retrieve the users Lemon Squeezy license key. Normally, your plugin would have a settings page where you ask for and store a license key.

### 3. Instanciate the updater class

1. In your plugin code, store the plugin version (e.g. in a `define( 'EXAMPLE_PLUGIN_VERSION', '1.0.0' )`).
1. In your plugin code, store the API URL for the updater. This should point to your WordPress site that is running the Lemon Squeezy WordPress plugin (e.g. `define( 'EXAMPLE_PLUGIN_API_URL', 'http://example.com/wp-json/lsq/v1' )`)
1. Instanciate your updater class, passing in the plugin ID and plugin slug as well as the plugin version and API URL.

See [`example-plugin.php`](example-plugin.php) for an example of how to set this up.

## How it works

1. This plugin code overrides WordPress default updater process to request update information from your WordPress site running the Lemon Squeezy WordPress plugin instead of WordPress.org.
1. The Lemon Squeezy WordPress plugin then uses the Lemon Squeezy API to validate the license key and fetch the required file information.
1. Finally, the Lemon Squeezy WordPress plugin returns the information required for this plugin to determine if it needs an update or not. If an update is required, the download URL returned by the Lemon Squeezy API will be used to provide the update.

```
┌─────────────────────────┐                  ┌───────────────────────────────────┐                   ┌─────────────────────┐
│                         │                  │                                   │                   │                     │
│  Your WordPress Plugin  │                  │  Lemon Squeezy  WordPress Plugin  │                   │  Lemon Squeezy API  │
│                         │                  │                                   │                   │                     │
└───────────┬─────────────┘                  └─────────────────┬─────────────────┘                   └──────────┬──────────┘
            │                                                  │                                                │
            │                                                  │                                                │
            │                                                  │                                                │
            │                                                  │                                                │
            │                                                  │                                                │
            │                                                  │                                                │
            │            Request udpate information            │                                                │
            ├─────────────────────────────────────────────────►│                                                │
            │                                                  │                                                │
            │                                                  │                                                │
            │                                                  │           Request file information             │
            │                                                  ├───────────────────────────────────────────────►│
            │                                                  │                                                │
            │                                                  │                                                │
            │                                                  │                                                │
            │                                                  │                                                │
            │                                                  │             Validate license key               │
            │                                                  │                      +                         │
            │                                                  │            Return file information             │
            │                                                  │◄───────────────────────────────────────────────┤
            │                                                  │                                                │
            │                                                  │                                                │
            │                                                  │                                                │
            │                                                  │                                                │
            │           Return update information              │                                                │
            │◄─────────────────────────────────────────────────┤                                                │
            │                                                  │                                                │
            │                                                  │                                                │
            │                                                  │                                                │
            │                                                  │                                                │
            │                                                  │                                                │
```
