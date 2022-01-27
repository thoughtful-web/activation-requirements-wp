<?php
/**
 * The file that provides plugin activation status detection via a configuration file or an array
 * passed to the constructor.
 *
 * Inspired by the meta_query parameter of WP_Meta_Query().
 * https://developer.wordpress.org/reference/classes/wp_meta_query/
 *
 * @package    ThoughtfulWeb\ActivationRequirements
 * @subpackage Query
 * @author     Zachary Kendall Watkins <watkinza@gmail.com>
 * @copyright  Zachary Kendall Watkins 2022
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GPL-2.0-or-later
 * @link       https://github.com/thoughtful-web/activation-requirements-wp/blob/master/src/Query/Plugins.php
 * @since      0.1.0
 */

declare(strict_types=1);
namespace ThoughtfulWeb\ActivationRequirementsWP\Query;

/**
 * The class that validates configuration requirements.
 *
 * @since 0.1.0
 */
class Plugins {

	/**
	 * Query results.
	 *
	 * @var array $results The plugin query results.
	 */
	private $query_results = array();

	/**
	 * Class constructor.
	 *
	 * @since  0.1.0
	 *
	 * @param array $plugin_clause {
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
	 * @return array
	 */
	public function __construct( $plugin_clause ) {

		// Results structure for this class's sole public function.
		$results = array(
			'passed'   => true,
			'relation' => '',
			'results'  => array(),
			'active'   => array(),
			'inactive' => array(),
			'notify'   => array(),
			'message'  => '',
		);

		// Enforce a default value of 'AND' for $relation.
		$relation = 'AND';
		if ( isset( $plugin_clause['relation'] ) ) {
			if ( 'OR' === strtoupper( $plugin_clause['relation'] ) ) {
				$relation = 'OR';
			}
			unset( $plugin_clause['relation'] );
		}
		$results['relation'] = $relation;

		// Get the active status of plugins.
		$plugin_status = $this->get_plugin_status( $plugin_clause );
		$results['active']      = $plugin_status['active'];
		$results['inactive']    = $plugin_status['inactive'];
		$results['uninstalled'] = $plugin_status['uninstalled'];

		// Determine if the currently active and inactive plugins meet the requirements configuration.
		if ( 'AND' === $relation ) {
			$results['passed'] = empty( $results['inactive'] );
		} else {
			$results['passed'] = 1 === count( $results['active'] ) ? true : false;
		}

		// Determine which plugins to report failure for.
		if ( 'AND' === $relation ) {
			$results['notify'] = $results['inactive'];
		} else {
			$results['notify'] = 1 < count( $results['active'] ) ? $results['active'] : $results['inactive'];
		}
		if ( ! empty( $results['uninstalled'] ) ) {
			$results['notify'] = array_merge( $results['notify'], $results['uninstalled'] );
		}

		/**
		 * Assemble all inactive plugins as a phrase using the relation parameter.
		 */
		error_log(serialize($results));
		if ( 0 < count( $results['notify'] ) ) {

			$results['message'] = $this->phrase_plugin_names( $results['notify'], $relation );

		}

		$this->query_results = $results;
	}

	/**
	 * Get the status of an array of plugin basenames.
	 *
	 * @param string[] $plugin_basenames An array of plugin basenames.
	 *
	 * @return array
	 */
	private function get_plugin_status( $plugin_basenames ) {

		$results = array(
			'active'      => array(),
			'inactive'    => array(),
			'uninstalled' => array(),
		);

		// Store plugin activation statuses.
		foreach ( $plugin_basenames as $key => $plugin ) {

			$file_path = WP_PLUGIN_DIR . '/' . $plugin;

			if ( ! file_exists( $file_path ) ) {
				$results['uninstalled'][] = 'uninstalled "' . $plugin . '"';
			} elseif ( is_plugin_active( $plugin ) ) {
				$results['active'][] = $plugin;
			} else {
				$results['inactive'][] = $plugin;
			}
		}

		return $results;

	}

	/**
	 * Convert plugin file paths into a phrase of names and a relation.
	 *
	 * Example 1: "Advanced Custom Fields"
	 * Example 2: "Advanced Custom Fields or Advanced Custom Fields Pro"
	 * Example 3: "Advanced Custom Fields and Admin Columns"
	 * Example 4: "Advanced Custom Fields, Admin Columns, and Gravity Forms"
	 * Example 5: "Advanced Custom Fields, Admin Columns, or Gravity Forms"
	 *
	 * @param string[] $plugin_files The relative plugin file paths to convert into names.
	 * @param string   $relation    The relationship between the plugins. Default: AND
	 * @return string
	 */
	private function phrase_plugin_names( $plugin_files, $relation = 'AND' ) {

		$message      = '';
		$plural       = 'OR' === $relation ? 1 : count( $plugin_files );
		$plugin_names = $this->get_plugin_names( $plugin_files );

		if ( 2 >= $plural ) {
			$message = implode( strtolower( " $relation " ), $plugin_names );
		} else {
			$plugin_last  = array_pop( $plugin_names );
			$message      = implode( ', ', $plugin_names );
			$message     .= strtolower( ", $relation " ) . $plugin_last;
		}

		return $message;

	}

	/**
	 * Retrieve plugin names from their file paths.
	 *
	 * @param string[] $plugins The array of relative plugin file paths.
	 *
	 * @return string[]
	 */
	private function get_plugin_names( $plugins ) {

		$plugin_names = array();

		foreach ( $plugins as $plugin ) {

			$file_path   = WP_PLUGIN_DIR . '/' . $plugin;
			$plugin_name = $plugin;

			if ( file_exists( $file_path ) ) {

				$plugin_data = get_plugin_data( $file_path );

				if ( ! empty( $plugin_data['Name'] ) ) {

					$plugin_name = $plugin_data['Name'];

				}
			}

			$plugin_names[] = $plugin_name;

		}

		return $plugin_names;

	}

	/**
	 * Return the query results.
	 *
	 * @return array
	 */
	public function results() {

		return $this->query_results;

	}
}
