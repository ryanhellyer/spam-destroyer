<?php

/**
 * Get the current time and set it as an option when the plugin is activated.
 *
 * @return null
 */
function winwar_set_activation_date() {

	$now = strtotime( "now" );
	add_option( 'myplugin_activation_date', $now );

}
register_activation_hook( __FILE__, 'winwar_set_activation_date' );



/**
 * Check date on admin initiation and add to admin notice if it was over 10 days ago.
 *
 * @return null
 */
function winwar_check_installation_date() {

	// Added Lines Start
	$nobug = "";
	$nobug = get_option('winwar_no_bug');

	if (!$nobug) {
	// Added Lines End

		$install_date = get_option( 'myplugin_activation_date' );
		$past_date    = strtotime( '+7 days' );

		if ( $past_date >= $install_date ) {

			add_action( 'admin_notices', 'winwar_display_admin_notice' );

		}

	// Added Lines Start
	}
	// Added Lines End
}
add_action( 'admin_init', 'winwar_check_installation_date' );



/**
 * Display Admin Notice, asking for a review
 *
 * @return null
 */
function winwar_display_admin_notice() {

	// Review URL - Change to the URL of your plugin on WordPress.org
	$reviewurl = 'http://wordpress.org/';

	$nobugurl = get_admin_url() . '?winwarnobug=1';

	echo '<div class="updated"><p>';
	printf( __( "You have been using our plugin for a week now, do you like it? If so, please leave us a review with your feedback! <br /><br /> <a href='%s' target='_blank'>Leave A Review</a>/<a href='%s'>Leave Me Alone</a>" ), $reviewurl, $nobugurl );
	echo "</p></div>";
}




/**
 * Set the plugin to no longer bug users if user asks not to be.
 *
 * @return null
 */
function winwar_set_no_bug() {

	$nobug = "";

	if ( isset( $_GET['winwarnobug'] ) ) {
		$nobug = esc_attr( $_GET['winwarnobug'] );
	}

	if ( 1 == $nobug ) {

		add_option( 'winwar_no_bug', TRUE );

	}

} add_action( 'admin_init', 'winwar_set_no_bug', 5 );
