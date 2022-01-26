# Thoughtful Web Activation Requirements for WordPress

>Free open source software under the GNU GPL-2.0+ License.  
>Copyright Zachary Kendall Watkins 2022.  

This library enables declaring plugin activation requirements from a configuration file (*.php or *.json).

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

## Roadmap

1. Theme activation requirements.
2. Load activation requirements from a file.
3. Provide a configuration value that facilitates post-activation notices to the user.
