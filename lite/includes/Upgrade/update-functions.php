<?php
/**
 * Functions for updating data, used by the background updater.
 */

defined( 'ABSPATH' ) || exit;

use KaizenCoders\MagicLink\Install;

/* --------------------- 1.0.0 (Start)--------------------------- */

/**
 * Update DB version
 *
 * @since 1.0.0
 */
function kaizencoders_magic_link_update_100_db_version() {
	Install::update_db_version( '1.0.0' );
}

/* --------------------- 1.0.0 (End)--------------------------- */
