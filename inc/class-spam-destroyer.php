<?php

/**
 * Spam Destroyer class
 * 
 * @copyright Copyright (c), Ryan Hellyer
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.0
 */
class Spam_Destroyer {

	public $version = '2.1.2';                     // The plugin version number
	public $spam_key;                              // Key used for confirmation of bot-like behaviour
	public $speed = 2;                             // Will be killed as spam if posted faster than this
	public $encryption_method = 'AES-256-CBC';     // The encryption method used
	public $min_word_length;                       // Min word length (for non-dictionary random text generation)
	public $max_word_length;                       // Max word length (for non-dictionary random text generation) - Used for dictionary words indicating the word-length for font-size modification purposes
	public $captcha_time_passed = HOUR_IN_SECONDS; // Time limit on answering individual CAPTCHA questions
	public $time_limit = 300;                      // CAPTCHA must be answered in this number of seconds
	public $spam_key_option = 'spam-destroyer-key';   // The anti-spam key option key
	protected $comment_issues;                     // Reasons for comments being marked as spam

	/**
	 * Preparing to launch the almighty spam attack!
	 * Spam, prepare for your imminent death!
	 *
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 * @since 1.0
	 */
	public function __construct() {

		// Set variables
		$this->set_keys();

		// Possible comment issues
		$this->comment_issues = array(
			'hidden-field-not-set' => esc_html__( 'Hidden input field not set', 'spam-destroyer' ),
			'wrong-timestamp'      => esc_html__( 'Time not set correctly', 'spam-destroyer' ),
			'captcha-wrong'        => esc_html__( 'CAPTCHA not answered correctly', 'spam-destroyer' ),
			'cookie-not-set'       => esc_html__( 'Cookie not set', 'spam-destroyer' ),
		);

		// Add filters
		add_filter( 'preprocess_comment',                   array( $this, 'check_for_comment_evilness' ) ); // Support for regular post/page comments
		add_filter( 'bbp_new_topic_pre_content',            array( $this, 'check_for_post_evilness' ), 1 ); // Support for bbPress topics
		add_filter( 'bbp_new_reply_pre_content',            array( $this, 'check_for_post_evilness' ), 1 ); // Support for bbPress replies
		add_filter( 'wpmu_validate_blog_signup',            array( $this, 'check_for_post_evilness' ) ); // Support for multisite site signups
		add_filter( 'wpmu_validate_user_signup',            array( $this, 'check_for_post_evilness' ) ); // Support for multisite user signups

		// JetPack - under development
		add_filter( 'antispam-check',                       array( $this, 'check_for_contact_form_evilness' ) ); // WordPress comments page
		add_filter( 'antispam-fields',                      array( $this, 'get_extra_input_field' ) ); // WordPress comments page

		// Add to hooks
		add_action( 'comment_form',                         array( $this, 'extra_input_field' ) ); // WordPress comments page
		add_action( 'signup_hidden_fields',                 array( $this, 'extra_input_field' ) ); // WordPress multi-site signup page
		add_action( 'bp_after_registration_submit_buttons', array( $this, 'extra_input_field' ) ); // BuddyPress signup page
		add_action( 'bbp_theme_before_topic_form_content',  array( $this, 'extra_input_field' ) ); // bbPress signup page
		add_action( 'bbp_theme_before_reply_form_content',  array( $this, 'extra_input_field' ) ); // bbPress signup page
		add_action( 'register_form',                        array( $this, 'extra_input_field' ) ); // bbPress user registration page
		add_action( 'admin_notices',                        array( $this, 'requirements_check' ) ); // Check plugin requirements

	}

	/**
	 * Set various keys
	 *
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 * @since 1.8
	 * @access   protected
	 */
	protected function set_keys() {
		$this->spam_key = get_option( $this->spam_key_option );

		// If no key set or version number doesn't match, then generate a one
		if (
			0 !== version_compare( $this->version, get_option( 'spam-destroyer-version' ) )
			||
			'' == $this->spam_key
		) {
			$key = $this->generate_new_key();
			update_option( $this->spam_key_option, $key );
			update_option( 'spam-destroyer-version', $this->version );
		}

	}

	/**
	 * Generate a new unique key
	 *
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 * @since 1.8
	 * @access   protected
	 * @return   string   A new spam key
	 */
	protected function generate_new_key() {
		$number = home_url() . rand( 0, 999999 ); // Use home_url() to make it unique and rand() to ensure some randomness in the output
		$hash = md5( $number ); // Use MD5 to ensure a consistent type of string
		$key = 'spam-destroyer-' . $hash;
		return $key;
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
			SPAM_DESTROYER_URL . 'assets/kill.js',
			'',
			$this->version,
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

		echo $this->get_extra_input_field();

	}

	/**
	 * An extra input field, which is intentionally filled with garble, but will be replaced dynamically with JS later
	 *
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 * @since 1.0
	 * @param   string   $string   Rarely used, but useful for when needing to use as filter in another plugin, instead of a hook
	 */
	public function get_extra_input_field( $string = '' ) {

		// Enqueue the payload - placed here so that it is ONLY used when on a page utilizing the plugin
		$this->load_payload();

		$field = $string . '<input type="hidden" id="killer_value" name="killer_value" value="' . md5( rand( 0, 999 ) ) . '"/>';
		return $field;
	}

	/**
	 * Boom! No more contact form spam!
	 * 
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 * @since 1.0
	 */
	public function check_for_contact_form_evilness() {

		// If the user is logged in, then they're clearly trusted, so continue without checking
		if ( is_user_logged_in() ) {
			return;
		}

		// Check the hidden input field against the key
		if ( $_POST['killer_value'] != $this->spam_key ) {
			wp_die( 'Sorry, but you failed out anti-spam test. If you believe this to be in error, please get in touch ... https://ryan.hellyer.kiwi/contact/' );
		}

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

		if ( $type == "trackback" || $type == "pingback" ) {
			/*
			 * Process trackbacks and pingbacks
			 */

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
			/*
			 * Process comments
			 */

			// If user answers CAPTCHA, then let them sail on through
			if ( isset( $_POST['spam-destroyer-question'] ) ) {

				// Extra question and time stamp from encrypted blob
				$text = $this->decrypt( $_POST['spam-destroyer-question'] );
				$text = explode( '|||', $text );
				$question = $text[0];
				$time = $text[1];

				// Confirm question was answered recently
				$this->check_time( $time, $comment );
				$time = time() - $text[1]; // Number of seconds since CAPTCHA was generated
				if ( $this->time_limit < $time ) {
					$this->comment_issue = 'wrong-timestamp';
					$this->kill_spam_dead( $comment ); // TOO SLOW! CAPTCHA wasn't answered within the alotted time and so they'll need to retry
				}

				// Did they answer it correctly?
				$answer = $_POST['spam-destroyer-captcha'];
				if ( $question == $answer && '' != $question ) {
					return $comment; // w00p w00p! The CAPTCHA was answered correctly :)
				} else {
					$this->comment_issue = 'captcha-wrong';
					$this->kill_spam_dead( $comment ); // SPLAT! Spam is killed, since it can't even answer a simple CAPTCHA!
				}
			}

			// Check the hidden input field against the key
			if ( $_POST['killer_value'] != $this->spam_key ) {
				$this->comment_issue = 'hidden-field-not-set';
				$this->kill_spam_dead( $comment ); // BOOM! Silly billy didn't have the correct input field so killing it before it reaches your eyes.
			}

			// Check for cookies presence
			if ( isset( $_COOKIE[ $this->spam_key ] ) ) {

				// If time not set correctly, then assume it's spam
				if ( $_COOKIE[$this->spam_key] > 1 && ( ( time() - $_COOKIE[$this->spam_key] ) < $this->speed ) ) {
					$this->comment_issue = 'wrong-timestamp';
					$this->kill_spam_dead( $comment ); // Something's up, since the commenters cookie time frame doesn't match ours
				}

			} else {
				$this->comment_issue = 'cookie-not-set';
				$this->kill_spam_dead( $comment ); // Ohhhh! Cookie not set, so killing the little dick before it gets through!
			}

			// Add extra protection here
			$comment = apply_filters( 'spam_destroyed_here', $comment );

		}

		// YAY! It's a miracle! Something actually got listed as a legit comment :) W00P W00P!!!
		return $comment;
	}

	/*
	 * Check how much time has passed since question was asked
	 * Squash the spammers like a pancake if the question is too old
	 * (this should force them answer another CAPTCHA)
	 *
	 * @param   string   $time
	 */
	public function check_time( $time, $comment ) {
		// Kill it dead if too much time has passed
		$time_passed = time() - $time;
		if ( $this->captcha_time_passed < $time_passed ) {
			$this->kill_spam_dead( $comment );
		}
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
		if ( is_user_logged_in() ) {
			return $result;
		}

		// Check the hidden input field against the key
		if ( $_POST['killer_value'] != $this->spam_key ) {

			// BAM! And the spam signup is dead :)
			if ( isset( $_POST['bbp_topic_id'] ) ) {
				bbp_add_error( 'bbp_reply_content', esc_html__( 'Sorry, but you have been detected as spam', 'spam-destroyer' ) );
			} else {

				if ( isset( $result['errors'] ) ) {
					$result['errors']->add( 'blogname', '' );
				}

			}
		}

		// Check for cookies presence
		if ( isset( $_COOKIE[ $this->spam_key ] ) ) {

			// If time not set correctly, then assume it's spam
			if ( $_COOKIE[$this->spam_key] > 1 && ( ( time() - $_COOKIE[$this->spam_key] ) < $this->speed ) ) {

				// Something's up, since the commenters cookie time frame doesn't match ours
				if ( isset( $result['errors'] ) ) {
					$result['errors']->add( 'blogname', '' );
				}

			}
		} else {

			// Cookie not set therefore destroy the evil splogger
			if ( isset( $result['errors'] ) ) {
				$result['errors']->add( 'blogname', '' );
			}

		}
		return $result;
	}

	/**
	 * Decrypt.
	 *
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 * @since 1.7
	 * @param    string   $text   Text to decrypt
	 * @return   string   Decrypted text
	 */
	public function decrypt( $text ) {
		$text = base64_decode( $text );

		if ( function_exists( 'openssl_decrypt' ) ) {
			$text = openssl_decrypt(
				$text,                    // The text to be decrypted
				$this->encryption_method, // The cipher method
				$this->spam_key,          // The password
				0,                        // Options - leave at 0
				'ik3m3mfmenektn37'        // Initialization vector
			);
		}

		return $text;
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

		// Adding hook for tracking killed spams
		do_action( 'spam_destroyer_death' );

		// Don't provide CAPTCHA if GD not enabled
		$test_gd = get_extension_funcs( 'gd' );
		if ( ! $test_gd ) {
			wp_die( esc_html__(  'Sorry, but your comment was rejected as it was detected as spam.', 'spam-destroyer' ) );
		}

		/*
		 * Let's give them one less chance to prove they're human :)
		 * This is necessary to allow JavaScript or Cookie'less users to comment.
		 */
		require( SPAM_DESTROYER_DIR . '/inc/class-spam-destroyer-captcha-question.php' );
		$captcha = new Spam_Destroyer_CAPTCHA_Question();
		$question = $captcha->get_encrypted_question();

		$error = '';
		$error .= '
		<form action="' . esc_url( site_url() ) . '/wp-comments-post.php" method="post" id="commentform" class="comment-form" novalidate>
			<p>' . esc_html__( 'Please confirm you are human by typing the words in the box below.', 'spam-destroyer' ) . '</p>
				' . $this->get_captcha_image( $question )
				. $this->get_extra_input_field() . '

				<div style="display:none">
					<input id="author" name="author" type="hidden" value="' . esc_attr( $comment['comment_author'] ) . '" />
					<input id="email" name="email" type="hidden" value="' . esc_attr( $comment['comment_author_email'] ) . '" />
					<input id="url" name="url" type="hidden" value="' . esc_attr( $comment['comment_author_url'] ) . '" />
					<textarea id="comment" name="comment" cols="45" rows="8" aria-required="true">' . esc_textarea( $comment['comment_content'] ) . '</textarea>
				</div>

				<p class="form-submit">
					<input type="submit" name="submit" class="button button-primary" id="submit" value="' . esc_attr( esc_html__( 'Submit answer' ) ) . '" />
					<input type="hidden" name="comment_post_ID" value="' . esc_attr( $comment['comment_post_ID'] ) . '" id="comment_post_ID" />
					<input type="hidden" name="comment_parent" id="comment_parent" value="' . esc_attr( $comment['comment_parent'] ) . '" />
				</p>';

		// Display reason for comment being caught as spam - hidden field is used on backend to store comment meta for later analysis
		if ( isset( $this->comment_issue ) || isset( $_POST['failed'] ) ) {

			// Grab issue
			if ( isset( $this->comment_issue ) ) {
				$issue_raw = $this->comment_issue; // The raw issue as provided by comment form
			} else {
				$issue_raw = 'captcha-wrong'; // They failed the CAPTCHA, so we'll change the issue
			}

			if ( isset( $this->comment_issues[$issue_raw] ) ) {
				$issue_human_readable = $this->comment_issues[$issue_raw]; // Convert to human readable format

				$error .= '<p><a href="#" onclick="alert(\'' . esc_html__( 'Your comment was detected as potential spam because', 'spam-destroyer' ) . ' ' . esc_html( $issue_human_readable ) . '\');">' . esc_html__( 'Why do I need to answer this?', 'spam-destroyer' ) . '</a></p>';
				$error .= '<input id="failed" name="failed" type="hidden" value="' . esc_attr( $issue_raw ) . '" />';
				$error .= '<input id="comment_karma" name="comment_karma" type="hidden" value="' . esc_attr( $issue_raw ) . '" />';
			}
		}

		$error .= '
		</form>';

		wp_die( $error );
	}

	/**
	 * Spam shall not darken our doorstep for long.
	 * Put your house in order, death is on the way.
	 *
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 * @since 1.7
	 * @param string  $question   The CAPTCHA question
	 */
	public function get_captcha_image( $question ) {
		return '
		<input type="hidden" name="spam-destroyer-question" value="' . esc_attr( $question ) . '" />
		<img src="' . esc_url( home_url( '?captcha=' . $question ) ) . '" alt="" />
		<p>
			<input type="text" value="" name="spam-destroyer-captcha" />
		</p>';
	}

	/**
	 * Alerts the admin if their site does not meet the plugins requirements.
	 *
	 * @author Ryan Hellyer <ryanhehellyer@gmail.com>
	 * @since 2.1
	 */
	public function requirements_check() {

		// This does not include a nonce check, but it's only for hiding a notice so not very important anyway
		if (
			isset( $_GET['remove_spam_destroyer_gd_notice'] )
			&&
			current_user_can( 'manage_options' )
		) {
			update_option( 'spam-destroyer-gd-notice-removed', '1' );
		}

		// Only display an error message if the notice has been previously hidden and GD not enable
		$test_gd = get_extension_funcs( 'gd' );
		if (
			'1' !== get_option( 'spam-destroyer-gd-notice-removed' )
			&&
			! $test_gd
		) {
			echo '
			<div class="notice notice-error" style="position:relative">
				<p>
					' . esc_html__( 'The Spam Destroyer plugin requires the GD library for when comments are detected as spam. When commenters are detected as spam, they are served a CAPTCHA as a double check to confirm they are not spammers. Due to the GD library not being detected on your site, the fallback CAPTCHA system has been disabled.', 'spam-destroyer' ) . '
				</p>
				<a href="' . esc_url( admin_url( '?remove_spam_destroyer_gd_notice' ) ) . '" class="notice-dismiss"><span class="screen-reader-text">
					' . esc_html__( 'Dismiss this notice.', 'spam-destroyer' ) . '
				</span></a>
			</div>';
		}

	}

}
$spam_destroyer = new Spam_Destroyer();
