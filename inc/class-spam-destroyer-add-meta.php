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
	 * This method uses an extraordinarily crude hack to get the meta data out. This
	 * was due to an inability to send data through wp-comments.php. This may be possible
	 * to do in more "correct" way, but I could not figure it out. If you know how
	 * to improve this, please let me know :)
	 *
	 * @author Ryan Hellyer <ryanhellyer@gmail.com>
	 * @since 1.8
	 * @param  string   $id            The comment ID
	 * @param  object   $commentdata   The comment object
	 */
	public function add_issues_to_comment_meta( $id, $commentdata ) {

		// CRUDE HACK! - no way was found to send extra data from this form to the comment processor, so we're temporarily tacking it on the front of the comment and removing it later
		$comment = explode( '_somerandomstringgoeshere_', $commentdata->comment_content );

//print_r( $_POST );
//die;

		$failed = $comment[0];
		// And now to actually save the data :)
		update_comment_meta( $id, 'issues', $failed );
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
		$columns['failed'] = __( 'Notes' );
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

		if ( 'issues' != $column ) {
			return;
		}

		echo esc_attr( get_comment_meta( $id, 'issues', true ) );
	}

}
new Spam_Destroyer_Add_Meta;
