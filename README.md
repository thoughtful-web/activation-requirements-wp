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
7. [Development Requirements, Installation, and Notes](#development-requirements-installation-and-notes)

## Features

1. Declare plugin requirements in a configuration file and automatically enforce these requirements during the plugin's activation phase.
2. Format a helpful message using `wp_die()` ([*link to official documentation*](https://developer.wordpress.org/reference/functions/wp_die/)) to indicate which plugin(s) need to be installed and/or activated in order to successfully activate your plugin.

[Back to top](#activation-requirements-for-wordpress)

## Requirements

1. WordPress 5.4 and above.
2. PHP 7.3.5 and above.
3. This library existing two directory levels below your plugin's root directory. Examples:  
   a. `vendor/thoughtful-web/activation-requirements-wp`  
   b. `lib/thoughtful-web/activation-requirements-wp`  
4. A configuration file or PHP array (*see [Creating the Config File](#creating-the-config-file)*)

[Back to top](#activation-requirements-for-wordpress)

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

[Back to top](#activation-requirements-for-wordpress)

## Simplest Implementation

The simplest implementation of this library is to add a configuration file at (1) `./config/thoughtful-web/activation-requirements.php` or (2) `./config/thoughtful-web/activation-requirements.json`. Then use Composer's autoloader and the main class file from within your plugin's root file. It should look like this:  

```php
require __DIR__ . '/vendor/autoload.php;
new \ThoughtfulWeb\ActivationRequirementsWP\Plugin();
```

[Back to top](#activation-requirements-for-wordpress)

## Implementation

The only parameter for the public-facing classes is optional and is either a configuration file name, file path, or array. To use the Plugin class with (or without) a configuration parameter you should know the accepted values:

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

[Back to top](#activation-requirements-for-wordpress)

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

[Back to top](#activation-requirements-for-wordpress)

## Roadmap

These are changes that I am either considering or will seek to implement.

1. Add a Theme top-level class that mimics the Plugin class but uses Theme hooks like the action hook `after_switch_theme`.
2. Add a "themes" parameter to the configuration array to check for the active theme or the presence of a parent theme.
3. Provide a configuration value that facilitates post-activation notices to the user.
4. Consider improving the error page content by including more plugin data parameters if available.
5. Allow both 'AND' and 'OR' clauses to be declared at the same time in the configuration.

[Back to top](#activation-requirements-for-wordpress)

## Development Requirements, Installation, and Notes

### Requirements

1. PHP 7.3.5+
2. The Composer package manager tool. [Get Composer](https://getcomposer.org/)
3. The PHP Codesniffer Composer module.  
   a. `$ composer global require squizlabs/php_codesniffer`
4. The [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/) ruleset.  
   a. `$ composer global require wp-coding-standards/wpcs`  
   b. `$ phpcs --config-set installed_paths $HOME/AppData/Roaming/Composer/vendor/wp-coding-standards/wpcs`  

### Installation

Run the following commands in your command line interface wherever you plan to install the library. If you are installing the library in this manner, you will typically do so within a directory like `my-wordpress-plugin/inc/thoughtful-web/`. The git hooks currently only automate creating a zip file using the latest tag after pushing changes to the repository's main branch, and only for Windows users since it runs a *.ps1 file.

1. `$ git clone https://github.com/thoughtful-web/activation-requirements-wp`
2. `$ cd activation-requirements-wp`
3. `$ composer install`
4. `$ git config core.hooksPath hooks`
5. If using Visual Studio Code I recommend these additional steps:  
   a. Install these extensions: "PHP Intelephense by Ben Mewburn" and "phpcs by Ioannis Kappas"   
   b. Include these key values with your Visual Studio Code workspace's settings file:
```json
{
    "phpcs.standard": "WordPress",
	"intelephense.stubs": [
		"wordpress",
		"apache",
		"bcmath",
		"bz2",
		"calendar",
		"com_dotnet",
		"Core",
		"ctype",
		"curl",
		"date",
		"dba",
		"dom",
		"enchant",
		"exif",
		"FFI",
		"fileinfo",
		"filter",
		"fpm",
		"ftp",
		"gd",
		"gettext",
		"gmp",
		"hash",
		"iconv",
		"imap",
		"intl",
		"json",
		"ldap",
		"libxml",
		"mbstring",
		"meta",
		"mysqli",
		"oci8",
		"odbc",
		"openssl",
		"pcntl",
		"pcre",
		"PDO",
		"pdo_ibm",
		"pdo_mysql",
		"pdo_pgsql",
		"pdo_sqlite",
		"pgsql",
		"Phar",
		"posix",
		"pspell",
		"readline",
		"Reflection",
		"session",
		"shmop",
		"SimpleXML",
		"snmp",
		"soap",
		"sockets",
		"sodium",
		"SPL",
		"sqlite3",
		"standard",
		"superglobals",
		"sysvmsg",
		"sysvsem",
		"sysvshm",
		"tidy",
		"tokenizer",
		"xml",
		"xmlreader",
		"xmlrpc",
		"xmlwriter",
		"xsl",
		"Zend OPcache",
		"zip",
		"zlib"
	]
}
```
### Notes

1. This repository uses a modified version of the [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/) to allow PSR-4 compliant class file names.
2. To add a new git hook file, run `$ git add --chmod=+x hooks/<hook-file-name> && git commit -m "Add git hook"`.

[Back to top](#activation-requirements-for-wordpress)
