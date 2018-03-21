<?php
require __DIR__ . '/vendor/autoload.php';
$schema = \Symfony\Component\Yaml\Yaml::parse( __DIR__ . '/schema.yml');
$in = array();
$in['csv_fields'] = implode(',', array_keys($schema['fields']));
$in['dbuser'] = 'root';
$in['dbpass'] = 'root';
$in['dbname'] = 'mydb';
$in['classname'] = 'Espresso';
$in['parent'] = 'Base' . $in['classname'];
$in['drupal_root'] = 'C:\\wamp\\adid\\';
$in['custom_module_dir'] = 'sites\\all\\modules\\adid\\';

$output_path = (empty($in['drupal_root']) || empty($in['custom_module_dir']))
  ? __DIR__
  : $in['drupal_root'] .$in['custom_module_dir'];

ob_start();
doSomething($in);

$out=ob_get_clean();

$drupal_module = doDrupalSomething();
$drupal_info = 'name = My module
description = My module functionality.
package = My Package
core = 7.x
scripts[] = mymodule.js
files[] = mymodule.test
stylesheets[all][] = mymodule.css';
$time=time();

$new_dir = $output_path .  strtolower($in['classname']) . '_' . $time;
mkdir($new_dir);
mkdir($new_dir.'\\lib');
file_put_contents($new_dir . '\\appmodule-' . $time . '.module', html_entity_decode($drupal_info));

file_put_contents($new_dir . '\\appmodule-' . $time . '.module', html_entity_decode($drupal_module));

file_put_contents($new_dir . '\\lib\\app-' . $time . '.php', html_entity_decode($out));



// Create new Colors class
	$colors = new ColorCLI();

	/* Test some basic printing with Colors class
	echo ColorCLI::getColoredString("Testing Colors class, this is purple string on yellow background.", "purple", "yellow") . "\n";
	echo ColorCLI::getColoredString("Testing Colors class, this is blue string on light gray background.", "blue", "light_gray") . "\n";
	echo ColorCLI::getColoredString("Testing Colors class, this is red string on black background.", "red", "black") . "\n";
	echo ColorCLI::getColoredString("Testing Colors class, this is cyan string on green background.", "cyan", "green") . "\n";
	echo ColorCLI::getColoredString("Testing Colors class, this is cyan string on default background.", "cyan") . "\n";
	echo ColorCLI::getColoredString("Testing Colors class, this is default string on cyan background.", null, "cyan") . "\n";

*/
ColorCLI::info('Created app-' . $time . '.php');
