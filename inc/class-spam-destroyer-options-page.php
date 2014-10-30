<?php

/**
 * Spam Destroyer options page class
 * 
 * @copyright Copyright (c), Ryan Hellyer
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.8
 */
class Spam_Destroyer_Options_Page extends Spam_Destroyer {

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

		// Add to hooks
		add_action( 'admin_init',                                        array( $this, 'register_settings' ) );
		add_action( 'admin_menu',                                        array( $this, 'options_add_page' ) );
		add_action( 'admin_menu',                                        array( $this, 'remove_menu_link' ), 20 );
		add_filter( 'plugin_action_links_' . 'spam-destroyer/index.php', array( $this, 'plugins_page_link' ) );
	}

	/**
	 * Add a Purge Cache link to the plugin list
	 */
	public function plugins_page_link( $links ) {

		$links[] = sprintf( 
				'<a href="%s">%s</a>',
				admin_url( 'admin.php?page=theme_options' ), 
				__( 'Settings' ) 
			);

		return $links;
	}

	/*
	 * Remove the admin page menu link
	 */
	public function remove_menu_link() {
		remove_menu_page( 'theme_options' );
	}

	/**
	 * Init plugin options to white list our options
	 */
	public function register_settings(){
		register_setting( 'spam-killer', 'spam-killer-level', array( $this, 'validate_level' ) );
		register_setting( 'spam-killer', 'spam-killer-key', array( $this, 'validate_key' ) );
	}

	/**
	 * Load up the menu page
	 */
	public function options_add_page() {
		add_menu_page( __( 'Spam Destroyer settings', 'spam-killer' ), __( 'Spam Destroyer settings', 'spam-killer' ), 'manage_options', 'theme_options', array( $this, 'admin_page' ) );
	}

	/**
	 * Output the admin page
	 */
	public function admin_page() {

		?>
		<div class="wrap">
			<?php screen_icon(); ?>

			<h2><?php _e( 'Spam Destroyer settings', 'spam-killer' ); ?></h2>
			<p><?php _e( 'Welcome to the Spam Killer settings page. In normal usage, you should never need to visit this page. But here you can manually adjustment the configuration of the Spam Destroyer plugins protection level.', 'spam-killer' ); ?></p>

			<form method="post" action="options.php">
				<?php settings_fields( 'spam-killer' ); ?>

				<table class="form-table">

					<tr valign="top">
						<th scope="row"><?php _e( 'Select the spam protection level to use', 'spam-killer' ); ?></th>
						<td>
							<select id="spam-killer-level" name="spam-killer-level"><?php

								// Output the various possible levels
								foreach( $this->possible_levels as $value => $name ) {

									if ( $value == get_option( 'spam-killer-level' ) ) {
										$selected = 'selected="selected" ';
									} else {
										$selected = '';
									}

									echo '<option ' . $selected . 'value="' . esc_attr( $value ) . '">' . esc_html( $name ) . '</option>';
								}

								?>
							</select>
							<label class="description" for="spam-killer-level"><?php _e( 'Spam protection level', 'spam-killer' ); ?></label>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row"><?php _e( 'Reset the spam protection key', 'spam-killer' ); ?></th>
						<td>
							<input type="checkbox" id="spam-killer-key" name="spam-killer-key" />
							<label class="description" for="spam-killer-key"><?php _e( 'You may need to refresh your page caches after resetting the spam protection key', 'spam-killer' ); ?></label>
							<br />
							<small><em>Current key: <?php echo esc_html( get_option( 'spam-killer-key' ) ); ?></em></small>
						</td>
					</tr>

				</table>

				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e( 'Save Settings', 'spam-killer' ); ?>" />
				</p>
			</form>
		</div><?php
	}

	/**
	 * Sanitize and validate protection level
	 *
	 * @param   string   $input   The spam protection level
	 * @return  string or bool  Returns the santized spam protection level or false if input doesn't match expected values
	 */
	public function validate_level( $input ) {
		$output = false;

		// Check input against all possible options
		foreach( $this->possible_levels as $value => $name ) {
			if ( $input == $value ) {
				$output = $input;
			}
		}

		return $output;
	}

	/**
	 * Validate and recreate key
	 *
	 * @param   string  $input   If input is 'on' then checkbox has been checked
	 * @return  string  $output  The santized spam protection key
	 */
	public function validate_key( $input ) {

		if ( 'on' == $input ) {
			$output = $this->generate_new_key();
		} else {
			$output = get_option( 'spam-killer-key' ); // If checkbox not checked, then just output existing value
		}

		return $output;
	}

}
new Spam_Destroyer_Options_Page();
