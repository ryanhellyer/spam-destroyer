<?php

/**
 * Spam Destroyer settings class
 * This adds links to the plugins page.
 * Allows for manually resetting the spam key.
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.8
 */
class Spam_Destroyer_Settings extends Spam_Destroyer {

	/**
	 * Fire the constructor up :)
	 */
	public function __construct() {
		add_filter( 'plugin_row_meta', array( $this, 'plugins_page_meta' ), 10, 4 );
	}

	/**
	 * Add a settings page link to the plugin list.
	 *
	 * The code was adapted from https://gist.github.com/lloc/5685040
	 * The idea was adapted from the Minit plugin by Kaspars Dambis (http://kaspars.net/)
	 *
	 * @param  array    $plugin_meta   Plugin meta links
	 * @param  string   $plugin_file   The plugin file name
	 * @return array    The plugin meta
	 */
	public function plugins_page_meta( $plugin_meta, $plugin_file ) {

		// Bail out now if not on Spam Destroyer plugin
	    if ( 'spam-destroyer/index.php' != $plugin_file ) {
	    	return $plugin_meta;
	    }

		// Add the plugin page link
		$plugin_meta[] = sprintf( 
			'<a href="%s">%s</a>',
			'https://geek.hellyer.kiwi/plugins/spam-destroyer/', 
			__( 'Plugin page' ) 
		);

		// Add the donations page link
		$plugin_meta[] = sprintf( 
			'<a href="%s">%s</a>',
			'https://geek.hellyer.kiwi/donations/', 
			__( 'Donate', 'spam-destroyer' ) 
		);

		return $plugin_meta;
	}

}
new Spam_Destroyer_Settings();
