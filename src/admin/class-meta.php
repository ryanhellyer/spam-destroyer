<?php
/**
 * Meta Class for Spam Destroyer Plugin
 *
 * Manages comment metadata in the WordPress admin panel.
 * Adds an extra column in comments admin to show if a comment failed any spam checks.
 * Also handles adding reasons for failing spam checks as comment meta.
 *
 * @package    Spam Destroyer
 * @subpackage Admin
 * @author     Ryan Hellyer <ryanhellyer@gmail.com>
 * @license    Copyright ©, Ryan Hellyer
 * @since      1.8
 * @link       http://wordpress.stackexchange.com/questions/97553/adding-another-state-spam-reject-approve-to-wordpress-comments Source
 */

declare(strict_types=1);

namespace SpamDestroyer\Admin;

/**
 * Handles comment metadata in the WordPress admin panel.
 * Manages an extra column in the comments admin table to show if a comment failed any spam checks.
 * Responsible for adding reasons for failing spam checks as comment meta.
 */
class Meta {
	/**
	 * The Config class instance.
	 *
	 * @var \SpamDestroyer\Config
	 */
	private $config;

	/**
	 * Class constructor.
	 *
	 * @param \SpamDestroyer\Config $config The Config instance.
	 */
	public function __construct( \SpamDestroyer\Config $config ) {
		$this->config = $config;
	}

	/**
	 * Initialize the class.
	 */
	public function init() {
		add_action( 'manage_comments_custom_column', array( $this, 'comment_column' ), 10, 2 );
		add_action( 'wp_insert_comment', array( $this, 'add_issues_to_comment_meta' ), 10, 1 );
		add_filter( 'manage_edit-comments_columns', array( $this, 'filter_comment_column' ) );
	}

	/**
	 * Add reason for failing security check as comment meta
	 *
	 * We are using comment meta here, but an alternative is to use the comment karma field.
	 * The comment karma field is intended for this sort of data, but that field was added
	 * long before comment meta was integrated into WordPress core, so we are treating it as
	 * a legacy field and avoiding using it due to this. If you have a boner for using the
	 * comments karma field, feel free to get in touch and convince us to do it differently ;)
	 *
	 * @param int $id The comment ID.
	 */
	public function add_issues_to_comment_meta( int $id ) {
		// @todo check this works
		$failed = filter_input( INPUT_POST, 'failed' );
		if ( $failed ) {
			$failed = wp_kses_post( $failed );
			update_comment_meta( $id, 'issues', $failed );
		}
	}

	/**
	 * Add new heading to comments tables
	 *
	 * @param array $columns The comments columns.
	 * @return array $columns The modified comments columns.
	 */
	public function filter_comment_column( array $columns ): array {
		$columns['issues'] = __( 'Notes' ); // Note: doesn't use second argument as this translation is available from within WordPress core.
		return $columns;
	}

	/**
	 * Add comment meta for each column
	 *
	 * @param string $column The comment column name.
	 * @param int    $id The comment ID.
	 */
	public function comment_column( string $column, int $id ) {

		// Bail out if not on the issues column.
		if ( 'issues' !== $column ) {
			return;
		}

		// Output the issue into the column.
		$issue = get_comment_meta( $id, 'issues', true );

		if ( $issue ) {
			echo '<strong>' . esc_html__( 'Failed initial spam check', 'spam-destroyer' ) . ':</strong> ' . esc_html( $this->config->get_error_explanation( $issue ) );
		}
	}
}
