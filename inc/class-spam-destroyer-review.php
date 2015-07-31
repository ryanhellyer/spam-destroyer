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
	 * Variables.
	 * These should be customised for each project.
	 */
	private $slug = 'spam-destroyer';      // The plugin slug
	private $name = 'Spam Destroyer';      // The plugin name
	private $time_limit = WEEK_IN_SECONDS; // The time limit at which notice is shown
	private $plugin_file = 'index.php';

	/**
	 * Fire the constructor up :)
	 */
	public function __construct() {

		// Register hook on activation
		$plugin_path = WP_PLUGIN_DIR . '/' . $this->slug . '/' . $this->plugin_file;
		register_activation_hook( $plugin_path, array( $this, 'set_activation_date' ) );
//$this->set_activation_date();

		// Loading main functionality
		add_action( 'admin_init', array( $this, 'check_installation_date' ) );
		add_action( 'admin_init', array( $this, 'set_no_bug' ), 5 );
	}

	/**
	 * Get the current time and set it as an option when the plugin is activated.
	 */
	public function set_activation_date() {
		add_option( $this->slug . '-activation-date', time() );
	}

	/**
	 * Check date on admin initiation and add to admin notice if it was more than the time limit.
	 */
	public function check_installation_date() {

		if ( '' == get_option( $this->slug . '-no-bug' ) ) {

			$install_date = get_option( $this->slug . '-activation-date' );

			if ( ( time() - $install_date ) >  $this->time_limit  ) {
				add_action( 'admin_notices', array( $this, 'display_admin_notice' ) );
			}

		}

	}

	/**
	 * Display Admin Notice, asking for a review
	 */
	public function display_admin_notice() {

		echo '
		<div class="updated">
			<p>' . sprintf( __( 'You have been using the %s plugin for a week now, do you like it? If so, please leave us a review with your feedback!', 'spam-destroyer' ), $this->name ) . '
				<br /><br />
				<a class="button button-primary" href="' . esc_url( 'https://wordpress.org/support/view/plugin-reviews/' . $this->slug . '#postform' ) . '" target="_blank">' . __( 'Leave A Review', 'spam-destroyer' ) . '</a>
				<br />
				<a href="' . esc_url( wp_nonce_url( admin_url( '?' . $this->slug . '-no-bug=true' ), 'review-nonce' ) ) . '">' . __( "Don't show this message again.", 'spam-destroyer' ) . '</a>
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
				! isset( $_GET[$this->slug . '-no-bug'] )
				||
				! current_user_can( 'manage_options' )
			)
		) {
			return;
		}

		add_option( $this->slug . '-no-bug', TRUE );

	}

}
new Spam_Destroyer_Review;
