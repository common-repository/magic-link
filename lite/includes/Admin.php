<?php

/**
 * The dashboard-specific functionality of the plugin.
 *
 * @link       https://kaizencoders.com
 * @since      1.0.0
 *
 * @package    MagicLink
 * @subpackage MagicLink/admin
 */

namespace KaizenCoders\MagicLink;

/**
 * The dashboard-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    MagicLink
 * @subpackage MagicLink/admin
 * @author     KaizenCoders <hello@kaizencoders.com>
 */
class Admin {
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

	/**
	 * Register the stylesheets for the Dashboard.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

	}

	/**
	 * Register the JavaScript for the dashboard.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'magic-link-script', \plugin_dir_url( __DIR__ ) . 'dist/scripts/magic-link.js', [ 'jquery', 'wp-i18n' ],
			KAIZENCODERS_MAGIC_LINK_PLUGIN_VERSION, true );
		wp_localize_script( 'magic-link-script', 'magicLinkAjax', [
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'generate_magic_link_nonce' ),
		] );

	}

	public function add_magic_link_column( $columns ) {
		$columns['magic_link'] = __( 'Magic Link', 'magic-link' );

		return $columns;

	}

	public function show_magic_link_button( $value, $column_name, $user_id ) {
		$magic_link = '';
		if ( $column_name == 'magic_link' ) {
			$magic_link .= '<button class="generate-magic-link button" data-user-id="' . esc_attr( $user_id ) . '">' . __( 'Generate Magic Link',
					'magic-link' ) . '</button><br><span class="magic-link" id="magic-link-' . esc_attr( $user_id ) . '"></span>';
		}

		$link = Helper::get_login_link( $user_id );

		if ( ! empty( $link ) ) {
			$magic_link .= '<input type="text" value="' . $link . '" readonly><button class="copy-magic-link button">' . __( 'Copy',
					'magic-link' ) . '</button>';
		}

		return $magic_link;
	}

	public function generate_magic_link() {
		check_ajax_referer( 'generate_magic_link_nonce', 'nonce' );

		if ( empty( $_POST['user_id'] ) ) {
			wp_send_json_error( 'User ID is missing.' );
		}

		$user_id = intval( wp_unslash( $_POST['user_id'] ) );

		if ( ! $user_id ) {
			wp_send_json_error( 'Invalid user ID.' );
		}

		$user = get_user_by( 'id', $user_id );

		$magic_link = Helper::create_login_link( $user );

		wp_send_json_success( [ 'magic_link' => $magic_link ] );
	}


}
