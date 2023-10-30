<?php
/**
 * Shared data and settings for the Spam Destroyer plugin.
 *
 * @package   Spam Destroyer
 * @copyright Copyright ©, Ryan Hellyer
 * @author    Ryan Hellyer <ryanhellyer@gmail.com>
 * @since     1.0
 */

declare(strict_types=1);

namespace SpamDestroyer;

/**
 * Configuration settings and other shared data.
 *
 * @package SpamDestroyer
 */
class Shared {
	/**
	 * The plugin version.
	 *
	 * @var string
	 */
	public const VERSION = '3.0';

	/**
	 * The plugin slug.
	 *
	 * @var string
	 */
	public const SLUG = 'spam-destroyer';

	/**
	 * Speed limit for posting to avoid being marked as spam.
	 *
	 * If a post is made faster than this speed, it will be flagged as spam.
	 * Measured in seconds.
	 *
	 * @var int
	 */
	public const SPEED = 2;

	/**
	 * The option key for storing the spam key.
	 *
	 * @var string
	 */
	private const SPAM_KEY_OPTION = 'spam-destroyer-key';

	/**
	 * The option key for storing the version number.
	 *
	 * @var string
	 */
	private const VERSION_OPTION = 'spam-destroyer-version';

	/**
	 * Get the stored spam key.
	 *
	 * @return string The spam key.
	 */
	public function get_spam_key(): string {
		return get_option( self::SPAM_KEY_OPTION );
	}

	/**
	 * Update the spam key.
	 *
	 * @param string $key The new spam key.
	 * @return bool true if successful.
	 */
	public function update_spam_key( $key ): bool {
		return update_option( self::SPAM_KEY_OPTION, $key );
	}

	/**
	 * Get the stored plugin version.
	 *
	 * @return string The stored plugin version.
	 */
	public function get_stored_plugin_version(): string {
		return get_option( self::VERSION_OPTION );
	}

	/**
	 * Update the stored plugin version.
	 *
	 * @param string $version The new plugin version.
	 * @return bool true if successful.
	 */
	public function update_stored_plugin_version( string $version ): bool {
		return update_option( self::VERSION_OPTION, $version );
	}

	/**
	 * Get the server IP address.
	 *
	 * @return string The server IP address.
	 */
	public function get_server_ip(): string {
		$http_x_forwarded_for = filter_input( INPUT_SERVER, 'HTTP_X_FORWARDED_FOR' );
		$remote_addr          = filter_input( INPUT_SERVER, 'REMOTE_ADDR' );

		$server_ip = isset( $http_x_forwarded_for ) ? $http_x_forwarded_for : $remote_addr;

		return $server_ip;
	}

	/**
	 * Get the web IP address from the comment author's URL.
	 *
	 * @param array $comment The comment data.
	 * @return string The web IP address.
	 */
	public function get_web_ip( array $comment ): string {
		return gethostbyname( wp_parse_url( $comment['comment_author_url'], PHP_URL_HOST ) );
	}

	/**
	 * Retrieve explanation of an error.
	 *
	 * @param string $error The error.
	 * @return string The explanation of the error.
	 */
	public function get_error_explanation( $error ): string {
		$possible_errors = array(
			'hidden-field-not-set'      => __( 'An input field was not set correctly.', 'spam-destroyer' ),
			'cookie-not-set-correctly'  => __( 'There was a cookie problem.', 'spam-destroyer' ),
			'website-ip-does-not-match' => __( 'The website IP was incorrect.', 'spam-destroyer' ),
			'page-does-not-exist'       => __( 'The page does not exist.', 'spam-destroyer' ),
			'page-does-not-mention-us'  => __( 'The page does not mention this website.', 'spam-destroyer' ),
			'commenting-too-quickly'    => __( 'The comment was posted too quickly.', 'spam-destroyer' ),
		);

		try {
			return $possible_errors[ $error ];
		} catch ( \Exception $e ) {
			wp_die( esc_html__( 'There was an error attempting to access an error message.', 'spam-destroyer' ) );
		}
	}
}
