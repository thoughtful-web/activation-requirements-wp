# Thoughtful Web Activation Requirements for WordPress

by Zachary Kendall Watkins
Published for free public use and access now and forever using the GNU GPL 2.0 and above software license.

## Requirements

1. WordPress 5.4 and above.
2. PHP 7.3.5 and above.
3. Any PHP extensions declared in this repository's unique source files (ThoughtfulWeb\LibraryWP\ namespace) are listed in composer.json:require 

## Installation

To install this module from Github using Composer, add it as a repository to the composer.json file:

```
{
    "name": "thoughtful-web/activation-requirements-wp",
    "description": "Helpful WordPress email logging released as free open source software under GNU GPL-2.0+ License",
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

Then either use Composer's autoloader or require the file directly in your PHP.
