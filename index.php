<?php
/**
 * Plugin Name: Spam Destroyer
 * Plugin URI: https://geek.hellyer.kiwi/products/spam-destroyer/
 * Description: Kills spam dead in it's tracks
 * Author: Ryan Hellyer
 * Version: 3.0
 * Author URI: https://geek.hellyer.kiwi/
 *
 * Copyright (c) 2012 - 2023 Ryan Hellyer
 *
 *
 * Based on the following open source projects:
 *
 * Cookies for Comments by Donncha O Caoimh
 * http://ocaoimh.ie/cookies-for-comments/
 *
 * WP Hashcash by Elliot Back
 * http://wordpress-plugins.feifei.us/hashcash/
 *
 * Spam Catharsis by Brian Layman
 * http://TheCodeCave.com/plugins/spam-catharsis/
 *
 * Script para la generación de CAPTCHAS by Jose Rodrigueze
 * http://code.google.com/p/cool-php-captcha
 *
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2 as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * license.txt file included with this plugin for more information.
 *
 * @package Spam Destroyer
 */

declare(strict_types=1);

namespace SpamDestroyer;

require_once __DIR__ . '/vendor/autoload.php';

$plugin_instances = Factory::create();
foreach ( $plugin_instances as $instance ) {
	if ( method_exists( $instance, 'init' ) ) {
		$instance->init();
	}
}


/*
// Defining folder locations
define( 'SPAM_DESTROYER_DIR', dirname( __FILE__ ) );
define( 'SPAM_DESTROYER_URL', plugin_dir_url( __FILE__ ) );

// Load the bare minimum for the front-end
require( 'inc/class-spam-destroyer.php' );

// Load extra modules - provides extra protection when required
require( 'inc/class-spam-destroyer-add-meta.php' );
require( 'inc/class-spam-destroyer-stats.php' );

// Only load generate CAPTCHA class if appropriate GET request sent
if ( isset( $_GET['captcha'] ) ) {
	require( 'inc/class-spam-destroyer-generate-captcha.php' );
}

// Load admin panel only files
if ( is_admin() ) {

	require( 'inc/class-spam-destroyer-settings.php' );

	// Loading dotorg plugin review code
	require( 'inc/class-dotorg-plugin-review.php' );
	new DotOrg_Plugin_Review(
		array(
			'slug'        => 'spam-destroyer', // The plugin slug
			'name'        => 'Spam Destroyer', // The plugin name
			'time_limit'  => WEEK_IN_SECONDS,  // The time limit at which notice is shown
		)
	);

}
*/


/*
 TODO:   INCLUDE WP CODING STANDARDS IN composer.json
 */
