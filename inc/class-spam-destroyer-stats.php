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
	private $frequency = 100;

	/**
	 * Class constructor.
	 */
	public function __construct() {

		// Iterate stats when one of the evil blighters is caught
		add_action( 'spam_destroyer_death', array( $this, 'iterate_stats' ) );

	}

	/**
	 * Iterate stats
	 */
	public function iterate_stats() {

		// Only iterate occasionally, based on the set frequency
		if ( 1 == rand( 1, $this->frequency ) ) {
			$stats = get_option( 'spam-destroyer-stats' ); // Get existing stats
			$months_since_epoch = absint( time() / MONTH_IN_SECONDS ); // The number of days since epoch
			$stats[$months_since_epoch] = absint( $stats[$months_since_epoch] );
			$stats[$months_since_epoch] = $stats[$months_since_epoch] + $this->frequency; // We need to iterate by more than 1, since we are only sporadically storing stats information
			update_option( 'spam-destroyer-stats', $stats );
		}

	}

}
new Spam_Destroyer_Stats;
