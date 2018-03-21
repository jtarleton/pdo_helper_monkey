<?php 
require __DIR__ . '/vendor/autoload.php';
$schema = \Symfony\Component\Yaml\Yaml::parse( __DIR__ . '/schema.yml');
$in = array(); 
$in['csv_fields'] = implode(',', array_keys($schema['fields']));
$in['dbuser'] = 'root';
$in['dbpass'] = 'root';
$in['dbname'] = 'mydb';
$in['classname'] = 'Customers';
$in['parent'] = 'BaseObject';
ob_start(); 
doSomething($in);

$out=ob_get_clean(); 
$time=time();
file_put_contents(__DIR__ . '/app-' . $time . '.php', html_entity_decode($out));



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
echo ColorCLI::getCs('Created app-' . $time . '.php', 'purple','yellow')."\n"; 
