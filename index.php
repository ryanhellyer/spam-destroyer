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

/**
 * Class autoloader.
 */
spl_autoload_register(
	function ( $class_name ) {
		$prefix   = 'SpamDestroyer\\';
		$base_dir = __DIR__ . '/src/';

		// If the class does not start with our namespace, then bail out.
		if ( strpos( $class_name, $prefix ) !== 0 ) {
			return;
		}

		// Strip the namespace from the class.
		$len = strlen( $prefix );
		if ( strncmp( $class_name, $prefix, $len ) === 0 ) {
				$class = substr( $class_name, $len );
		}

		$path = strtolower( $class );
		$path = str_replace( '_', '-', $path );
		$dirs = explode( '\\', $path );

		// The class is in the root.
		if ( 1 === count( $dirs ) ) {
			$path = $base_dir . 'class-' . $dirs[0] . '.php';
		} else {
			$path = $base_dir . $dirs[0] . '/class-' . $dirs[1] . '.php';
		}

		if ( file_exists( $path ) ) {
			require $path;
		}
	}
);

$plugin_instances = Factory::create();
foreach ( $plugin_instances as $instance ) {
	if ( method_exists( $instance, 'init' ) ) {
		$instance->init();
	}
}
