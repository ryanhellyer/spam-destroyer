<?php

/**
 * Lets track our spam :)
 * 
 * @copyright Copyright (c), Ryan Hellyer
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.0
 */
class Spam_Destroyer_Stats {

	/**
	 * Frequency of stats updating.
	 * We don't want to update the stats on every spam received as this will cause.
	 * Spam is only stored at a rate determined by $frequency. This number is
	 * increased/reduced over time based on the rate of spam detected.
	 *
	 * This is set to private as this will be set to automatically modify itself in future
	 * and so if people mess with it, that would cause problems for the automation process.
	 *
	 * @var int
	 * @access private
	 */
	private $frequency;

	/**
	 * Class constructor.
	 */
	public function __construct() {

		// Add hooks at plugin activation
		register_activation_hook( SPAM_DESTROYER_DIR . '/index.php', array( $this, 'set_default_frequency' ) );
		register_activation_hook( SPAM_DESTROYER_DIR . '/index.php', array( $this, 'activate_cron' ) );
		register_deactivation_hook( SPAM_DESTROYER_DIR . '/index.php', array( $this, 'deactivate_cron' ) );

		// Iterate stats when one of the evil blighters is caught
		add_action( 'spam_destroyer_death', array( $this, 'iterate_stats' ) );

	}

	/**
	 * On deactivation, remove all functions from the scheduled action hook.
	 */
	public function deactivate_cron() {
		wp_clear_scheduled_hook( 'spam_destroyer_modify_frequency' );
	}

	/**
	 * Set frequency on plugin activation
	 */
	public function set_default_frequency() {
		add_option( 'spam-killer-frequency-stats', 2 );
		add_option( 'spam-killer-historical-stats', array(), '', 'no' ); // We do not set this to auto-load since it is only needed occasionally
	}

	/**
	 * Set frequency via a Cron.
	 * This is used to modify the frequency/rate at which spam statistics are tracked.
	 */
	public function activate_cron() {
		wp_schedule_event( time(), 'daily', 'spam_destroyer_modify_frequency' ); // If changed from 'daily then also change "DAY_IN_SECONDS" constant
	}

	/**
	 * Run Cron.
	 * Stashes the historical data.
	 * Sets the frequency to update spam stats (because we don't want to hammer the server too hard on sites with huge amounts of spam)
	 */
	public function modify_frequency() {

		// Stashing historical data
		$history = get_option( 'spam-killer-historical-stats' );
		$days_since_epoch = absint( time() / DAY_IN_SECONDS ); // The number of days since epoch
		$history[$days_since_epoch] = get_option( 'spam-killer-recent-stats' );

		// Modifying frequency based on recent history
		$frequency = ( $history[$days_since_epoch] / ( 6 * 24 ) ); // Attempt to record a spam hit once per 10 minute block (estimated)
		$frequency = absint( $frequency ); // Need to make sure it's a round number
		if ( 0 == $frequency ) {
			$frequency = 1; // 1 is the minimum
		}

		update_option( 'spam-killer-frequency-stats', $frequency );

	}

	/**
	 * Iterate stats
	 */
	public function iterate_stats() {

		// Get current set frequency
		$frequency = get_option( 'spam-killer-frequency-stats' );
		$frequency = absint( $frequency );

		// Only iterate occasionally, based on the set frequency
		if ( 1 == rand( 1, $frequency ) ) {
			$stats = get_option( 'spam-killer-recent-stats' );
			$stats = absint( $stats );
			$stats = $stats + $frequency; // We need to iterate by more than 1, since we are only sporadically storing stats information
			update_option( 'spam-killer-recent-stats', $stats );
		}

	}

}
new Spam_Destroyer_Stats;
