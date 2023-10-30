<?php
/**
 * Factory Class for Spam Destroyer
 *
 * This class is responsible for creating instances of other classes in the plugin.
 *
 * @package Spam Destroyer.
 */

declare(strict_types=1);

namespace SpamDestroyer;

/**
 * Factory Class
 *
 * Creates instances of the main classes used in the Inpsyde Coding Test Plugin.
 */
class Factory {

	/**
	 * Create instances of main plugin classes.
	 *
	 * @return object[] An array containing instances of the main plugin classes.
	 */
	public static function create(): array {
		$classes = array();

		$config = new Config();
		if ( is_admin() ) {
			$classes[] = new Admin\Meta( $config );
			$classes[] = new Admin\Links();
		} else {
			$classes[] = new Frontend\Set_Keys( $config );
			$classes[] = new Frontend\Catcher(
				$config,
				new Frontend\Spam_Checking( $config ),
				new Frontend\Asset_Loading( $config )
			);
		}

		return $classes;
	}
}
