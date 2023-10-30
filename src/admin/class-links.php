<?php
/**
 * Includes helpful links to this plugins section of the WordPress plugins page.
 *
 * @package    Spam Destroyer
 * @subpackage Admin
 * @author     Ryan Hellyer <ryanhellyer@gmail.com>
 * @license    Copyright ©, Ryan Hellyer
 * @link       http://wordpress.stackexchange.com/questions/97553/adding-another-state-spam-reject-approve-to-wordpress-comments Source
 */

declare(strict_types=1);

namespace SpamDestroyer\Admin;

/**
 * Includes links on the WordPress plugins page.
 */
class Links {

	/**
	 * Initialize the class.
	 */
	public function init() {
		add_filter( 'plugin_row_meta', array( $this, 'plugins_page_meta' ), 10, 4 );
	}

	/**
	 * Add a settings page link to the plugin list.
	 *
	 * The code was adapted from https://gist.github.com/lloc/5685040
	 * The idea was adapted from the Minit plugin by Kaspars Dambis (http://kaspars.net/)
	 *
	 * @param array  $plugin_meta Plugin meta links.
	 * @param string $plugin_file The plugin file name.
	 * @return array The plugin meta.
	 */
	public function plugins_page_meta( array $plugin_meta, string $plugin_file ): array {

		// Bail out now if not on Spam Destroyer plugin.
		if ( 'spam-destroyer/index.php' !== $plugin_file ) {
			return $plugin_meta;
		}

		return $this->add_meta( $plugin_meta );
	}

	/**
	 * Add links to the plugin meta array.
	 *
	 * @param array $plugin_meta Plugin meta links.
	 * @return array The plugin meta.
	 */
	private function add_meta( array $plugin_meta ): array {
		// Add the plugin page link.
		$plugin_meta[] = sprintf(
			'<a href="%s">%s</a>',
			'https://geek.hellyer.kiwi/plugins/spam-destroyer/',
			esc_html__( 'Plugin page' ) // Note: doesn't use second argument as this translation is available from within WordPress core.
		);

		// Add the donations page link.
		$plugin_meta[] = sprintf(
			'<a href="%s">%s</a>',
			'https://geek.hellyer.kiwi/donations/',
			esc_html__( 'Donate', 'spam-destroyer' )
		);

		return $plugin_meta;
	}
}
