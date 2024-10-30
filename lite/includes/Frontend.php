<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://kaizencoders.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/Frontend
 */

namespace KaizenCoders\MagicLink;

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    MagicLink
 * @author     KaizenCoders <hello@kaizencoders.com>
 */
class Frontend {
	/**
	 * The plugin's instance.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    Plugin $plugin This plugin's instance.
	 */
	private $plugin;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param  Plugin  $plugin  This plugin's instance.
	 *
	 * @since 1.0.0
	 *
	 */
	public function __construct( Plugin $plugin ) {
		$this->plugin = $plugin;
	}

	function handle_login_request() {
		global $pagenow;

		if ( empty( $_GET['magic-link'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		if ( empty( $_GET['user_id'] ) || empty( $_GET['magic-token'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$user_id = intval( wp_unslash( $_GET['user_id'] ) );

		if ( is_user_logged_in() ) {
			$error = __( 'Invalid magic link token', 'magic-link' );
			wp_die( $error );
		}

		$user = get_user_by( 'id', $user_id );

		if ( ! $user ) {
			$error = __( 'Invalid User', 'magic-link' );
			wp_die( $error );
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$user_token = sanitize_text_field( wp_unslash( $_GET['magic-token'] ) );

		$tokens = Helper::get_user_tokens( $user_id );

		$is_valid = false;
		foreach ( $tokens as $i => $token_data ) {
			if ( empty( $token_data ) || ! is_array( $token_data ) || ! isset( $token_data['token'] ) ) {
				unset( $tokens[ $i ] );
				continue;
			}

			if ( Helper::validate_user_token( $user_token, $token_data['h_token'] ) ) {
				$is_valid = true;
				break;
			}
		}

		if ( ! $is_valid ) {
			$error = __( 'Invalid Token', 'magic-link' );
			wp_die( wp_kses_post( $error ) );
		}

		wp_set_auth_cookie( $user->ID, true, is_ssl() );

		do_action( 'wp_login', $user->user_login, $user );

		$default_redirect = Helper::get_user_default_redirect( $user );
		wp_safe_redirect( $default_redirect );
		exit;
	}

}
