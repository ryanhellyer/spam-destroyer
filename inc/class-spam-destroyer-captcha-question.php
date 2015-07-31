<?php

/**
 * Create questions for CAPTCHA.
 *
 * Derived from "Script para la generación de CAPTCHAS" by Jose Rodrigueze - http://code.google.com/p/cool-php-captcha
 *
 * @author  Jose Rodriguez <jose.rodriguez@exec.cl>
 * @author  Ryan Hellyer <ryanhellyer@gmail.com>
 * @license GPLv3
 */
class Spam_Destroyer_CAPTCHA_Question extends Spam_Destroyer {

	public $key; // The cache key
	public $spam_key; // The spam protection key

	/**
	 * Class constructor.
	 *
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 * @since 1.8
	 */
	public function __construct() {
		$this->set_keys(); // Set variables

		$this->spam_key = get_option( $this->spam_key_option );
	}

	/**
	 * Get the encrypted text.
	 *
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 * @since 1.7
	 * @return   string   Encrypted text
	 */
	public function get_encrypted_question() {

		// Get a question
		$text = $this->get_question();

		// Add the time to the text
		$text .= '|||'.time();

		// Return encrypted text string
		return $this->encrypt( $text );
	}

	/**
	 * Encrypt.
	 *
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 * @since 1.7
	 * @param    string   $text   Text to encrypt
	 * @return   string   Encrypted text
	 */
	public function encrypt( $text ) {
		if ( function_exists( 'openssl_encrypt' ) ) {
			$text = openssl_encrypt( $text, $this->encryption_method, $this->spam_key, 0, 'ik3m3mfmenektn37' );
		}
		$text = base64_encode( $text );
		return $text;
	}

	/**
	 * Text generation.
	 *
	 * @return string Text
	 */
	protected function get_question() {
		$text = $this->get_dictionary_captcha_text();
		if ( ! $text ) {
			$text = $this->get_random_captcha_text();
		}
		return $text;
	}

	/**
	 * Random text generation.
	 *
	 * @return string Text
	 */
	protected function get_random_captcha_text( $length = null ) {
		if ( empty( $length ) ) {
			$length = rand( $this->min_word_length, $this->max_word_length );
		}

		$words  = "abcdefghijlmnopqrstvwyz";
		$vocals = "aeiou";

		$text  = '';
		$vocal = rand( 0, 1 );
		for ( $i = 0; $i < $length; $i++ ) {
			if ( $vocal ) {
				$text .= substr( $vocals, mt_rand( 0, 4 ), 1 );
			} else {
				$text .= substr( $words, mt_rand( 0, 22 ), 1 );
			}
			$vocal = !$vocal;
		}
		return $text;
	}

	/**
	 * Random dictionary word generation.
	 *
	 * @param boolean $extended Add extended "fake" words
	 * @return string Word
	 */
	function get_dictionary_captcha_text( $extended = false ) {
		$words_file = apply_filters( 'spam_destroyer_word_file', SPAM_DESTROYER_DIR . '/assets/words.txt' );

		$fp     = fopen( $words_file, 'r' );
		$length = strlen( fgets( $fp ) );
		if ( ! $length ) {
			return false;
		}

		$line   = rand( 1, ( filesize( $words_file ) / $length ) - 2 );
		if ( fseek( $fp, $length * $line ) == -1 ) {
			return false;
		}
		$text = trim( fgets( $fp ) );
		fclose( $fp );

		// Change ramdom vowels
		if ( $extended ) {
			$text   = preg_split( '//', $text, -1, PREG_SPLIT_NO_EMPTY );
			$vocals = array( 'a', 'e', 'i', 'o', 'u' );
			foreach ( $text as $i => $char ) {
				if ( mt_rand( 0, 1 ) && in_array( $char, $vocals ) ) {
					$text[$i] = $vocals[mt_rand( 0, 4 )];
				}
			}
			$text = implode( '', $text );
		}

		return $text;
	}

}
