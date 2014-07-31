<?php

/**
 * Setting protection level
 * 
 * @copyright Copyright (c), Ryan Hellyer
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.0
 */
class Spam_Destroyer_Protection_Level extends Spam_Destroyer {

	/**
	 * Class constructor
	 */
	public function __construct() {
		add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widget' ) ); // Add dashboard widget
		add_action( 'spam_comment',       array( $this, 'spam_it' ) );
		add_action( 'unspam_comment',     array( $this, 'unspam_it' ) );
	}

	/**
	 * For testing purposes only
	 */
	public function spam_it( $comment_id ) {
		update_comment_meta( $comment_id, 'issues', 'Marked as spam' );
		return $comment_id;
	}

	/**
	 * For testing purposes only
	 */
	public function unspam_it( $comment_id ) {
		update_comment_meta( $comment_id, 'issues', 'Removed from spam' );
		return $comment_id;
	}

	/*
	 * Add the dashboard widget
	 */
	public function add_dashboard_widget() {
		wp_add_dashboard_widget(
			'dashboard_spam_destroyer',
			__( 'Spam Destroyer', 'spam-destroyer' ),
			array( $this, 'dashboard_widget' )
		);
	}

	/*
	 * The dashboard widget content
	 */
	public function dashboard_widget() {
		echo '<p>';
		echo sprintf( __( 'Spam Destroyer is current at the %s protection leve ', 'spam-destroyer' ), $this->level ) . ' ';
		echo '</p>';
	}

}
new Spam_Destroyer_Protection_Level;
