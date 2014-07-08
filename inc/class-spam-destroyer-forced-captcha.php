<?php

/**
 * Spam Destroyer Force CAPTCHA class
 * Forces the user to answer a CAPTCHA when on very high level
 *
 * Manual code is set in non_js_captcha() to allow this CAPTCHA to be displayed within the 
 * comment form in themes which utilize the appropriate hooks
 * 
 * @copyright Copyright (c), Ryan Hellyer
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.8
 */
class Spam_Destroyer_Forced_CAPTCHA extends Spam_Destroyer {

	/**
	 * Add filter
	 *
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 * @since 1.8
	 */
	public function __construct() {
		add_action( 'init',                array( $this, 'set_key' ), 1 ); // Needed for decrypting the question
		add_filter( 'spam_destroyed_here', array( $this, 'force_captcha' ) );
	}

	/**
	 * Force check on CAPTCHA
	 *
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 * @since 1.8
	 */
	public function force_captcha( $comment ) {

		// Only fire this if on very high setting
		if ( 'very-high' != $this->level ) {
			return $comment;
		}

		// Grab question
		$text = $this->decrypt( $_POST['spam-killer-question'] );

		$text = explode( '|||', $text );
		$question = $text[0];
		$time = $text[1];

		// Confirm question was answered recently
		$this->check_time( $time, $comment );

		$answer = $_POST['spam-killer-captcha'];
		if ( $question != $answer || '' == $question ) {
			$this->comment_issues[] = 'CAPTCHA not answered correctly on very high level';
			$this->kill_spam_dead( $comment ); // Ohhhh! Cookie not set, so killing the little dick before it gets through!
		}

		return $comment;//$comment;
	}

}
new Spam_Destroyer_Forced_CAPTCHA;
