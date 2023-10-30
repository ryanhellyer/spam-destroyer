<?php
/**
 * Spam Destroyer Frontend Catcher
 *
 * Contains the Catcher class which handles spam prevention logic.
 *
 * @package   Spam Destroyer
 * @copyright Copyright (c), Ryan Hellyer
 * @author    Ryan Hellyer <ryanhellyer@gmail.com>
 * @since     1.0
 */

declare(strict_types=1);

namespace SpamDestroyer\Frontend;

/**
 * Catcher Class
 *
 * This class contains the logic for preventing spam.
 */
class Catcher {
	/**
	 * The Config class instance.
	 *
	 * @var \SpamDestroyer\Config
	 */
	private $config;

	/**
	 * The Spam Checking class instance.
	 *
	 * @var \SpamDestroyer\Frontend\Spam_Checking
	 */
	private $spam_checking;

	/**
	 * The Asset Loading class instance.
	 *
	 * @var \SpamDestroyer\Frontend\Asset_Loading
	 */
	private $asset_loading;

	/**
	 * Class constructor.
	 *
	 * @param \SpamDestroyer\Config                 $config The Config instance.
	 * @param \SpamDestroyer\Frontend\Spam_Checking $spam_checking The Spam Checking instance.
	 * @param \SpamDestroyer\Frontend\Asset_Loading $asset_loading The Asset Loading instance.
	 */
	public function __construct(
		\SpamDestroyer\Config $config,
		\SpamDestroyer\Frontend\Spam_Checking $spam_checking,
		\SpamDestroyer\Frontend\Asset_Loading $asset_loading
	) {
		$this->config        = $config;
		$this->spam_checking = $spam_checking;
		$this->asset_loading = $asset_loading;
	}

	/**
	 * Preparing to launch the almighty spam attack!
	 * Spam, prepare for your imminent death!
	 */
	public function init() {
		add_action( 'init', array( $this, 'add_hooks_and_filters' ) );
	}

	/**
	 * Adds hooks and filters.
	 *
	 * If the user is logged in, they are trusted and no checks are made.
	 * Otherwise, various filters and actions are added to combat spam.
	 */
	public function add_hooks_and_filters() {

		// If the user is logged in, then they're clearly trusted, so continue without checking.
		if ( is_user_logged_in() ) {
			return;
		}

		add_filter( 'preprocess_comment', array( $this->spam_checking, 'check_for_comment_evilness' ) ); // Support for regular post/page comments.
		add_action( 'comment_form', array( $this, 'extra_input_field' ) ); // WordPress comments page.
	}

	/**
	 * Display the extra input field on the page.
	 */
	public function extra_input_field() {
		$field = $this->get_extra_input_field();

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $field; // Doesn't need to be escaped as escaping is handled within get_extra_input_field().
	}

	/**
	 * An extra input field, which is intentionally filled with garble, but will be replaced dynamically with JS later
	 *
	 * @param string $html Rarely used, but useful for when needing to use as filter in another plugin, instead of a hook.
	 */
	private function get_extra_input_field( string $html = '' ): string {

		// Enqueue the payload - placed here so that it is ONLY used when on a page utilizing the plugin.
		$this->asset_loading->load_payload();

		$random_string = md5( uniqid() );

		$field = $html . '<input type="hidden" id="killer_value" name="killer_value" value="' . esc_attr( $random_string ) . '"/>';

		return $field;
	}
}
