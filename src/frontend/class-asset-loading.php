<?php
/**
 * Handles asset loading for the Spam Destroyer plugin.
 *
 * This file is responsible for loading necessary assets for the Spam Destroyer plugin.
 *
 * @package   Spam Destroyer
 * @copyright Copyright ©, Ryan Hellyer
 * @author    Ryan Hellyer <ryanhellyer@gmail.com>
 * @since     1.0
 */

declare(strict_types=1);

namespace SpamDestroyer\Frontend;

use SpamDestroyer\Shared;

/**
 * Manages asset loading for the Spam Destroyer plugin.
 *
 * This class is used for enqueuing scripts and styles required by the Spam Destroyer plugin.
 *
 * @package Spam Destroyer
 */
class Asset_Loading {
	/**
	 * The Shared class instance.
	 *
	 * @var Shared
	 */
	private $shared;

	/**
	 * Class constructor.
	 *
	 * @param Shared $shared The Shared instance.
	 */
	public function __construct( Shared $shared ) {
		$this->shared = $shared;
	}

	/**
	 * Loading the javascript payload.
	 * Adds the kill.js script which changes a hidden input field
	 * in the page with correct data from the kill_it_dead var.
	 *
	 * Adds a spam_destroyer_cookie_lifetime filter, which can be used
	 * for modifying the cookie expiry time.
	 */
	public function load_payload() {

		// Load the payload.
		wp_enqueue_script(
			'kill_it_dead',
			plugins_url( 'assets/kill-spam.js', dirname( __DIR__ ) ),
			'',
			$this->shared::VERSION,
			true
		);

		// Set the key as JS variable for use in the payload.
		wp_localize_script(
			'kill_it_dead',
			'spam_destroyer',
			array(
				'key'      => $this->shared->get_spam_key(),
				'lifetime' => absint( apply_filters( 'spam_destroyer_cookie_lifetime', HOUR_IN_SECONDS ) ),
			),
		);
	}
}
