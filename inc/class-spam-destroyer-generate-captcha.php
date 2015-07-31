<?php

/**
 * Generate a CAPTCHA image
 *
 * Derived from "Script para la generación de CAPTCHAS" by Jose Rodrigueze - http://code.google.com/p/cool-php-captcha
 *
 * @author  Jose Rodriguez <jose.rodriguez@exec.cl>
 * @author  Ryan Hellyer <ryanhellyer@gmail.com>
 * @license GPLv3
 */
class Spam_Destroyer_Generate_CAPTCHA extends Spam_Destroyer {

	public $width  = 200; // Width of the image
	public $height = 70; // Height of the image
	public $background_color = array( 255, 255, 255 ); // Background color in RGB-array
	public $colors = array(
		array( 27, 78, 181 ), // blue
		array( 22, 163, 35 ), // green
		array( 214, 36, 7 ),  // red
	);
	public $shadow_color = null; // Shadow color in RGB-array or null ... array(0, 0, 0);
	public $line_width = 0; // Horizontal line through the text
	public $cache_expiry = 4; // The cache expiry time

	/**
	 * Font configuration.
	 *
	 * - font: TTF file
	 * - spacing: relative pixel space between character
	 * - minSize: min font size
	 * - maxSize: max font size
	 */
	public $fonts = array(
		'Antykwa'  => array( 'spacing' => -3, 'minSize' => 27, 'maxSize' => 30, 'font' => 'AntykwaBold.ttf' ),
		'Candice'  => array( 'spacing' =>-1.5,'minSize' => 28, 'maxSize' => 31, 'font' => 'Candice.ttf' ),
		'DingDong' => array( 'spacing' => -2, 'minSize' => 24, 'maxSize' => 30, 'font' => 'Ding-DongDaddyO.ttf' ),
		'Duality'  => array( 'spacing' => -2, 'minSize' => 30, 'maxSize' => 38, 'font' => 'Duality.ttf' ),
		'Heineken' => array( 'spacing' => -2, 'minSize' => 24, 'maxSize' => 34, 'font' => 'Heineken.ttf' ),
		'Jura'     => array( 'spacing' => -2, 'minSize' => 28, 'maxSize' => 32, 'font' => 'Jura.ttf' ),
		'StayPuft' => array( 'spacing' =>-1.5,'minSize' => 28, 'maxSize' => 32, 'font' => 'StayPuft.ttf' ),
		'Times'    => array( 'spacing' => -2, 'minSize' => 28, 'maxSize' => 34, 'font' => 'TimesNewRomanBold.ttf' ),
		'VeraSans' => array( 'spacing' => -1, 'minSize' => 20, 'maxSize' => 28, 'font' => 'VeraSansBold.ttf' ),
	);

	/** Wave configuration in X and Y axes */
	public $y_period    = 12;
	public $y_amplitude = 14;
	public $x_period    = 11;
	public $x_amplitude = 5;

	public $max_rotation = 8; // letter rotation clockwise
	public $scale = 3;        // Internal image size factor (for better image quality) - 1: low, 2: medium, 3: high
	public $blur = true;      // Blur effect for better image quality (but slower image processing) - Better image results with scale=3
	public $debug = false;    // Debug?
	public $im;               // GD image
	public $key;              // The cache key value
	public $spam_key;         // The spam key value

	public function __construct() {

		parent::__construct();

		$this->set_captcha_settings();
		$this->init();
	}

	/**
	 * Set the CAPTCHA settings.
	 */
	public function set_captcha_settings() {

		$this->min_word_length = 3; // Min word length (for non-dictionary random text generation)
		$this->max_word_length = 5; // Max word length (for non-dictionary random text generation) - Used for dictionary words indicating the word-length for font-size modification purposes

		$this->y_period    = 92;
		$this->y_amplitude = 1;
		$this->x_period    = 91;
		$this->x_amplitude = 1;
		$this->max_rotation = 2; // letter rotation clockwise
		$this->scale = 3; // Internal image size factor (for better image quality) - 1: low, 2: medium, 3: high

	}

	/**
	 * Initialise image generation.
	 */
	public function init() {

		// Grab image from cache
		$this->key = md5( $_GET['captcha'] );
		//wp_cache_delete( $this->key, 'spam_destroyer' );
		$image_blob = wp_cache_get( $this->key, 'spam_destroyer' );
		if ( ! $image_blob ) {
			$this->create_image(); // Create the image since not found in cache
		} else {
			header( 'Content-type: image/png' );
			echo $image_blob;
			exit;
		}

}
	/*
	 * Create the image.
	 */
	public function create_image() {
		$ini = microtime( true );

		/** Initialization */
		$this->image_allocate();

		/** Text insertion */
		$text = $this->decrypt( $_GET['captcha'] );
		$text = explode( '|||', $text );
		$question = $text[0];

		$fontcfg  = $this->fonts[array_rand( $this->fonts )];
		$this->write_text( $question, $fontcfg );

		/** Transformations */
		if ( ! empty( $this->line_width ) ) {
			$this->write_line();
		}
		$this->wave_image();
		if ( $this->blur && function_exists( 'imagefilter' ) ) {
			imagefilter( $this->im, IMG_FILTER_GAUSSIAN_BLUR );
		}
		$this->reduce_image();

		if ( $this->debug ) {
			imagestring( $this->im, 1, 1, $this->height-8,
				"$question {$fontcfg['font']} " . round( ( microtime( true ) - $ini ) * 1000 ) . 'ms',
				$this->GdFgColor
			);
		}

		/** Output */
		$this->write_image();
		$this->cleanup();
	}

	/**
	 * Creates the image resources.
	 */
	protected function image_allocate() {
		// Cleanup
		if ( ! empty( $this->im ) ) {
			imagedestroy( $this->im );
		}

		$this->im = imagecreatetruecolor( $this->width*$this->scale, $this->height*$this->scale );

		// Background color
		$this->GdBgColor = imagecolorallocate(
			$this->im,
			$this->background_color[0],
			$this->background_color[1],
			$this->background_color[2]
		);
		imagefilledrectangle( $this->im, 0, 0, $this->width*$this->scale, $this->height*$this->scale, $this->GdBgColor );

		// Foreground color
		$color           = $this->colors[mt_rand( 0, sizeof( $this->colors ) - 1 )];
		$this->GdFgColor = imagecolorallocate( $this->im, $color[0], $color[1], $color[2] );

		// Shadow color
		if ( ! empty( $this->shadow_color ) && is_array( $this->shadow_color ) && sizeof( $this->shadow_color ) >= 3 ) {
			$this->gd_shadow_color = imagecolorallocate(
				$this->im,
				$this->shadow_color[0],
				$this->shadow_color[1],
				$this->shadow_color[2]
			);
		}
	}

	/**
	 * Horizontal line insertion.
	 */
	protected function write_line() {

		$x1 = $this->width*$this->scale * .15;
		$x2 = $this->textFinalX;
		$y1 = rand( $this->height * $this->scale * .40, $this->height * $this->scale * .65 );
		$y2 = rand( $this->height * $this->scale * .40, $this->height * $this->scale * .65 );
		$width = $this->line_width / 2 * $this->scale;

		for ( $i = $width * -1; $i <= $width; $i++ ) {
			imageline( $this->im, $x1, $y1 + $i, $x2, $y2 + $i, $this->GdFgColor );
		}
	}

	/**
	 * Text insertion.
	 */
	protected function write_text( $question, $fontcfg = array() ) {
		if ( empty( $fontcfg ) ) {
			// Select the font configuration
			$fontcfg  = $this->fonts[array_rand( $this->fonts )];
		}

		// Full path of font file
		$fontfile = SPAM_DESTROYER_DIR . '/assets/fonts/' . $fontcfg['font'];


		/** Increase font-size for shortest words: 9% for each glyp missing */
		$letters_missing = $this->max_word_length - strlen( $question );
		$font_size_factor = 1 + ( $letters_missing * 0.09 );

		// Text generation (char by char)
		$x      = 20 * $this->scale;
		$y      = round( ( $this->height * 27 / 40 ) * $this->scale );
		$length = strlen( $question );
		for ( $i = 0; $i < $length; $i++ ) {
			$degree   = rand( $this->max_rotation * -1, $this->max_rotation );
			$fontsize = rand( $fontcfg['minSize'], $fontcfg['maxSize'] ) * $this->scale * $font_size_factor;
			$letter   = substr( $question, $i, 1 );

			if ( $this->shadow_color ) {
				$coords = imagettftext(
					$this->im,
					$fontsize,
					$degree,
					$x+$this->scale,
					$y+$this->scale,
					$this->gd_shadow_color,
					$fontfile,
					$letter
				);
			}
			$coords = imagettftext(
				$this->im,
				$fontsize,
				$degree,
				$x,
				$y,
				$this->GdFgColor,
				$fontfile,
				$letter
			);
			$x += ( $coords[2] -$x ) + ( $fontcfg['spacing'] * $this->scale );
		}

		$this->textFinalX = $x;
	}

	/**
	 * Wave filter.
	 */
	protected function wave_image() {

		// X-axis wave generation
		$xp = $this->scale * $this->x_period * rand( 1, 3 );
		$k = rand( 0, 100 );
		for ( $i = 0; $i < ( $this->width * $this->scale ); $i++ ) {
			imagecopy(
				$this->im,
				$this->im,
				$i - 1,
				sin( $k + $i / $xp ) * ( $this->scale * $this->x_amplitude ),
				$i, 
				0, 
				1, 
				$this->height * $this->scale
			);
		}

		// Y-axis wave generation
		$k = rand( 0, 100 );
		$yp = $this->scale * $this->y_period * rand( 1,2 );
		for ( $i = 0; $i < ( $this->height * $this->scale ); $i++ ) {
			imagecopy(
				$this->im,
				$this->im,
				sin( $k + $i / $yp ) * ( $this->scale * $this->y_amplitude ),
				$i - 1,
				0, 
				$i,
				$this->width * $this->scale,
				1
			);
		}
	}

	/**
	 * Reduce the image to the final size.
	 */
	protected function reduce_image() {
		// Reduzco el tamaño de la imagen
		$imResampled = imagecreatetruecolor( $this->width, $this->height );
		imagecopyresampled(
			$imResampled,
			$this->im,
			0,
			0,
			0,
			0,
			$this->width,
			$this->height,
			$this->width * $this->scale,
			$this->height * $this->scale
		);
		imagedestroy( $this->im );
		$this->im = $imResampled;
	}

	/**
	 * File generation.
	 */
	protected function write_image() {

		// Cache the image - requires an object caching backend to work
		ob_start();
		imagepng( $this->im );
		$image_blob = ob_get_contents();
		wp_cache_add( $this->key, $image_blob, 'spam_destroyer', $this->cache_expiry ); // Cache blob
		ob_end_clean();

		header( 'Content-type: image/png' );
		echo $image_blob;
		exit;

	}

	/**
	 * Cleanup image.
	 */
	protected function cleanup() {
		imagedestroy( $this->im );
	}

}
new Spam_Destroyer_Generate_CAPTCHA();
