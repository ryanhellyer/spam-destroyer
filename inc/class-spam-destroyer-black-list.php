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
		add_filter( 'spam_destroyed_here', array( $this, 'naughty_comment_fields' ), 19 );
		add_filter( 'spam_destroyed_here', array( $this, 'naughty_urls' ), 20 ); // Check this last, since it takes longer to process
	}

	/**
	 * Kill any scum caught using naughty words!
	 *
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 * @since 1.8
	 * @param  $comment   string    The comment array
	 * @return string
	 */
	public function naughty_comment_fields( $comment ) {

		// Only fire this if on high or very high setting
		if ( 'high' != $this->level && 'very-high' != $this->level ) {
			return $comment;
		}

		// Grab comment blacklist - in an option so that we can leverage object caching when available
		$black_list = get_site_option( 'spam-killer-comments-blacklist' );
		if ( '' == $black_list ) {
			$black_list = file_get_contents( SPAM_DESTROYER_DIR . '/assets/comments-black-list.txt' );
			update_site_option( 'spam-killer-comments-blacklist', $black_list );
		}
		$black_list = explode( "\n", $black_list );

		// Loop through black list
		foreach( $black_list as $key => $banned_string ) {

			if ( '' != $banned_string ) {
				// Check each bad word
				if (
					strpos( $comment['comment_author']      , $banned_string ) !== false ||
					strpos( $comment['comment_author_email'], $banned_string ) !== false ||
					strpos( $comment['comment_author_url']  , $banned_string ) !== false ||
					strpos( $comment['comment_content']     , $banned_string ) !== false
				) {
					$comment['failed'][] = 'Naughty word used';
					$this->kill_spam_dead( $comment ); // Death to those who use NAUGHTY WORDS!
				}
			}
		}

		return $comment;//$comment;
	}

	/**
	 * Kill any scum caught using naughty words in their URLs!
	 * Checks both the comment author URL and all URLs found in the comment itself
	 *
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 * @since 1.8
	 * @param  $comment   string    The comment array
	 * @return string
	 */
	public function naughty_urls( $comment ) {

		$urls = $this->get_urls_from_string( $comment['comment_content'] );

		// Add author URL
		if ( isset( $comment['comment_author_url'] ) ) {
			if ( '' != $comment['comment_author_url'] ) {
				$urls[] = $comment['comment_author_url'];
			}
		}

		// Make sure we don't check the same URL twice
		$urls = array_unique( $urls );

		// Grab URL blacklist - in an option so that we can leverage object caching when available
		$black_list = get_site_option( 'spam-killer-url-blacklist' );
		if ( '' == $black_list ) {
			$black_list = file_get_contents( SPAM_DESTROYER_DIR . '/assets/url-black-list.txt' );
			update_site_option( 'spam-killer-url-blacklist', $black_list );
		}
		$black_list = explode( "\n", $black_list );

		// Loop through the URLs found in the comment
		foreach( $urls as $url ) {
			$response = wp_remote_get( $url );

			// Kill if no response code obtained
			if ( isset( $response->errors['http_request_failed'][0] ) ) {
				$comment['failed'] = 'We were unable to check one of your URLs';
				$this->kill_spam_dead( $comment ); // no checky, no letty comment
			}

			// w00t, we got a response code, so lets go check for evilness
			if ( 200 == $response['response']['code'] ) {
				$url_contents = $response['body'];

				// Loop through black list
				foreach( $black_list as $key => $banned_string ) {

					if ( '' != $banned_string ) {
						// Check each bad word
						if ( strpos( $url_contents , $banned_string ) !== false ) {
							$comment['failed'][] = 'Naughty word used';
							$this->kill_spam_dead( $comment ); // Death to those who use NAUGHTY WORDS!
						}
					}
				}
			}
		}

		return $comment;
	}

	/**
	 *
	 * Get URLs from a string
	 *
	 * Adapted from code provided by Kevin Waterson (http://www.phpro.org/examples/Get-All-URLs-From-Page.html)
	 * Adapted from code provided here http://www.the-art-of-web.com/php/parse-links/
	 *
	 * @param  string   $string   The input string
	 * @return array   The URLs found in the string
	 */
	public function get_urls_from_string( $string ) {

		// Grabbing plain URLs from page
		$regex = '/https?\:\/\/[^\" ]+/i';
		preg_match_all( $regex, $string, $urls );
		$urls = $urls[0];

		// Grabbing links (earlier code doesnt' grab malformed URLs in link tags)
		$regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
		preg_match_all( "/$regexp/siU", $string, $links );
		$links = $links[0];

		// Loop through links and add href's to URL array
		foreach( $links as $link ) {
			$link = stripslashes( $link );
			$a = new SimpleXMLElement( $link );
			$urls[] = esc_url( $a['href'] );
		}

		// Make sure we don't have double ups
		$urls = array_unique( $urls );
//Hello http://ryanhellyer.net/ there <a href="bla.com">BLA</a>, <a href="too.com">TOOT</a> and http://pooper.com/ too
		return $urls;
	}

}
new Spam_Destroyer_Black_List;
