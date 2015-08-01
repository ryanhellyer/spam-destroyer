<?php

/**
 * Spam Destroyer settings class
 * 
 * Based on code by Rhys Wynne ... https://winwar.co.uk/2014/10/ask-wordpress-plugin-reviews-week/
 *
 * @copyright Copyright (c), Ryan Hellyer
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.8
 */
class Spam_Destroyer_Review {

	/**
	 * Constants.
	 * These should be customised for each project.
	 */
	const slug = 'spam-destroyer';      // The plugin slug
	const name = 'Spam Destroyer';      // The plugin name
	const time_limit = WEEK_IN_SECONDS; // The time limit at which notice is shown
	const plugin_file = 'index.php';    // The main plugin file

	/**
	 * Variables.
	 */
	public $nobug_option;

	/**
	 * Fire the constructor up :)
	 */
	public function __construct() {
		$this->nobug_option = self::slug . '-no-bug';

		// Register hook on activation
		$plugin_path = WP_PLUGIN_DIR . '/' . self::slug . '/' . self::plugin_file;
		register_activation_hook( $plugin_path, array( $this, 'set_activation_date' ) );

		// Loading main functionality
		add_action( 'admin_init', array( $this, 'check_installation_date' ) );
		add_action( 'admin_init', array( $this, 'set_no_bug' ), 5 );
	}

	/**
	 * Get the current time and set it as an option when the plugin is activated.
	 */
	public function set_activation_date() {
		add_option( self::slug . '-activation-date', time() );
	}

	/**
	 * Check date on admin initiation and add to admin notice if it was more than the time limit.
	 */
	public function check_installation_date() {

		if ( '' == get_option( $this->nobug_option ) ) {

			$install_date = get_option( self::slug . '-activation-date' );

			if ( ( time() - $install_date ) >  self::time_limit  ) {
				add_action( 'admin_notices', array( $this, 'display_admin_notice' ) );
			}

		}

	}

	/**
	 * Display Admin Notice, asking for a review.
	 */
	public function display_admin_notice() {

		$no_bug_url = wp_nonce_url( admin_url( '?' . $this->nobug_option . '=true' ), 'review-nonce' );

		echo '
		<div class="updated">
			<p>' . sprintf( __( 'You have been using the %s plugin for a week now, do you like it? If so, please leave us a review with your feedback!', 'spam-destroyer' ), self::name ) . '
				<br /><br />
				<a onclick="location.href=\'' . esc_url( $no_bug_url ) . '\';" class="button button-primary" href="' . esc_url( 'https://wordpress.org/support/view/plugin-reviews/' . self::slug . '#postform' ) . '" target="_blank">' . __( 'Leave A Review', 'spam-destroyer' ) . '</a>
				 &nbsp; 
				<a href="' . esc_url( $no_bug_url ) . '">' . __( 'No thanks.', 'spam-destroyer' ) . '</a>
			</p>
		</div>';

	}

	/**
	 * Set the plugin to no longer bug users if user asks not to be.
	 */
	public function set_no_bug() {

		// Bail out if not on correct page
		if (
			! isset( $_GET['_wpnonce'] )
			||
			(
				! wp_verify_nonce( $_GET['_wpnonce'], 'review-nonce' )
				||
				! is_admin()
				||
				! isset( $_GET[$this->nobug_option] )
				||
				! current_user_can( 'manage_options' )
			)
		) {
			return;
		}

		add_option( $this->nobug_option, TRUE );

	}

}
new Spam_Destroyer_Review;
