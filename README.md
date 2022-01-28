# Activation Requirements for WordPress

>Free open source software under the GNU GPL-2.0+ License.  
>Copyright Zachary Kendall Watkins 2022.  

This library trivializes requiring other plugins to be activated before your plugin can be actived. Declare plugin activation requirements in a configuration file (*.php or *.json) and apply them with the library's Plugin class.

## Table of Contents

1. [Features](#features)
1. [Requirements](#requirements)
2. [Installation](#installation)
3. [Simplest Implementation](#simplest-implementation)
4. [Implementation](#implementation)
5. [Creating the Config File](#creating-the-config-file)
6. [Roadmap](#roadmap)

## Features

1. Declare plugin requirements in a configuration file and automatically enforce these requirements during the plugin's activation phase.
2. Format a helpful message using `wp_die()` ([*link to official documentation*](https://developer.wordpress.org/reference/functions/wp_die/)) to indicate which plugin(s) need to be installed and/or activated in order to successfully activate your plugin.

## Requirements

1. WordPress 5.4 and above.
2. PHP 7.3.5 and above.
3. This library existing two directory levels below your plugin's root directory. Examples:  
   a. `vendor/thoughtful-web/activation-requirements-wp`  
   b. `lib/thoughtful-web/activation-requirements-wp`  
4. A configuration file or PHP array (*see [Creating the Config File](#creating-the-config-file)*)

## Installation

To install this module from Composer directly, use the command line. Then either use Composer's autoloader or require each of the 3 class files directly in your PHP.

`$ composer require thoughtful-web/activation-requirements-wp`

To install this module from Github using Composer, add it as a repository to the composer.json file:

```
{
    "name": "zachwatkins/wordpress-plugin-name",
    "description": "WordPress plugin boilerplate using best practices, tools, and commonly needed modules.",
	"repositories": [
		{
			"type": "vcs",
			"url": "https://github.com/thoughtful-web/activation-requirements-wp"
		}
	],
	"require": {
		"thoughtful-web/activation-requirements-wp": "dev-main"
	}
}
```

## Simplest Implementation

The simplest implementation of this library is to add a configuration file at (1) `./config/thoughtful-web/activation-requirements.php` or (2) `./config/thoughtful-web/activation-requirements.json`. Then use Composer's autoloader and the main class file without a parameter. It should look like this:  

```php
require __DIR__ . '/vendor/autoload.php;
new \ThoughtfulWeb\ActivationRequirementsWP\Plugin();
```

## Implementation

To load the Plugin class with (or without) a configuration parameter you should know the accepted values:

```php
@param array $config The configuration parameters. Either a configuration file name, file path, or array of configuration options.
```

This library will load a file using an `include` statement if it is a PHP file or using `file_read_contents()` if it is a JSON file. Here is an explanation of the possible values for this parameter:

1. The **"no parameter"** approach requires the configuration file to be here: `./config/thoughtful-web/activation-requirements.php`. Example:  
   a. `new \ThoughtfulWeb\ActivationRequirementsWP\Plugin();`  

2. The **"file name"** approach accepts a PHP or JSON file name and requires the file to be in `./config/thoughtful-web/<file>`. Examples:  
   a. `new \ThoughtfulWeb\ActivationRequirementsWP\Plugin( 'filename.php' );`  
   b. `new \ThoughtfulWeb\ActivationRequirementsWP\Plugin( 'filename.json' );`  

3. The **"file path"** approach allows the config file to be anywhere on your server where the `./src/Config.php` class file has read access. Examples:  
   a. `new \ThoughtfulWeb\ActivationRequirementsWP\Plugin( __DIR__ . '/config/filename.json' );`  
   b. `new \ThoughtfulWeb\ActivationRequirementsWP\Plugin( '/home/website/filename.php' );`  

4. The **"array"** approach allows you to pass a PHP array containing the configuration values in their final state. Example:

```php
$config = array(
	'plugins' => array(
		'relation' => 'OR',
		'advanced-custom-fields/acf.php',
		'advanced-custom-fields-pro/acf.php',
	),
)
new \ThoughtfulWeb\ActivationRequirementsWP\Plugin( $config );
```

***Note:** Call the class as early as you can in your plugin's code for best performance. Also, you must either call the class without an action hook or within an action hook early enough in the execution order to not skip the WordPress actions, filters, and functions used in this library's class files. It is yet to be determined which action hooks are compatible with the class's instantiation.*

## Creating the Config File

Example configuration files are shown below.

*./config/thoughtful-web/activation-requirements.json*
```json
{
    "plugins": {
        "relation": "OR",
        "0": "advanced-custom-fields\/acf.php",
        "1": "advanced-custom-fields-pro\/acf.php"
    }
}
```

*./config/thoughtful-web/activation-requirements.php*
```php
<?php
// For security.
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
```

Here is an example using the "AND" relation parameter:

```php
return array(
    'plugins' => array(
        'relation' => 'AND',
        'advanced-custom-fields/acf.php',
        'gravityforms/gravityforms.php',
        'post-smtp/postman-smtp.php',
    ),
);
```

## Roadmap

These are changes that I am either considering or will seek to implement.

1. Confirm support for themes to use this library.
2. Add a "themes" parameter to the configuration array to check for the active theme or the presence of a parent theme.
3. Provide a configuration value that facilitates post-activation notices to the user.
4. Allow both 'AND' and 'OR' clauses to be declared in the configuration.
5. Improve the error page content by including more plugin data parameters if available.
