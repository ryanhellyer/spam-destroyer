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

	public $nonce = 'spam-destroyer';

	/**
	 * Fire the constructor up :)
	 */
	public function __construct() {
		add_filter( 'plugin_row_meta', array( $this, 'plugins_page_meta' ), 10, 4 );
		add_action( 'admin_init',      array( $this, 'reset_spam_key' ) );
	}

	/**
	 * Add an admin notice, saying that spam key has been changed.
	 */
	public function key_change_notice() {
		echo '
		<div class="updated">
			<p>' . __( 'The Spam Destroyer key has been reset.', 'spam-destroyer' ) . '</p>
		</div>';
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

		// Add the spam key reset link
		$plugin_meta[] = sprintf( 
			'<a href="%s">%s</a>',
			wp_nonce_url( admin_url( 'plugins.php?reset_spam_key=true' ), $this->nonce ), 
			__( 'Reset spam key', 'spam-destroyer' ) 
		);

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

	/**
	 * Generate a new spam key.
	 */
	public function reset_spam_key() {

		// Bail out if not on correct page
		if (
//			! check_admin_referer( $this->nonce )
//			||
			! is_admin()
			||
			! isset( $_GET['reset_spam_key'] )
			||
			! current_user_can( 'manage_options' )
		) {
			return;
		}

		// Delete the spam key - will be reset during next page load when not found
		delete_option( $this->spam_key_option );

		// Add admin notice about key change
		add_action( 'admin_notices',   array( $this, 'key_change_notice' ) );
	}

}
new Spam_Destroyer_Settings();
