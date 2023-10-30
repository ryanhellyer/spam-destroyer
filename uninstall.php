<?php
/**
 * Uninstaller Script for Spam Destroyer
 *
 * This file is run when the plugin is uninstalled via the WordPress admin panel.
 *
 * @package Spam Destroyer
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Unauthorized access!' );
}

/**
 * Remove plugin options from the database.
 */
$options = array(
	'spam-destroyer-key',
	'spam-destroyer-version',
);

foreach ( $options as $option ) {
	delete_option( $option );
}
