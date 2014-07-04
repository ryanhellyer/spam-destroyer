<?php

/**
 * Spam Destroyer Naughty Words class
 * Blocks spam by checking a black list
 * Also applies to phrases/sentences as well as single words
 * 
 * @copyright Copyright (c), Ryan Hellyer
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.8
 */
class Spam_Destroyer_Black_List extends Spam_Destroyer {

	/**
	 * Kill any scum caught using naughty words!
	 *
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 * @since 1.8
	 */
	public function __construct() {
		add_filter( 'spam_destroyer_kill_high_level', array( $this, 'naughty_string' ) );
	}

	/**
	 * Kill any scum caught using naughty words!
	 *
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 * @since 1.8
	 */
	public function naughty_string( $comment ) {

		// Grab comment blacklist - in an option so that we can leverage object caching when available
		$black_list = get_site_option( 'spam-killer-blacklist' );
		if ( '' == $black_list ) {
			$black_list = file_get_contents( SPAM_DESTROYER_DIR . '/assets/black-list.txt' );
			update_site_option( 'spam-killer-blacklist', $black_list );
		}

		// Loop through black list
		$black_list = explode( "\n", $black_list );
		foreach( $black_list as $key => $banned_string ) {

			if ( '' != $banned_string ) {
				// Check each bad word
				if (
					strpos( $comment['comment_author'], $banned_string ) !== false ||
					strpos( $comment['comment_author_email'], $banned_string ) !== false ||
					strpos( $comment['comment_author_url'], $banned_string ) !== false ||
					strpos( $comment['comment_content'], $banned_string ) !== false
				) {
					$comment['failed'][] = 'Naughty word used';
					$this->kill_spam_dead( $comment ); // Death to those who use NAUGHTY WORDS!
				}
			}
		}

		return $comment;//$comment;
	}

}
new Spam_Destroyer_Black_List;
