<?php

/*
 * THIS IS A WORK IN PROGRESS AND IS NOT CURRENTLY LOADED!
 */

/**
 * Setting protection level
 * 
 * @copyright Copyright (c), Ryan Hellyer
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.0
 */
class Spam_Destroyer_Protection_Level extends Spam_Destroyer {

	/**
	 * Class constructor.
	 */
	public function __construct() {

		$this->comment_issues['manual-spam'] = __( 'Manual spam', 'spam-killer' ); // Translation for comment being marked as spam

		add_action( 'spam_comment',       array( $this, 'spam_it' ) );
		add_action( 'unspam_comment',     array( $this, 'unspam_it' ) );
		add_action( 'admin_notices',      array( $this, 'admin_notice' ) );

	}

	/**
	 * Admin notice.
	 * Prompts user to increase or decrease spam protectoin level.
	 */
	public function admin_notice() {
		?>

		<script>
		function spam_killer_show_hide(elementid){
			if (document.getElementById(elementid).style.display == 'none'){
				document.getElementById(elementid).style.display = '';
			} else {
				document.getElementById(elementid).style.display = 'none';
			}
		}

		</script>

		<div class="updated">
			<p><strong><?php _e( 'Increase spam protection level?', 'spam-killer' ); ?></strong></p>
			<p><?php _e( 'We have noticed you reported spam recently. Would you like us to increase your spam protection level?', 'spam-killer' ); ?></p>
			<p>
				<a href="#" class="button button-primary">Yes please</a>
				 &nbsp; 
				<a href="#" class="button">No thanks</a>
				 &nbsp; 
				<small><a href="javascript:spam_killer_show_hide('spam-protection-level-info');">More information</a></small>

				<div id="spam-protection-level-info" style="display:none;margin:1rem 0;">
					Increasing your spam protection level also increases the chance of real human commenters being detected as spam.

					<form method="post" action="options.php">
						<p>
							<label for="spam-killer">Protection level</label>
							<select id="spam-killer-level" for="spam-killer" name="spam-killer-level">
								<option  selected='selected'value="low">Low</option>
								<option value="medium">Medium</option>
								<option value="high">High</option>
								<option value="very-high">Very high</option>
							</select>
						</p>
						<p>
							<label for="spam-killer">Reset anti-spam key</label>
							<input type="checkbox" id="spam-killer-key" name="spam-killer-key" />
							<small><em>Current key: eb8bc64920c2411e17494cdfe0aae07f</em></small>
						</p>
						<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes"  /></p>
					</form>

				</div>

			</p>
		</div>
		<?php
	}

	/**
	 * Fired when the user marks a comment as spam.
	 *
	 * @param    int   $comment_id  The comments ID
	 * @return   int   The comments ID
	 */
	public function spam_it( $comment_id ) {

		// Load the parent constructor so that we can access $protection_levels
		parent::__construct();

		$current_level = get_option( 'spam-killer-level' );
		foreach( $this->protection_levels as $key => $level ) {
			if ( $level == $current_level ) {

/**
 ***********************************************
 ***********************************************
 *
 * BETTER IF PUTS INTO NOTIFICATION BOX IN ADMIN
 * THEN ASKS USER TO CONFIRM IF PROTECTION LEVEL
 * SHOULD CHANGE.
 *
 * SAME BOX WOULD APPEAR WHEN CRON CONFIRMS THAT
 * NO SPAM FOR A WHILE, THEREFORE SHOULD DROP
 * PROTECTION LEVEL BACK DOWN.
 * 
 ***********************************************
 ***********************************************
 */
//update_option( 'spam-killer-check-date', time() - (30*24*60*60) );

				// Last check date
				if ( '' == get_option( 'spam-killer-check-date' ) ) {
					$last_check_date = time() - ( 30 * 24 * 60 * 60 );
				} else {
					$last_check_date = get_option( 'spam-killer-check-date' );
				}

				// Check for recent spam comments
				$args = array(
					'status'     => 'spam',
					'date_query' => array(
						'after'     => date( 'F jS Y', $last_check_date ), // Set how long ago to check for comments back
						'before'    => 'tomorrow',
						'inclusive' => true,
					),
				);
				$comments = get_comments( $args );
				// Only bump protection level if there have been other recent spam comments
				if ( 1 < count( $comments ) ) {

					// Bump the protection level up a notch
					$key++; // Bump the key up a notch (corresponding to a higher protection level)
					if ( isset( $this->protection_levels[$key] ) ) {
						$new_level = $this->protection_levels[$key];
						update_option( 'spam-killer-level', $new_level ); // Update spam protection level
						update_option( 'spam-killer-check-date', time() ); // Update check date
						update_comment_meta( $comment_id, 'issues', 'manual-spam' );
					}

				}

			}
		}

		return $comment_id;
	}

	/**
	 * For testing purposes only
	 */
	public function unspam_it( $comment_id ) {
		update_comment_meta( $comment_id, 'manual-spam', false );
		return $comment_id;
	}

}
new Spam_Destroyer_Protection_Level;

/*
	add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widget' ) ); // Add dashboard widget
	public function add_dashboard_widget() {
		wp_add_dashboard_widget(
			'dashboard_spam_destroyer',
			__( 'Spam Destroyer', 'spam-destroyer' ),
			array( $this, 'dashboard_widget' )
		);
	}
	public function dashboard_widget() {
		echo '<p>';
		echo sprintf( __( 'Spam Destroyer is current at the %s protection leve ', 'spam-destroyer' ), $this->level ) . ' ';
		echo '</p>';
	}
*/
