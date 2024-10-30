<?php

namespace KaizenCoders\MagicLink;

use KaizenCoders\MagicLink\Option;

/**
 * Plugin_Name
 *
 * @link      https://kaizencoders.com
 * @author    KaizenCoders <hello@kaizencoders.com>
 * @package   MagicLink
 */

/**
 * Helper Class
 */
class Helper {

	/**
	 * Whether given user is an administrator.
	 *
	 * @param  \WP_User  $user  The given user.
	 *
	 * @return bool
	 */
	public static function is_user_admin( \WP_User $user = null ) {
		if ( is_null( $user ) ) {
			$user = wp_get_current_user();
		}

		if ( ! $user instanceof WP_User ) {
			_doing_it_wrong( __METHOD__, 'To check if the user is admin is required a WP_User object.', '1.0.0' );
		}

		return is_multisite() ? user_can( $user, 'manage_network' ) : user_can( $user, 'manage_options' );
	}

	/**
	 * What type of request is this?
	 *
	 * @param  string  $type  admin, ajax, cron, cli or frontend.
	 *
	 * @return bool
	 * @since 1.0.0
	 *
	 */
	public function request( $type ) {
		switch ( $type ) {
			case 'admin_backend':
				return $this->is_admin_backend();
			case 'ajax':
				return $this->is_ajax();
			case 'installing_wp':
				return $this->is_installing_wp();
			case 'rest':
				return $this->is_rest();
			case 'cron':
				return $this->is_cron();
			case 'frontend':
				return $this->is_frontend();
			case 'cli':
				return $this->is_cli();
			default:
				_doing_it_wrong( __METHOD__, esc_html( sprintf( 'Unknown request type: %s', $type ) ), '1.0.0' );

				return false;
		}
	}

	/**
	 * Is installing WP
	 *
	 * @return boolean
	 */
	public function is_installing_wp() {
		return defined( 'WP_INSTALLING' );
	}

	/**
	 * Is admin
	 *
	 * @return boolean
	 * @since 1.0.0
	 */
	public function is_admin_backend() {
		return is_user_logged_in() && is_admin();
	}

	/**
	 * Is ajax
	 *
	 * @return boolean
	 * @since 1.0.0
	 */
	public function is_ajax() {
		return ( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() ) || defined( 'DOING_AJAX' );
	}

	/**
	 * Is rest
	 *
	 * @return boolean
	 * @since 1.0.0
	 */
	public function is_rest() {
		return defined( 'REST_REQUEST' );
	}

	/**
	 * Is cron
	 *
	 * @return boolean
	 * @since 1.0.0
	 */
	public function is_cron() {
		return ( function_exists( 'wp_doing_cron' ) && wp_doing_cron() ) || defined( 'DOING_CRON' );
	}

	/**
	 * Is frontend
	 *
	 * @return boolean
	 * @since 1.0.0
	 */
	public function is_frontend() {
		return ( ! $this->is_admin_backend() || ! $this->is_ajax() ) && ! $this->is_cron() && ! $this->is_rest();
	}

	/**
	 * Is cli
	 *
	 * @return boolean
	 * @since 1.0.0
	 */
	public function is_cli() {
		return defined( 'WP_CLI' ) && WP_CLI;
	}

	/**
	 * Define constant
	 *
	 * @param $value
	 *
	 * @param $name
	 *
	 * @since 1.0.0
	 *
	 */
	public static function maybe_define_constant( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Get current date time
	 *
	 * @return false|string
	 */
	public static function get_current_date_time() {
		return gmdate( 'Y-m-d H:i:s' );
	}


	/**
	 * Get current date time
	 *
	 * @return false|string
	 */
	public static function get_current_gmt_timestamp() {
		return strtotime( gmdate( 'Y-m-d H:i:s' ) );
	}

	/**
	 * Get current date
	 *
	 * @return false|string
	 */
	public static function get_current_date() {
		return gmdate( 'Y-m-d' );
	}

	/**
	 * Format date time
	 *
	 * @param $date
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 *
	 */
	public static function format_date_time( $date ) {
		$convert_date_format = get_option( 'date_format' );
		$convert_time_format = get_option( 'time_format' );

		$local_timestamp = ( $date !== '0000-00-00 00:00:00' ) ? date_i18n( "$convert_date_format $convert_time_format",
			strtotime( get_date_from_gmt( $date ) ) ) : '<i class="dashicons dashicons-es dashicons-minus"></i>';

		return $local_timestamp;
	}

	/**
	 * Clean String or array using sanitize_text_field
	 *
	 * @param $variable Data to sanitize
	 *
	 * @return array|string
	 *
	 * @since 1.0.0
	 *
	 */
	public static function clean( $var ) {
		if ( is_array( $var ) ) {
			return array_map( [ __CLASS__, 'clean' ], $var );
		} else {
			return is_scalar( $var ) ? sanitize_text_field( $var ) : $var;
		}
	}

	/**
	 * Insert $new in $array after $key
	 *
	 * @param $key
	 * @param $new
	 *
	 * @param $array
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 *
	 */
	public static function array_insert_after( $array, $key, $new ) {
		$keys  = array_keys( $array );
		$index = array_search( $key, $keys );
		$pos   = false === $index ? count( $array ) : $index + 1;

		return array_merge( array_slice( $array, 0, $pos ), $new, array_slice( $array, $pos ) );
	}

	/**
	 * Insert a value or key/value pair before a specific key in an array.  If key doesn't exist, value is prepended
	 * to the beginning of the array.
	 *
	 * @param  string  $key
	 * @param  array  $new
	 *
	 * @param  array  $array
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 *
	 */
	public static function array_insert_before( array $array, $key, array $new ) {
		$keys = array_keys( $array );
		$pos  = (int) array_search( $key, $keys );

		return array_merge( array_slice( $array, 0, $pos ), $new, array_slice( $array, $pos ) );
	}

	/**
	 * Insert $new in $array after $key
	 *
	 * @param $array
	 *
	 * @return boolean
	 *
	 * @since 1.0.0
	 *
	 */
	public static function is_forechable( $array = [] ) {

		if ( ! is_array( $array ) ) {
			return false;
		}

		if ( empty( $array ) ) {
			return false;
		}

		if ( count( $array ) <= 0 ) {
			return false;
		}

		return true;
	}

	/**
	 * Get current db version
	 *
	 * @since 1.0.0
	 */
	public static function get_db_version() {
		return Option::get( 'db_version', null );
	}

	/**
	 * Get data from array
	 *
	 * @param  string  $var
	 * @param  string  $default
	 * @param  bool  $clean
	 *
	 * @param  array  $array
	 *
	 * @return array|string
	 *
	 * @since 1.0.0
	 *
	 */
	public static function get_data( $array = [], $var = '', $default = '', $clean = false ) {
		if ( empty( $array ) ) {
			return $default;
		}

		if ( ! empty( $var ) || ( 0 === $var ) ) {
			$value = isset( $array[ $var ] ) ? wp_unslash( $array[ $var ] ) : $default;
		} else {
			$value = wp_unslash( $array );
		}

		if ( $clean ) {
			$value = self::clean( $value );
		}

		return $value;
	}

	/**
	 * Get all Plugin admin screens
	 *
	 * @return array|mixed|void
	 * @since 1.0.0
	 */
	public static function get_plugin_admin_screens() {
		// TODO: Can be updated with a version check when https://core.trac.wordpress.org/ticket/18857 is fixed
		$prefix = sanitize_title( __( 'MagicLink', 'magic-link' ) );

		$screens = [
		];

		return apply_filters( 'kaizencoders_magic_link_admin_screens', $screens );
	}

	/**
	 * Is es admin screen?
	 *
	 * @param  string  $screen_id  Admin screen id
	 *
	 * @return bool
	 * @since 1.0.0
	 *
	 */
	public static function is_plugin_admin_screen( $screen_id = '' ) {

		$current_screen_id = self::get_current_screen_id();

		// Check for specific admin screen id if passed.
		if ( ! empty( $screen_id ) ) {
			if ( $current_screen_id === $screen_id ) {
				return true;
			} else {
				return false;
			}
		}

		$plugin_admin_screens = self::get_plugin_admin_screens();

		if ( in_array( $current_screen_id, $plugin_admin_screens ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Get Current Screen Id
	 *
	 * @return string
	 * @since 1.0.0
	 */
	public static function get_current_screen_id() {

		$current_screen = function_exists( 'get_current_screen' ) ? get_current_screen() : false;

		if ( ! $current_screen instanceof \WP_Screen ) {
			return '';
		}

		$current_screen = get_current_screen();

		return ( $current_screen ? $current_screen->id : '' );
	}

	public static function create_user_token( $user ) {
		$new_token    = sha1( wp_generate_password() );
		$hashed_token = hash_hmac( 'sha256', $new_token, wp_salt() );

		$tokens[] = [
			'h_token' => $hashed_token,
			'token'   => $new_token,
			'time'    => time(),
		];

		update_user_meta( $user->ID, '_magic_link_token', $tokens );

		return $new_token;
	}

	public static function get_user_tokens( $user_id ) {
		$tokens = get_user_meta( $user_id, '_magic_link_token', true );
		$tokens = is_array( $tokens ) ? $tokens : [];

		return $tokens;
	}

	public static function validate_user_token( $user_token, $token ) {
		if ( hash_equals( $token, hash_hmac( 'sha256', $user_token, wp_salt() ) ) ) {
			return true;
		}

		return false;
	}

	public static function create_login_link( $user ) {
		$token = self::create_user_token( $user );

		$query_args = [
			'user_id'     => $user->ID,
			'magic-token' => $token,
			'magic-link'  => 1,
		];

		return esc_url_raw( add_query_arg( $query_args, wp_login_url() ) );
	}

	public static function get_login_link( $user ) {

		if ( $user instanceof \WP_User ) {
			$user_id = $user->ID;
		} else {
			$user_id = (int) $user;
		}

		$tokens = self::get_user_tokens( $user_id );

		if ( ! empty( $tokens ) ) {
			foreach ( $tokens as $token_data ) {
				if ( empty( $token_data ) ) {
					continue;
				}

				$query_args = [
					'user_id'     => $user_id,
					'magic-token' => $token_data['token'],
					'magic-link'  => 1,
				];


				return esc_url_raw( add_query_arg( $query_args, wp_login_url() ) );
			}
		}

		return '';
	}

	public static function get_user_default_redirect( $user ) {
		if ( is_multisite() && ! get_active_blog_for_user( $user->ID ) && ! is_super_admin( $user->ID ) ) {
			$redirect_to = user_admin_url();
		} elseif ( is_multisite() && ! $user->has_cap( 'read' ) ) {
			$redirect_to = get_dashboard_url( $user->ID );
		} elseif ( ! $user->has_cap( 'edit_posts' ) ) {
			$redirect_to = $user->has_cap( 'read' ) ? admin_url( 'profile.php' ) : home_url();
		} else {
			$redirect_to = admin_url();
		}

		return $redirect_to;
	}
}
