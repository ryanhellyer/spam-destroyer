<?php

/**
 * Spam Destroyer settings class
 * This adds settings to the options-discussion.php page
 * In common use, these settings should never need modified.
 * 
 * @copyright Copyright (c), Ryan Hellyer
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.8
 */
class Spam_Destroyer_Settings extends Spam_Destroyer {

	private $possible_levels;

	/**
	 * Fire the constructor up :)
	 *
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 * @since 1.8
	 */
	public function __construct() {

		// Set the spam protection level
		add_option( 'spam-killer-level', 'low' );

		// Set the possible protection levels
		$this->possible_levels = array(
			'low'       => __( 'Low', 'spam-killer' ),
			'medium'    => __( 'Medium', 'spam-killer' ),
			'high'      => __( 'High', 'spam-killer' ),
			'very-high' => __( 'Very high', 'spam-killer' ),
		);

		add_action( 'admin_init', array( $this, 'register_setting' ) );
		add_filter( 'plugin_row_meta', array( $this, 'plugins_page_meta' ), 10, 4 );

	}

	/**
	 * Add a settings page link to the plugin list
	 *
	 * The code was adapted from https://gist.github.com/lloc/5685040
	 * The idea was adapted from the Minit plugin by Kaspars Dambis (http://kaspars.net/)
	 *
	 * @param  array    $plugin_meta   Plugin meta links
	 * @param  string   $plugin_file   The plugin file name
	 * @return array    The plugin meta
	 */
	public function plugins_page_meta( $plugin_meta, $plugin_file ) {

		// Bail out now if not on Spam Destroyer plugin
	    if ( 'spam-destroyer/index.php' != $plugin_file ) {
	    	return $plugin_meta;
	    }

	    // Add the settings page link
		$plugin_meta[] = sprintf( 
			'<a href="%s">%s</a>',
			admin_url( 'options-discussion.php#spam-destroyer-settings' ), 
			__( 'Settings' ) 
		);

		// Add the plugin page link
		$plugin_meta[] = sprintf( 
			'<a href="%s">%s</a>',
			'http://geek.ryanhellyer.net/product/spam-destroyer/', 
			__( 'Plugin page' ) 
		);

		// Add the donations page link
		$plugin_meta[] = sprintf( 
			'<a href="%s">%s</a>',
			'http://geek.ryanhellyer.net/donations/', 
			__( 'Donate', 'spam-killer' ) 
		);

		return $plugin_meta;
	}

	/**
	 * Tell WP we use a setting - and where.
	 */
	public function register_setting() {

		// Create new section on discussion page
		add_settings_section(
			'spam-killer',
			__( 'Anti-spam settings', 'spam-killer' ),
			array( $this, 'description' ),
			'discussion'
		);

		// Register the spam level settings
		register_setting(
			'discussion',
			'spam-killer-level',
			'trim'
		);
		add_settings_field(
			'spam-killer-level',
			__( 'Protection level', 'spam-killer' ),
			array( $this, 'spam_level_field' ),
			'discussion',
			'spam-killer',
			array ( 'label_for' => 'spam-killer' )
		);

		// Register the spam key settings
		register_setting(
			'discussion',
			'spam-killer-key',
			array( $this, 'generate_spam_key' )
		);
		add_settings_field(
			'spam-killer-key',
			__( 'Reset anti-spam key', 'spam-killer' ),
			array( $this, 'spam_killer_field' ),
			'discussion',
			'spam-killer',
			array ( 'label_for' => 'spam-killer' )
		);

	}

	/**
	 * Generate a new spam key
	 *
	 * @access  private
	 * @param   string  $input   If input is 'on' then checkbox has been checked
	 * @return  string  $output  The santized spam protection key
	 */
	private function generate_spam_key( $input ) {

		if ( 'on' == $input ) {
			$output = $this->generate_new_key();
		} else {
			$output = get_option( 'spam-killer-key' ); // If checkbox not checked, then just output existing value
		}

		return $output;
	}

	/**
	 * Print the text before our field.
	 */
	public function description() {
		echo '<p id="spam-destroyer-settings" class="description">' . __( 'Unless you experience a severe spam problem, these settings should never need modified.', 'spam-killer' ) . '</p>';
	}

	/**
	 * Show the spam level field
	 *
	 * @param array $args
	 */
	public function spam_level_field( $args ) {

		echo '
		<select id="spam-killer-level" for="' . esc_attr( $args['label_for'] ) . '" name="spam-killer-level">';

			// Output the various possible levels
			foreach( $this->possible_levels as $value => $name ) {
				echo '<option ' . selected( $value, get_option( 'spam-killer-level' ) ) . 'value="' . esc_attr( $value ) . '">' . esc_html( $name ) . '</option>';
			}

		echo '
		</select>';
	}

	/**
	 * Show the spam killer field
	 *
	 * @param array $args
	 */
	public function spam_killer_field( $args ) {

		echo '
		<input type="checkbox" id="spam-killer-key" name="spam-killer-key" />
		<small><em>Current key: ' . esc_html( get_option( 'spam-killer-key' ) ) . '</em></small>
		';
	}

}
new Spam_Destroyer_Settings();
