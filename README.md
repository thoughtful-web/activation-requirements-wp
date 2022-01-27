# Thoughtful Web Activation Requirements for WordPress

>Free open source software under the GNU GPL-2.0+ License.  
>Copyright Zachary Kendall Watkins 2022.  

This library enables declaring plugin activation requirements from a configuration file (*.php or *.json).

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
2. Format a helpful message to indicate which plugin(s) need to be installed and/or activated in order to successfully activate the plugin.

## Requirements

1. WordPress 5.4 and above.
2. PHP 7.3.5 and above.

## Installation

To install this module from Composer directly, use the command line. Then either use Composer's autoloader or require the class files directly in your PHP.

`$ composer require thoughtful-web/activation-requirements-wp`

To install this module from Github using Composer, add it as a repository to the composer.json file:

```
{
    "name": "thoughtful-web/activation-requirements-wp",
    "description": "Helpful WordPress activation requirements library released as free open source software under GNU GPL-2.0+ License",
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

The simplest implementation of this library is to include it with the autoloader and add a configuration file at `./config/thoughtful-web/activation-requirements.php` or `./config/thoughtful-web/activation-requirements.json`. Then apply the activation requirements by creating a new instance of the `ThoughtfulWeb\ActivationRequirementsWP\Plugin` class in your Plugin's main file like this:  

```
require __DIR__ . '/vendor/autoload.php;
new \ThoughtfulWeb\ActivationRequirementsWP\Plugin();
```

## Implementation

To load the Plugin class with (or without) a configuration parameter you should know the accepted values:

```
@param array $config The configuration parameters. Either a configuration file name,
                     file path, or array of configuration options.
```

This class will load a file using an `include` statement if it is a PHP file or using `file_read_contents` it is a JSON file. Here is an explanation of the possible values for this parameter:

1. **No parameter** assumes there is a configuration file located here: `./config/thoughtful-web/activation-requirements.php`. Example:  
   a. `new \ThoughtfulWeb\ActivationRequirementsWP\Plugin();`  

2. **File name** accepts a PHP or JSON file name and requires the file to be in the directory `./config/thoughtful-web/{file}`. Examples:  
   a. `new \ThoughtfulWeb\ActivationRequirementsWP\Plugin( 'filename.php' );`  
   b. `new \ThoughtfulWeb\ActivationRequirementsWP\Plugin( 'filename.json' );`  

3. **File path** can be any location on your server, as long as the `./src/Config.php` class file has read access to it. Examples:  
   a. `new \ThoughtfulWeb\ActivationRequirementsWP\Plugin( __DIR__ . '/config/filename.json' );`  
   b. `new \ThoughtfulWeb\ActivationRequirementsWP\Plugin( '/home/website/filename.php' );`  

4. **Array** The configuration values in their final state.

**Note:** Call the class without an action hook or within an action hook early enough in the execution order to not skip the WordPress actions, filters, and functions used in this library's class files. It is yet to be determined which action hooks are compatible with the class's instantiation.

## Creating the Config File

Documentation for this library is a work in progress. Some documentation for creating a configuration file can be found below. Example configuration files are shown below.

*./config/thoughtful-web/activation-requirements.example.json*
```json
{
    "plugins": {
        "relation": "OR",
        "0": "advanced-custom-fields\/acf.php",
        "1": "advanced-custom-fields-pro\/acf.php"
    }
}
```

*./config/thoughtful-web/activation-requirements.example.php*
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

1. Theme activation requirements.
2. Load activation requirements from a file.
3. Provide a configuration value that facilitates post-activation notices to the user.
