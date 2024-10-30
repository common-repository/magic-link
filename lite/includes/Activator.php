<?php

/**
 * Fired during plugin activation
 *
 * @link       https://kaizencoders.com
 * @since      1.0.0
 *
 * @package    MagicLink
 * @subpackage MagicLink/includes
 */

namespace KaizenCoders\MagicLink;

use KaizenCoders\MagicLink\Install;

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    MagicLink
 * @subpackage MagicLink/includes
 * @author     KaizenCoders <hello@kaizencoders.com>
 */
class Activator {

	/**
	 *
	 * @since 1.0.0
	 */
	static $redirect_option = 'magic_link_do_activation_redirect';

	/**
	 * @since 1.0.0
	 *
	 * @param $network_wide
	 *
	 */
	public static function activate( $network_wide ) {
		global $wpdb;

		if ( is_multisite() && $network_wide ) {
			// Get all active blogs in the network and activate plugin on each one.
			$args = [
				'deleted' => 0,
				'fields'  => 'ids',
			];

			$blog_ids = \get_sites( $args );

			foreach ( $blog_ids as $blog_id ) {
				self::activate_on_blog( $blog_id );
			}
		} else {
			self::install();
			add_option( self::$redirect_option, true );
		}
	}

	/**
	 * Activate on specific blog
	 *
	 * @since 1.0.0
	 *
	 * @param $blog_id
	 *
	 */
	public static function activate_on_blog( $blog_id ) {
		switch_to_blog( $blog_id );
		self::install();
		add_option( self::$redirect_option, true );
		restore_current_blog();
	}

	/**
	 * Run Installer
	 *
	 * @since 1.0.0
	 */
	public static function install() {
		Install::install();
	}
}
