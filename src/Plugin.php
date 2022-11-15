<?php
/**
 * The file that handles plugin activation and deactivation with annotated dependency checks.
 *
 * Links to PHP core documentation are included but this file will not be easy to grasp for beginners.
 *
 * @package    ThoughtfulWeb\ActivationRequirementsWP
 * @author     Zachary Kendall Watkins <watkinza@gmail.com>
 * @copyright  Zachary Kendall Watkins 2022
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GPL-2.0-or-later
 * @link       https://github.com/thoughtful-web/activation-requirements-wp/blob/master/src/Plugin.php
 * @since      0.1.0
 */

declare(strict_types=1);
namespace ThoughtfulWeb\ActivationRequirementsWP;

use \ThoughtfulWeb\ActivationRequirementsWP\Query\Plugins;
use \ThoughtfulWeb\ActivationRequirementsWP\Config;

/**
 * The class that handles plugin activation requirements.
 *
 * @since 0.1.0
 */
class Plugin {

	/**
	 * The root plugin file path relative to the server root.
	 *
	 * @var root_plugin_path
	 */
	private $root_plugin_path;

	/**
	 * The plugin query clause.
	 *
	 * @var array $plugin_clause
	 */
	private $plugin_clause;

	/**
	 * The plugin query results.
	 *
	 * @var array $plugin_query_results
	 */
	private $plugin_query_results;

	/**
	 * Initialize the class
	 *
	 * @todo Add support for an array of plugin clauses.
	 *
	 * @see   https://www.php.net/manual/en/function.version-compare.php
	 * @see   https://developer.wordpress.org/reference/functions/register_activation_hook/
	 * @since 0.1.0
	 *
	 * @param array|string $config {
	 *     The details for plugins which may or may not be present and/or active on the site.
	 *
	 *     @type string $relation Optional. The keyword used to compare the activation status of the
	 *                            plugins. Accepts 'AND' or 'OR'. Default 'AND'.
	 *     @type array  ...$0 {
	 *         An array of a plugin's data.
	 *
	 *         @type string $name Required. Display name of the plugin.
	 *         @type string $path Required. Path to the plugin file relative to the plugins
	 *                            directory.
	 *     }
	 * }
	 *
	 * @return void
	 */
	public function __construct( $config = array() ) {

		// Check if get_plugins() function exists. This is required on the front end of the
		// site, since it is in a file that is normally only loaded in the admin.
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$plugin_folder_name = basename( dirname( __FILE__, 5 ) );
		$plugin_files       = get_plugins( "/{$plugin_folder_name}" );
		if ( empty( $plugin_files ) ) {
			return;
		}

		// The plugin's root file was found.
		$file_name              = array_key_first( $plugin_files );
		$this->root_plugin_path = "{$plugin_folder_name}/{$file_name}";

		// Store attributes from the compiled parameters.
		$config_obj          = new \ThoughtfulWeb\ActivationRequirementsWP\Config( $config );
		$this->plugin_clause = $config_obj->get( 'plugins' );
		// Register activation hook.
		register_activation_hook( $this->root_plugin_path, array( $this, 'activate_plugin' ) );

	}

	/**
	 * Ensure plugin activation requirements are met and a graceful deactivation if not.
	 *
	 * @since  0.1.0
	 *
	 * @return void
	 */
	public function activate_plugin() {

		$plugin_query               = new \ThoughtfulWeb\ActivationRequirementsWP\Query\Plugins( $this->plugin_clause );
		$this->plugin_query_results = $plugin_query->results();

		// Handle result.
		if ( empty( $this->plugin_query_results['passed'] ) ) {
			$this->deactivate_plugin();
		}

	}

	/**
	 * Deactivate the plugin.
	 *
	 * @see    https://developer.wordpress.org/reference/functions/deactivate_plugins/
	 * @see    https://developer.wordpress.org/reference/functions/plugin_basename/
	 * @see    https://developer.wordpress.org/reference/functions/is_plugin_active_for_network/
	 * @since  0.1.0
	 *
	 * @return void
	 */
	public function deactivate_plugin() {

		// Deactivate the plugin in a multisite-friendly way.
		$plugin_base = plugin_basename( $this->root_plugin_path );

		if ( ! is_multisite() ) {
			deactivate_plugins( $plugin_base );
		} elseif ( is_network_admin() && is_plugin_active_for_network( $this->root_plugin_path ) ) {
			deactivate_plugins( $plugin_base, false, true );
		} else {
			deactivate_plugins( $plugin_base, false, false );
		}
		wp_die(
			wp_kses_post( $this->get_error_message() ),
			'Plugin Activation Error',
			array(
				'link_text' => 'Go back to the plugins page',
				'link_url'  => esc_url( admin_url( 'plugins.php' ) ),
			)
		);

	}

	/**
	 * Show admin notice
	 *
	 * @since 0.1.0
	 *
	 * @return string
	 */
	public function get_error_message() {

		$plugin_data = get_plugin_data( $this->root_plugin_path );
		$plural      = '';
		if (
			'AND' === $this->plugin_query_results['relation']
			&& count( $this->plugin_query_results['notify'] ) > 1
		) {
			$plural = 's';
		};
		$message_output = sprintf(
			/* translators: %1$s: The name of the deactivated plugin. %2$s: Name or names of the plugins which did not meet requirements. %3$s: Plural suffix for the word "plugin" if the number of plugins is 2 or more. */
			__( '<h1>%1$s could not be activated.</h1><p>Activate the %2$s plugin%3$s first.</p>', 'thoughtful-web-library-wp' ),
			esc_html( $plugin_data['Name'] ),
			esc_html( $this->plugin_query_results['message'] ),
			$plural
		);

		$message_output = apply_filters( 'twar_plugin_activation_error', $message_output, $plugin_data['Name'], $this->plugin_query_results['message'], $this->plugin_query_results );

		return $message_output;

	}
}
