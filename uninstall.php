<?php

/*
 * Uninstaller script
 * Only runs when the plugin is being uninstalled via the WordPress admin panel
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( 'What you doin?' );
}

// Remove options
remove_option( 'spam-killer-stats' );
remove_option( 'spam-killer-level' );

// Remove network wide options
remove_site_option( 'spam-killer-comments-blacklist' );
remove_site_option( 'spam-killer-url-blacklist' );
