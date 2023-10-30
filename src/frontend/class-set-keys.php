<?php
/**
 * Handles the key setting functionality for the Spam Destroyer plugin.
 *
 * @package   SpamDestroyer\Frontend
 * @copyright Copyright (c) Ryan Hellyer
 * @author    Ryan Hellyer <ryanhellyer@gmail.com>
 * @since     1.0
 */

declare(strict_types=1);

namespace SpamDestroyer\Frontend;

/**
 * Set_Keys Class
 *
 * This class is responsible for setting and generating keys for spam protection.
 */
class Set_Keys {
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
	 * Preparing to launch the almighty spam attack!
	 * Spam, prepare for your imminent death!
	 */
	public function init() {
		$this->set_keys();
	}

	/**
	 * Set various keys
	 */
	private function set_keys() {

		// If no key set or version number doesn't match, then generate and store new spam keys.
		if (
			0 !== version_compare( $this->config::VERSION, $this->config->get_stored_plugin_version() )
			||
			'' === $this->config->get_spam_key()
		) {
			$key = $this->generate_new_key();
			$this->config->update_spam_key( $key );
			$this->config->update_stored_plugin_version( $this->config::VERSION );
		}
	}

	/**
	 * Generate a new unique key
	 *
	 * @return string A new spam key.
	 */
	private function generate_new_key(): string {
		$hash = md5( bin2hex( random_bytes( 16 ) ) ); // Use MD5 to ensure a consistent type of string.
		$key  = 'spam-destroyer-' . $hash;

		return $key;
	}
}
