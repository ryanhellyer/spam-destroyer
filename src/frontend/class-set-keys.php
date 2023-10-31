<?php
/**
 * Handles the key setting functionality for the Spam Destroyer plugin.
 * Keys need to be set in advance and stored, so that the spam blocking
 * system can detect when spammers don't implement the keys correctly.
 *
 * @package   Spam Destroyer
 * @copyright Copyright © Ryan Hellyer
 * @author    Ryan Hellyer <ryanhellyer@gmail.com>
 * @since     1.0
 */

declare(strict_types=1);

namespace SpamDestroyer\Frontend;

use SpamDestroyer\Shared;

/**
 * Set_Keys Class
 *
 * This class is responsible for setting and generating keys for spam protection.
 */
class Set_Keys {
	/**
	 * The Config class instance.
	 *
	 * @var Shared
	 */
	private $shared;

	/**
	 * Class constructor.
	 *
	 * @param Shared $shared The Config instance.
	 */
	public function __construct( Shared $shared ) {
		$this->shared = $shared;
	}

	/**
	 * Initializing the class.
	 */
	public function init() {
		$this->set_keys();
	}

	/**
	 * Set various keys
	 */
	private function set_keys() {
		$is_version_mismatch = version_compare( $this->shared::VERSION, $this->shared->get_stored_plugin_version() ) !== 0;
		$is_key_empty        = empty( $this->shared->get_spam_key() );

		// Generate and store new spam keys if version doesn't match or key is empty.
		if ( $is_version_mismatch || $is_key_empty ) {
			$key = $this->generate_new_key();
			$this->shared->update_spam_key( $key );
			$this->shared->update_stored_plugin_version( $this->shared::VERSION );
		}
	}

	/**
	 * Generate a new unique key
	 *
	 * @return string A new spam key.
	 */
	private function generate_new_key(): string {
		$hash = md5( uniqid() ); // Use MD5 to ensure a consistent type of string .
		$key  = 'spam-destroyer-' . $hash;

		return $key;
	}
}
