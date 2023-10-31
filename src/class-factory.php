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
 * Creates instances of the main classes used in the plugin.
 */
class Factory {

	/**
	 * Create instances of main plugin classes.
	 *
	 * @return array An array containing instances of the main plugin classes.
	 */
	public static function create(): array {
		$classes = array();

		if ( is_admin() ) {
			$classes[] = new Admin\Links();
		} else {
			$shared = new Shared();

			$classes[] = new Frontend\Set_Keys( $shared );
			$classes[] = new Frontend\Catcher(
				new Frontend\Spam_Checking( $shared ),
				new Frontend\Asset_Loading( $shared )
			);
		}

		return $classes;
	}
}
