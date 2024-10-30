<?php


namespace KaizenCoders\MagicLink;


class Uninstall {
	/**
	 * Init Uninstall
	 *
	 * @since 1.8.1
	 */
	public function init() {
		magic_link_fs()->add_action( 'after_uninstall', [ $this, 'uninstall_cleanup' ] );
	}

	/**
	 * Delete plugin data
	 *
	 * @since 1.8.1
	 */
	public function uninstall_cleanup() {
		// TODO: Do Uninstall Cleanup.
	}
}