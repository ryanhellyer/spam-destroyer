<?php
/*
Plugin Name: Spam Destroyer
Plugin URI: http://geek.ryanhellyer.net/products/spam-destroyer/
Description: Kills spam dead in it's tracks
Author: Ryan Hellyer
Version: 1.7
Author URI: http://geek.ryanhellyer.net/

Copyright (c) 2014 Ryan Hellyer


Based on the following plugins ...

Cookies for Comments by Donncha O Caoimh
http://ocaoimh.ie/cookies-for-comments/

WP Hashcash by Elliot Back
http://wordpress-plugins.feifei.us/hashcash/

Spam Catharsis by Brian Layman
http://TheCodeCave.com/plugins/spam-catharsis/

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License version 2 as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
license.txt file included with this plugin for more information.

*/



/**
 * Spam Destroyer class
 * 
 * @copyright Copyright (c), Ryan Hellyer
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.0
 */
class Spam_Destroyer {

	public $spam_key; // Key used for confirmation of bot-like behaviour
	public $speed = 5; // Will be killed as spam if posted faster than this
	public $spam_days = 5; // How many days to keep spam around
	public $first_deletion = 120; // How soon in seconds after activation should the first deletion be triggered
	public $spam_delete_limit = 200; // Maximum number of spam IDs to delete at a time
	public $spam_deletion_interval = 600; // If more than spam_delete_limit comments exist, trigger another deletion after this interval in seconds
	public $level = 'low'; // Low provides minimal protection. Medium provides significant protection. High does not exist yet :P

	/**
	 * Preparing to launch the almighty spam attack!
	 * Spam, prepare for your imminent death!
	 *
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 * @since 1.0
	 */
	public function __construct() {

		// Activation hook
		register_activation_hook( __FILE__,                 array( $this, 'reg_spam_destroyer_cleanout' ) );
		register_activation_hook( __FILE__,                 array( $this, 'schedule_single_deletion_event' ) );

		// Add filters
		add_filter( 'preprocess_comment',                   array( $this, 'check_for_comment_evilness' ) ); // Support for regular post/page comments
		add_filter( 'bbp_new_topic_pre_content',            array( $this, 'check_for_post_evilness' ), 1 ); // Support for bbPress topics
		add_filter( 'bbp_new_reply_pre_content',            array( $this, 'check_for_post_evilness' ), 1 ); // Support for bbPress replies
		add_filter( 'wpmu_validate_blog_signup',            array( $this, 'check_for_post_evilness' ) ); // Support for multisite site signups
		add_filter( 'wpmu_validate_user_signup',            array( $this, 'check_for_post_evilness' ) ); // Support for multisite user signups

		// Add to hooks
		add_action( 'init',                                 array( $this, 'set_key' ) );
		add_action( 'comment_form',                         array( $this, 'extra_input_field' ) ); // WordPress comments page
		add_action( 'signup_hidden_fields',                 array( $this, 'extra_input_field' ) ); // WordPress multi-site signup page
		add_action( 'bp_after_registration_submit_buttons', array( $this, 'extra_input_field' ) ); // BuddyPress signup page
		add_action( 'bbp_theme_before_topic_form_content',  array( $this, 'extra_input_field' ) ); // bbPress signup page
		add_action( 'bbp_theme_before_reply_form_content',  array( $this, 'extra_input_field' ) ); // bbPress signup page
		add_action( 'register_form',                        array( $this, 'extra_input_field' ) ); // bbPress user registration page
		add_action( 'spam_destroyer_cleanout',				array( $this, 'apply_cleanout' ) ); 	// Fires from registered event
		add_action( 'spam_destroyer_cleanout_single',		array( $this, 'apply_cleanout' ) ); 	// Fires from registered event

	}

	/**
	 * Set spam key
	 * Needs set at init due to using nonces
	 *
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 * @since 1.0
	 */
	public function set_key() {

		// set spam key using home_url() and new nonce as salt
		$string = home_url() . wp_create_nonce( 'spam-killer' );
		$this->spam_key = md5( $string );

	}

	/**
	 * Loading the javascript payload
	 *
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 * @since 1.0
	 */
	public function load_payload() {

		// Load the payload
		wp_enqueue_script(
			'kill_it_dead',
			plugins_url( 'kill.js',  __FILE__ ),
			'',
			'1.2',
			true
		);

		// Set the key as JS variable for use in the payload
		wp_localize_script(
			'kill_it_dead',
			'spam_destroyer',
			array(
				'key'      => $this->spam_key, 
				'lifetime' => absint( apply_filters( 'spam_destroyer_cookie_lifetime', HOUR_IN_SECONDS ) ) )
		);

	}

	/**
	 * An extra input field, which is intentionally filled with garble, but will be replaced dynamically with JS later
	 *
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 * @since 1.0
	 */
	public function extra_input_field() {

		echo '<input type="hidden" id="killer_value" name="killer_value" value="' . md5( rand( 0, 999 ) ) . '"/>';
		echo '<noscript>' . __( 'Sorry, but you are required to use a javascript enabled brower to comment here.', 'spam-killer' ) . '</noscript>';

		// Enqueue the payload - placed here so that it is ONLY used when on a page utilizing the plugin
		$this->load_payload();

	}

	/**
	 * Kachomp! Be gone evil demon spam!
	 * Checks if the user is doing something evil
	 * If they're detected as being evil, then the little bastards are killed dead in their tracks!
	 * 
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 * @since 1.0
	 * @param array $comment The comment
	 * @return array The comment
	 */
	public function check_for_comment_evilness( $comment ) {

		// If the user is logged in, then they're clearly trusted, so continue without checking
		if ( is_user_logged_in() )
			return $comment;

		$type = $comment['comment_type'];

		// Process trackbacks and pingbacks
		if ( $type == "trackback" || $type == "pingback" ) {

			// Check the website's IP against the url it's sending as a trackback, mark as spam if they don't match
			$server_ip = isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
			$web_ip = gethostbyname( parse_url( $comment['comment_author_url'], PHP_URL_HOST ) );
			if ( $server_ip != $web_ip ) {
				$this->kill_spam_dead( $comment ); // Patchooo! Website IP doesn't match server IP, therefore kill it dead as a pancake :)
			}

			// look for our link in the page itself
			if ( ! isset( $spam ) ) {
				// Work out the link we're looking for
				$permalink = get_permalink( $comment['comment_post_ID'] );
				$permalink = preg_replace( '/\/$/', '', $permalink );

				// Download the trackback/pingback
				$response = wp_remote_get( $comment['comment_author_url'] );
				if ( 200 == $response['response']['code'] ) {
					$page_body = $response['body'];
				}
				else {
					// BAM! Suck on that sploggers! Page doesn't exist, therefore kill the little bugger dead in it's tracks
					$this->kill_spam_dead( $comment );
				}

				// Look for permalink in trackback/pingback page body
				$pos = strpos( $page_body, $permalink );
				if ( $pos === false ) {
				}
				else {
					// Whammo! They didn't even mention us, so killing the blighter since it's of no interest to us anyway
					$this->kill_spam_dead( $comment );
				}
			}

		} else {

			// If they answered the CAPTCHA, then let 'em fly on through :)'
			if ( '4' == $_POST['spam-killer-captcha'] ) {
				return $comment;
			}

			// Check the hidden input field against the key
			if ( $_POST['killer_value'] != $this->spam_key ) {
				$this->kill_spam_dead( $comment ); // BOOM! Silly billy didn't have the correct input field so killing it before it reaches your eyes.
			}

			// Check for cookies presence
			if ( isset( $_COOKIE[ $this->spam_key ] ) ) {
				// If time not set correctly, then assume it's spam
				if ( $_COOKIE[$this->spam_key] > 1 && ( ( time() - $_COOKIE[$this->spam_key] ) < $this->speed ) ) {
					$this->kill_spam_dead( $comment ); // Something's up, since the commenters cookie time frame doesn't match ours
				}
			} else {
				$this->kill_spam_dead( $comment ); // Ohhhh! Cookie not set, so killing the little dick before it gets through!
			}

		}

		// YAY! It's a miracle! Something actually got listed as a legit comment :) W00P W00P!!!
		return $comment;
	}

	/**
	 * Kills splogger signups, BuddyPress posts and replies and bbPress spammers dead in their tracks
	 * This method is an alternative to pouring kerosine on sploggers and lighting a match.
	 * Checks both the cookie and input key payloads
	 *
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 * @since 1.0
	 */
	public function check_for_post_evilness( $result ) {

		// If the user is logged in, then they're clearly trusted, so continue without checking
		if ( is_user_logged_in() )
			return $comment;

		// Check the hidden input field against the key
		if ( $_POST['killer_value'] != $this->spam_key ) {
			// BAM! And the spam signup is dead :)
			if ( isset( $_POST['bbp_topic_id'] ) ) {
				bbp_add_error('bbp_reply_content', __('Sorry, but you have been detected as spam', 'spam-destroyer' ) );
			}
			else {
				$result['errors']->add( 'blogname', '' );
			}
		}

		// Check for cookies presence
		if ( isset( $_COOKIE[ $this->spam_key ] ) ) {
			// If time not set correctly, then assume it's spam
			if ( $_COOKIE[$this->spam_key] > 1 && ( ( time() - $_COOKIE[$this->spam_key] ) < $this->speed ) ) {
				// Something's up, since the commenters cookie time frame doesn't match ours
			$result['errors']->add( 'blogname', '' );
			}
		} else {
			// Cookie not set therefore destroy the evil splogger
			$result['errors']->add( 'blogname', '' );
		}
		return $result;
	}

	/**
	 * Be gone evil demon spam!
	 * Kill spam dead in it's tracks :)
	 * 
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 * @since 1.0
	 * @param array $comment The comment
	 * @return array The comment
	 */
	public function kill_spam_dead( $comment ) {

		// Set as spam
		add_filter( 'pre_comment_approved', create_function( '$a', 'return \'spam\';' ) );
		// add_filter( 'comment_post', create_function( '$id', 'wp_delete_comment( $id ); die( \'This comment has been deleted\' );' ) );
		// add_filter( 'pre_comment_approved', create_function( '$a', 'return 0;' ) );

		// Bypass Akismet since comment is spam
		if ( function_exists( 'akismet_auto_check_comment' ) ) {
			remove_filter( 'preprocess_comment', 'akismet_auto_check_comment', 10 );
		}

		$error = '
			<p>' . __( 'Please answer the following question to confirm you are a human.', 'spam-killer' ) . '</p>
			<form action="' . esc_url( site_url() ) . '/wp-comments-post.php" method="post" id="commentform" class="comment-form" novalidate>

				<p>
					<label>' . __( 'What does 1 + 3 equal?', 'spam-killer' ) . '</label>
					<input type="text" value="" name="spam-killer-captcha" />
				</p>

				<p>&nbsp;</p>

				<p class="comment-form-author">
					<label for="author">' . __( 'Name' ) . ' <span class="required">*</span></label> 
					<input id="author" name="author" type="text" value="' . esc_attr( $comment['comment_author'] ) . '" size="30" aria-required="true" />
				</p>

				<p class="comment-form-email">
					<label for="email">' . __( 'Email' ) . ' <span class="required">*</span></label>
					<input id="email" name="email" type="email" value="' . esc_attr( $comment['comment_author_email'] ) . '" size="30" aria-required="true" />
				</p>
				<p class="comment-form-url">
					<label for="url">' . __( 'Website' ) . '</label>
					<input id="url" name="url" type="url" value="' . esc_attr( $comment['comment_author_url'] ) . '" size="30" />
				</p>
				<p class="comment-form-comment">
					<label for="comment">' . __( 'Comment' ) . '</label>
					<br />
					<textarea id="comment" name="comment" cols="45" rows="8" aria-required="true">' . $comment['comment_content'] . '</textarea>
				</p>

				<p class="form-submit">
					<input name="submit" type="submit" id="submit" value="' . __( 'Submit answer' ) . '" />
					<input type="hidden" name="comment_post_ID" value="' . esc_attr( $comment['comment_post_ID'] ) . '" id="comment_post_ID" />
					<input type="hidden" name="comment_parent" id="comment_parent" value="' . esc_attr( $comment['comment_parent'] ) . '" />
				</p>
			</form>';
		wp_die( $error );
	}

	/**
	 * Spam shall not darken our doorstep for long.
	 * Put your house in order, death is on the way.
	 *
	 * @author Brian Layman <plugins@thecodecave.com>
	 * @since 1.4
	 */
	public function schedule_single_deletion_event() {

		// error_log( "schedule_single_deletion_event " );
		// Kick off an initial deletion
		if ( ! wp_next_scheduled( 'spam_destroyer_cleanout_single' ) ) {
			// error_log( "!!! Spam Destroyer initiated " );
			// error_log( "!!! Next event scheduled for now + " .  $this->first_deletion . " Seconds." );
			wp_schedule_single_event( time() + $this->first_deletion , 'spam_destroyer_cleanout_single' ); // Start deleting after one minute
		}

	}

	/**
	 * Spam shall not darken our doorstep for long.
	 * Put your house in order, death is on the way.
	 *
	 * @author Brian Layman <plugins@thecodecave.com>
	 * @since 1.4
	 */
	public function reg_spam_destroyer_cleanout() {

		// error_log( "Registering next schedule_single_deletion_event" );
		if ( !wp_next_scheduled( 'spam_destroyer_cleanout' ) ) {
			// error_log( "Daily cleanout registered" );
			wp_schedule_event( time(), 'daily', 'spam_destroyer_cleanout');
		}

	}

	/**
	 * Ask not for whom the bell tolls, 
	 * it tolls for thee, Spam... 
	 * This routine is intended to be called by the scheduled event.
	 *
	 * @author Brian Layman <plugins@thecodecave.com>
	 * @since 1.4
	 * @global $wpdb  The primary WordPress database object
	 */
	public function apply_cleanout() {

		// error_log( "Apply Cleanout Executing" );
		// This routine is originally lifted from Akismet. Adjusted to limit the initial deletes.
		global $wpdb;
		$now_gmt = current_time( 'mysql', 1 );
		$sql = "SELECT comment_id FROM $wpdb->comments WHERE DATE_SUB('$now_gmt', INTERVAL " . $this->spam_days . " DAY) > comment_date_gmt AND comment_approved = 'spam' limit 0,"  . $this->spam_delete_limit;
		// error_log( $sql );
		$comment_ids = $wpdb->get_col( $sql );
		if ( empty( $comment_ids ) )
			return;
		$comma_comment_ids = implode( ', ', array_map('intval', $comment_ids) );
		do_action( 'delete_comment', $comment_ids );
		// error_log( "Deleted comments: " . count( $comment_ids ) );
		if ( count( $comment_ids ) >= $this->spam_delete_limit ) $this->schedule_single_deletion_event();
		$wpdb->query( "DELETE FROM $wpdb->comments WHERE comment_id IN ( $comma_comment_ids )" ); // Note these have passed throught intval
		$wpdb->query( "DELETE FROM $wpdb->commentmeta WHERE comment_id IN ( $comma_comment_ids )" ); // Note these have passed throught intval
		clean_comment_cache( $comment_ids );

	}
	
}

$spam_destroyer = new Spam_Destroyer();