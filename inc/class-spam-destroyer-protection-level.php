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
		add_action( 'init',     array( $this, 'get_spammed' ) );
	}

function get_spammed() {
	if ( ! isset( $_GET['test'] ) ) {
		return;
	}

	$args = array(
		'status'     => 'spam',
		'date_query' => array(
			'after'     => date( 'F jS Y', get_option( 'spam-killer-check-date' ) ),
			'before'    => 'tomorrow',
			'inclusive' => true,
		),
	);
	$comments = get_comments( $args );
print_r( $comments );
	$count = 0;
	foreach( $comments as $key => $comment ) {
		$issues = get_comment_meta( $comment->comment_ID, 'manual-spam', true );

		if ( true == $issues ) {
			$count++;
		}

	}

	if ( 0 < $count ) {
		$levels = array(
			0 => 'low',
			1 => 'medium',
			2 => 'high',
			3 => 'very-high',
		);
		foreach( $levels as $number => $level ) {
			if ( $level == get_option( 'spam-killer-level' ) ) {
				if ( isset( $levels[$number + 1] ) ) {
					$new_level = $levels[$number + 1];
				}
			}
		}

		update_option( 'spam-killer-level', $new_level );
		update_option( 'spam-killer-check-date', time() );
	}

	echo $count;
	die;
}

	/**
	 * For testing purposes only
	 */
	public function spam_it( $comment_id ) {
		update_comment_meta( $comment_id, 'manual-spam', true );
		return $comment_id;
	}

	/**
	 * For testing purposes only
	 */
	public function unspam_it( $comment_id ) {
		update_comment_meta( $comment_id, 'manual-spam', false );
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
