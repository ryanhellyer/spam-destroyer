<?php
/**
 * Spam_Checking Class File
 *
 * This file contains the Spam_Checking class, responsible for handling
 * spam-related checks on comments, trackbacks, and pingbacks.
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
 * Spam_Checking Class
 *
 * This class is responsible for carrying out various checks to
 * determine if a comment, trackback, or pingback is spam.
 */
class Spam_Checking {
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
	 * Checks if this is a spam comment.
	 * If it is detected as a spam comment, then it is killed dead in it's tracks.
	 *
	 * @param array $comment The comment.
	 * @return array The comment.
	 */
	public function filter_spam_comments( array $comment ): array {

		try {
			$type = $comment['comment_type'];
			if ( 'trackback' === $type || 'pingback' === $type ) {
				$this->validate_trackbacks_and_pingbacks( $comment );
			} else {
				$this->validate_comment( $comment );
			}
		} catch ( \Exception $e ) {
			$this->kill_spam_dead( $e->getMessage() );
		}

		// YAY! It's a miracle! Something actually got listed as a legit comment :) W00P W00P!!!.
		return $comment;
	}

	/**
	 * Be gone evil demon spam!
	 * This is the primary method used when a spam comment needs to be killed.
	 *
	 * @param string $error The error.
	 */
	public function kill_spam_dead( string $error ) {

		// Adding hook for tracking killed spams.
		do_action( 'spam_destroyer_death' );

		$issue = $this->shared->get_error_explanation( $error );

		wp_die( esc_html__( 'Sorry, but your comment was rejected as it was detected as spam.', 'spam-destroyer' ) . '<br><br>' . esc_html( $issue ) );
	}

	/**
	 * Checking trackbacks and pingbacks for spam.
	 *
	 * @param array $comment The comment.
	 * @return array The comment.
	 * @throws \Exception If a comment is detected as spam.
	 */
	private function validate_trackbacks_and_pingbacks( array $comment ): array {

		// Check the website's IP against the url it's sending as a trackback, mark as spam if they don't match.
		if ( $this->shared->get_server_ip() !== $this->shared->get_web_ip( $comment ) ) {
			throw new \Exception( 'website-ip-does-not-match' );
		}

		// Get the permalink we need to look up.
		$permalink = get_permalink( $comment['comment_post_ID'] );
		$permalink = preg_replace( '/\/$/', '', $permalink );

		// Download the trackback/pingback.
		$response = wp_remote_get( $comment['comment_author_url'] );
		// @todo use try/catch here.
		if ( isset( $response['response']['code'] ) && 200 === $response['response']['code'] ) {
			$page_body = $response['body'];
		} else {
			throw new \Exception( 'page-does-not-exist' );
		}

		// Look for permalink in trackback/pingback page body.
		$pos = strpos( $page_body, $permalink );
		if ( false !== $pos ) {
			// They didn't even mention us in the page, so we kill it.
			throw new \Exception( 'page-does-not-mention-us' );
		}

		return $comment;
	}

	/**
	 * Check a comment.
	 *
	 * @param array $comment The comment to check.
	 * @return array The checked comment.
	 * @throws \Exception If a comment is detected as spam.
	 */
	private function validate_comment( array $comment ): array {
		$killer_value      = filter_input( INPUT_POST, 'killer_value' );
		$cookie_time_stamp = filter_input( INPUT_COOKIE, $this->shared->get_spam_key() );

		// Check the hidden input field against the key.
		if ( $killer_value !== $this->shared->get_spam_key() ) {
			throw new \Exception( 'hidden-field-not-set' );
		}

		// Check if cookie is set at all.
		if ( ! is_numeric( $cookie_time_stamp ) ) {
			throw new \Exception( 'cookie-not-set-correctly' );
		}

		// Check if the commenter posted within a reasonable time frame.
		$time_delay = time() - $cookie_time_stamp;
		if ( $time_delay < $this->shared::SPEED ) {
			throw new \Exception( 'commenting-too-quickly' );
		}

		return $comment;
	}
}
