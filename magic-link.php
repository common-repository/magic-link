<?php
/**
 *
 * Magic Link
 *
 * Simple, Easy and Secure one click login for WordPress.
 *
 * @link      http://wordpress.org/plugins/magic-link
 * @author    KaizenCoders <hello@kaizencoders.com>
 * @license   GPL-2.0+
 * @package   MagicLink
 * @copyright 2024 KaizenCoders
 *
 * @wordpress-plugin
 * Plugin Name:       Magic Link
 * Plugin URI:        https://kaizencoders.com/magic-link
 * Description:       Simple, Easy and Secure one click login for WordPress.
 * Version:           1.0.3
 * Author:            KaizenCoders
 * Author URI:        https://kaizencoders.com
 * Tested up to:      6.6.2
 * Requires PHP:      5.6
 * Text Domain:       magic-link
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! defined( 'KAIZENCODERS_MAGIC_LINK_PLUGIN_VERSION' ) ) {
	define( 'KAIZENCODERS_MAGIC_LINK_PLUGIN_VERSION', '1.0.3' );
}

if ( function_exists( 'magic_link_fs' ) ) {
	magic_link_fs()->set_basename( true, __FILE__ );
} else {
	// Create a helper function for easy SDK access.
	function magic_link_fs() {
		global $magic_link_fs;

		if ( ! isset( $magic_link_fs ) ) {
			// Include Freemius SDK.
			require_once dirname( __FILE__ ) . '/libs/fs/start.php';

			$magic_link_fs = fs_dynamic_init( [
				'id'                  => '16733',
				'slug'                => 'magic-link',
				'type'                => 'plugin',
				'public_key'          => 'pk_2df226c8b5135d3f92b1d1ffa6a7d',
				'is_premium'          => true,
				'has_premium_version' => true,
				'has_addons'          => false,
				'has_paid_plans'      => true,
				'menu'                => [
					'first-path' => 'plugins.php',
				],
			] );
		}

		return $magic_link_fs;
	}

	// Init Freemius.
	magic_link_fs();

	// Use custom icon for onboarding.
	magic_link_fs()->add_filter( 'plugin_icon', function () {
		return dirname( __FILE__ ) . '/assets/images/plugin-icon.png';
	} );


	// Signal that SDK was initiated.
	do_action( 'magic_link_fs_loaded' );


	if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
		require_once __DIR__ . '/vendor/autoload.php';
	}

	if ( ! function_exists( 'kaizencoders_magic_link_fail_php_version_notice' ) ) {

		/**
		 * Admin notice for minimum PHP version.
		 *
		 * Warning when the site doesn't have the minimum required PHP version.
		 *
		 * @return void
		 * @since 1.0.0
		 *
		 */
		function kaizencoders_magic_link_fail_php_version_notice() {
			/* translators: %s: PHP version */
			$message      = sprintf( esc_html__( 'Magic Link requires PHP version %s+, plugin is currently NOT RUNNING.',
				'magic-link' ), '5.6' );
			$html_message = sprintf( '<div class="error">%s</div>', wpautop( $message ) );
			echo wp_kses_post( $html_message );
		}
	}

	if ( ! version_compare( PHP_VERSION, '5.6', '>=' ) ) {

		add_action( 'admin_notices', 'kaizencoders_magic_link_fail_php_version_notice' );

		return;
	}


	// Plugin Folder Path.
	if ( ! defined( 'KAIZENCODERS_MAGIC_LINK_PLUGIN_DIR' ) ) {
		define( 'KAIZENCODERS_MAGIC_LINK_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
	}

	if ( ! defined( 'KAIZENCODERS_MAGIC_LINK_PLUGIN_BASE_NAME' ) ) {
		define( 'KAIZENCODERS_MAGIC_LINK_PLUGIN_BASE_NAME', plugin_basename( __FILE__ ) );
	}

	if ( ! defined( 'KAIZENCODERS_MAGIC_LINK_PLUGIN_FILE' ) ) {
		define( 'KAIZENCODERS_MAGIC_LINK_PLUGIN_FILE', __FILE__ );
	}

	if ( ! defined( 'KAIZENCODERS_MAGIC_LINK_PLUGIN_URL' ) ) {
		define( 'KAIZENCODERS_MAGIC_LINK_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
	}

	/**
	 * The code that runs during plugin activation.
	 * This action is documented in lib/Activator.php
	 */
	\register_activation_hook( __FILE__, '\KaizenCoders\MagicLink\Activator::activate' );

	/**
	 * The code that runs during plugin deactivation.
	 * This action is documented in lib/Deactivator.php
	 */
	\register_deactivation_hook( __FILE__, '\KaizenCoders\MagicLink\Deactivator::deactivate' );


	if ( ! function_exists( 'kaizencoders_magic_link' ) ) {
		/**
		 * Initialize.
		 *
		 * @since 1.0.0
		 */
		function kaizencoders_magic_link() {
			return \KaizenCoders\MagicLink\Plugin::instance();
		}
	}

	add_action( 'plugins_loaded', function () {
		kaizencoders_magic_link()->run();
	} );
}