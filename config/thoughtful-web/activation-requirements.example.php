<?php
/**
 * Plugin activation requirements.
 *
 * @package    Thoughtful Web Library for WordPress
 * @subpackage Plugin Requirements
 * @see        ThoughtfulWeb\LibraryWP\Plugin\Requirements( $plugin_query )
 * @copyright  Zachary Watkins 2022
 * @author     Zachary Watkins <watkinza@gmail.com>
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GPL-2.0-or-later
 * @link       https://github.com/thoughtful-web/library-wp/blob/master/config/activation-requirements.example.php
 * @since      0.1.0
 */

// If this file is called directly, or is included by a file other than those we expect, then abort.
if ( ! defined( 'ABSPATH' ) ) {
	http_response_code( 404 );
	?><html><head><title>HTTP 404 Not Found</title></head><body><p>The requested page does not exist.</p></body></html>
	<?php
	die();
}

return array(
	'plugins' => array(
		'relation' => 'OR',
		'advanced-custom-fields/acf.php',
		'advanced-custom-fields-pro/acf.php',
	),
);
