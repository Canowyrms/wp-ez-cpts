<?php
/**
 * @wordpress-plugin
 * Plugin Name: WP Ez CPTs
 * Plugin URI:  http://TODO.dev/
 * Description: TODO
 * Version:     0.0.0
 * Author:      TODO
 * Author URI:  TODO
 * License:     TODO
 * License URI: TODO
 */

declare(strict_types=1);

namespace BK\EZCPT;

use BK\EZCPT\Hooks\{
	Activator,
	Deactivator,
	Uninstaller
};
use BK\EZCPT\Controllers\PostTypeController as EzCPT;
use BK\EZCPT\Controllers\TaxonomyController as EzTax;

require_once __DIR__ . '/vendor/autoload.php';

/**
 * Plugin constants
 */

//define('EZCPT_CONST', '');

/**
 * Plugin hooks
 */

//@fmt:off
//register_activation_hook(  __FILE__, fn () => Activator::init());   // TODO - Plugin activator
//register_deactivation_hook(__FILE__, fn () => Deactivator::init()); // TODO - Plugin deactivator
//register_uninstall_hook(   __FILE__, fn () => Uninstaller::init()); // TODO - Plugin uninstaller
//@fmt:on

/**
 * Boot up
 */

$ezcpts = new EzCPTs();

/**
 * Testing
 */

/*EzCPT::register([
	'slug'   => 'test-cpt',
	'labels' => ['Test CPT', 'Test CPTs'],
	'icon'   => '', // TODO
	'extra'  => [
		'taxonomies' => ['category', 'post_tag']
	], // TODO?
]);*/

/*EzTax::register([
	'slug'      => 'test-tax',
	'postTypes' => ['test-cpt'],
	'labels'    => ['Test Taxonomy', 'Test Taxonomies'],
	'extra'     => [], // TODO?
]);*/
