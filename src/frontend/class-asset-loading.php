<?php
/**
 * Handles asset loading for the SpamDestroyer plugin.
 *
 * This file is responsible for loading necessary assets for the SpamDestroyer plugin.
 *
 * @package   SpamDestroyer\Frontend
 * @copyright Copyright (c), Ryan Hellyer
 * @author    Ryan Hellyer <ryanhellyer@gmail.com>
 * @since     1.0
 */

declare(strict_types=1);

namespace SpamDestroyer\Frontend;

/**
 * Manages asset loading for the SpamDestroyer plugin.
 *
 * This class is used for enqueuing scripts and styles required by the SpamDestroyer plugin.
 *
 * @package SpamDestroyer\Frontend
 */
class Asset_Loading {
	/**
	 * The Config class instance.
	 *
	 * @var \SpamDestroyer\Config
	 */
	private $config;

	/**
	 * Class constructor.
	 *
	 * @param \SpamDestroyer\Config $config The Config instance.
	 */
	public function __construct( \SpamDestroyer\Config $config ) {
		$this->config = $config;
	}

	/**
	 * Loading the javascript payload
	 */
	public function load_payload() {

		// Load the payload.
		wp_enqueue_script(
			'kill_it_dead',
			plugins_url( 'assets/kill.js', dirname( __DIR__ ) ),
			'',
			$this->config::VERSION,
			true
		);

		// Set the key as JS variable for use in the payload.
		wp_localize_script(
			'kill_it_dead',
			'spam_destroyer',
			array(
				'key'      => $this->config->get_spam_key(),
				'lifetime' => absint( apply_filters( 'spam_destroyer_cookie_lifetime', HOUR_IN_SECONDS ) ),
			),
		);
	}
}
