# WP EZ CPTs

This package is intended to be used by developers to more easily add custom post types and taxonomies to WordPress, and to take the grunt-work out of proper labels and update messages.

This is very much a work-in-progress - expect some best-practices to have been neglected.


## Installation

Install this as a standard plugin, or install it as an mu-plugin alongside [Roots' must-use plugin autoloader](https://roots.io/bedrock/docs/mu-plugin-autoloader/).


## Usage

### Register a Custom Post Type

```php
use BK\EZCPT\Controllers\PostTypeController as EzCPT;

$ExampleCPT = [
	// Required
	'slug'   => 'my-slug',
	// Required
	'labels' => ['Singular Label', 'Plural Label'], // i.e. Post, Posts
	// Optional
	'icon'   => '', // TODO - URL or file-path?
	// Optional
	'extra'  => [
		'taxonomies' => ['category', 'post_tag', /*...*/],
		// Anything else compatible with WordPress' `register_post_type`'s `$args` param.
		// Can also be used to override the default args this plugin registers CPTs with.
	],
];

EzCPT::register($ExampleCPT);
```

---

### Register a Custom Taxonomy

```php
use BK\EZCPT\Controllers\TaxonomyController as EzTax;

$ExampleTax = [
	// Required
	'slug'      => 'my-slug',
	// Required
	'postTypes' => ['post', 'page', /*...*/],
	// Required
	'labels'    => ['Singular Label', 'Plural Label'], // i.e. Tag, Tags
	// Optional
	'extra'     => [
		// Anything else compatible with WordPress' `register_taxonomy`'s `$args` param.
		// Can also be used to override the default args this plugin registers custom taxonomies with.
	],
];

EzTax::register($ExampleTax);
```

---


## TODOs

- Implement CPT icon functionality.
- Implement warnings/errors for insufficient data and duplicate registrations.
- Figure out `supports` arg for CPTs.
- Figure out `capabilities`, `rewrite`, and other args for taxonomies.
- ... so many more. Search for "TODO" in this directory.
