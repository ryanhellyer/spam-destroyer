<?php

/**
 * Add meta to comments
 * Adds extra column to comments in admin panel
 * Used for showing if the commenter failed any spam checks
 *
 * Adapted from code found at http://wordpress.stackexchange.com/questions/97553/adding-another-state-spam-reject-approve-to-wordpress-comments
 * 
 * @copyright Copyright (c), Ryan Hellyer
 * @author Ryan Hellyer <ryanhellyer@gmail.com>
 * @since 1.8
 */
class Spam_Destroyer_Add_Meta extends Spam_Destroyer {

	/**
	 * Add hooks and filters
	 *
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 * @since 1.8
	 */
	public function __construct() {

		// Add hooks
		add_action( 'manage_comments_custom_column', array( $this, 'comment_column' ), 10, 2 );
		add_action( 'wp_insert_comment',             array( $this, 'add_issues_to_comment_meta' ), 10, 2 );

		// Add filters
		add_filter( 'manage_edit-comments_columns', array( $this, 'filter_comment_column' ) );

	}

	/*
	 * Add reason for failing security check as comment meta
	 *
	 * We are using comment meta here, but an alternative is to use the comment karma field.
	 * The comment karma field is intended for this sort of data, but that field was added
	 * long before comment meta was integrated into WordPress core, so we are treating it as
	 * a legacy field and avoiding using it due to this. If you have a boner for using the
	 * comments karma field, feel free to get in touch and convince us to do it differently ;)
	 *
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 * @since 1.8
	 * @param  string   $id            The comment ID
	 * @param  object   $commentdata   The comment object
	 */
	public function add_issues_to_comment_meta( $id, $commentdata ) {

		// And now to actually save the data :)
		if ( isset( $_POST['failed'] ) ) {
			$failed = wp_kses_post( $_POST['failed'] );
			update_comment_meta( $id, 'issues', $failed );
		}

	}

	/*
	 * Add new heading to comments tables
	 *
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 * @since 1.8
	 * @param   array   $columns   The comments columns
	 * @return  array   $columns   The modified comments columns
	 */
	public function filter_comment_column( $columns ) {
		$columns['issues'] = __( 'Notes' );
		return $columns;
	}

	/*
	 * Add comment meta for each column
	 *
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 * @since 1.8
	 * @param   array   $columns   The comments columns
	 * @param   array   $id        The comment ID
	 * @return  array   $columns   The modified comments columns
	 */
	public function comment_column( $column, $id ) {

		// Bail out if not on the issues column;
		if ( 'issues' != $column ) {
			return;
		}

		// Need to call parent constructor here to access the $comment_issues variable
		parent::__construct();

		// Output the issue into the column
		$issue = get_comment_meta( $id, 'issues', true );
		if ( isset( $this->comment_issues[$issue] ) ) {
			echo '<strong>' . __( 'Failed initial spam check', 'spam-destroyer' ) . ':</strong> ' . esc_html( $this->comment_issues[$issue] );
		}

	}

}
new Spam_Destroyer_Add_Meta;
