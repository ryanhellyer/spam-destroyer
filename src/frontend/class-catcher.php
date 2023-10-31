<?php
/**
 * Spam Destroyer Frontend Catcher
 *
 * Contains the Catcher class which handles spam prevention logic.
 *
 * @package   Spam Destroyer
 * @copyright Copyright ©, Ryan Hellyer
 * @author    Ryan Hellyer <ryanhellyer@gmail.com>
 * @since     1.0
 */

declare(strict_types=1);

namespace SpamDestroyer\Frontend;

use SpamDestroyer\Frontend\Spam_Checking;
use SpamDestroyer\Frontend\Asset_Loading;

/**
 * Catcher Class
 *
 * This class contains the logic for preventing spam.
 */
class Catcher {

	/**
	 * The Spam Checking class instance.
	 * Responsible for the logic that identifies and filters spam content.
	 *
	 * @var Spam_Checking
	 */
	private $spam_checking;

	/**
	 * The Asset Loading class instance.
	 * Manages the loading of JavaScript and CSS files needed by the plugin.
	 *
	 * @var Asset_Loading
	 */
	private $asset_loading;

	/**
	 * Class constructor.
	 *
	 * @param Spam_Checking $spam_checking The Spam Checking instance.
	 * @param Asset_Loading $asset_loading The Asset Loading instance.
	 */
	public function __construct(
		Spam_Checking $spam_checking,
		Asset_Loading $asset_loading
	) {
		$this->spam_checking = $spam_checking;
		$this->asset_loading = $asset_loading;
	}

	/**
	 * Initializes the spam prevention measures.
	 * Registers the register_spam_prevention_hooks_and_filters method to the WordPress 'init' action hook.
	 */
	public function init() {
		add_action( 'init', array( $this, 'register_spam_prevention_hooks_and_filters' ) );
	}

	/**
	 * Registers spam prevention hooks and filters.
	 *
	 * Adds actions and filters for handling comment spam, unless the user is logged in.
	 * Logged-in users bypass spam checks.
	 */
	public function register_spam_prevention_hooks_and_filters() {

		// If the user is logged in, then they're clearly trusted, so continue without checking.
		if ( is_user_logged_in() ) {
			return;
		}

		add_filter( 'preprocess_comment', array( $this->spam_checking, 'filter_spam_comments' ) ); // Support for regular post/page comments.
		add_action( 'comment_form', array( $this, 'display_extra_input_field' ) ); // WordPress comments page.
	}

	/**
	 * Display the extra input field on the page.
	 */
	public function display_extra_input_field() {
		$field = $this->get_extra_input_field();

		$allowed_html = array(
			'input' => array(
				'type'  => array(),
				'id'    => array(),
				'name'  => array(),
				'value' => array(),
			),
		);
		echo wp_kses( $field, $allowed_html );
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
