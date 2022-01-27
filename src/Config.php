<?php
/**
 * The file that retrieves a configuration file if requested.
 *
 * @package    ThoughtfulWeb\ActivationRequirementsWP
 * @author     Zachary Kendall Watkins <watkinza@gmail.com>
 * @copyright  Zachary Kendall Watkins 2022
 * @license    https://www.gnu.org/licenses/gpl-2.0.html GPL-2.0-or-later
 * @link       https://github.com/thoughtful-web/activation-requirements-wp/blob/main/src/Config.php
 * @since      0.1.0
 */

declare(strict_types=1);
namespace ThoughtfulWeb\ActivationRequirementsWP;

/**
 * The Activation Requirements Configuration class.
 *
 * @since 0.1.0
 */
class Config {

	/**
	 * The configuration associative array.
	 *
	 * @var array $config The associative array storing the final configuration state.
	 */
	private $config;

	/**
	 * Constructor for the Config class.
	 *
	 * @param array $config The configuration parameters. Either a configuration file name, file path, or array of configuration options.
	 *
	 * @return void
	 */
	public function __construct( $config = array() ) {

		$this->config = $this->maybe_autoload_file( $config );

	}

	/**
	 * Detect and load a config file if given an empty config variable.
	 *
	 * @param array $config The Settings page configuration parameters.
	 *
	 * @return array
	 */
	public function maybe_autoload_file( $config ) {

		$try_loading_file = empty( $config ) || is_string( $config ) ? true : false;
		if ( $try_loading_file ) {
			$path_from_subfolder = dirname( __FILE__, 5 ) . '/config/thoughtful-web/';
			$is_json             = false;
			if ( is_string( $config ) && ! empty( $config ) && preg_match( '/(\.php|\.json)$/', $config ) ) {
				// Load a file from the path provided by the user.
				$is_json = preg_match( '/\.json$/', $config );
				// If only a file name is provided, it must be in the config directory.
				// If a file path is provided, it must be a complete file path.
				$file_path_pre = preg_match( '/\//', $config ) ? '' : $path_from_subfolder;
			} elseif ( empty( $config ) ) {
				// If no parameter is provided then assume the file name is just "settings.json|php".
				$file_path_pre = $path_from_subfolder;
				$is_json       = file_exists( "{$path_from_subfolder}activation-requirements.json" );
				$config        = $is_json ? 'activation-requirements.json' : 'activation-requirements.php';
			}
			// Check for JSON, then PHP.
			$file_path = "{$file_path_pre}{$config}";
			if ( file_exists( $file_path ) ) {
				if ( $is_json ) {
					$str    = file_get_contents( $file_path );
					$config = json_decode( $str, true );
				} else {
					$config = include $file_path;
				}
			}
		}

		return $config;

	}

	/**
	 * Return the configuration array.
	 *
	 * @param string $key The array key to retrieve.
	 *
	 * @return array
	 */
	public function get( $key = '' ) {

		if ( ! empty( $key ) ) {
			return $this->config[ $key ];
		} else {
			return $this->config;
		}
	}
}
