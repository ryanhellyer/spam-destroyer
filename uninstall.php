<?php

/*
 * Uninstaller script
 * Only runs when the plugin is being uninstalled via the WordPress admin panel
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'What you doin?' );
}

// Remove options
delete_option( 'spam-killer-level' );
delete_option( 'spam-killer-frequency-stats' );
delete_option( 'spam-killer-recent-stats' );
delete_option( 'spam-killer-historical-stats' );

// Remove network wide options
delete_site_option( 'spam-killer-comments-blacklist' );
delete_site_option( 'spam-killer-url-blacklist' );
