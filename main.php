<!DOCTYPE html>
<head>
<title>Output</title>
<style type="text/css">
.output {

color:#FFF;

background-color:#111;

margin:50px;
padding:50px;

}
</style>
</head>

<body>

<?php require 'backlink.php'; ?>


<div class="output">
<?php 
$path = __DIR__ . '/vendor/autoload.php';


require $path;

use Symfony\Component\Yaml\Yaml;

if(isset($_POST['theyaml'])){
	$yaml= str_replace("\t",'  ', $_POST['theyaml']);
	$schema = Yaml::parse(strip_tags(trim($yaml))); }
else {
	$schema = Yaml::parseFile( __DIR__ . '/schema.yml');
}
$classes  = get_declared_classes();

$in = array();
$in['csv_fields'] = implode(',', array_keys($schema['fields']));
$in['dbuser'] = $schema['dbuser'];
$in['dbpass'] = $schema['dbpass'];
$in['dbname'] = $schema['dbname'];
$in['classname'] =$schema['classname'];
$in['parent'] = 'Base' . $in['classname'];
$in['drupal_root'] = ''; // 'C:\\wamp\\adid\\';
$in['custom_module_dir'] = '';
// $output_path = (empty($in['drupal_root']) || empty($in['custom_module_dir']))
//   ? __DIR__
//     : $in['drupal_root'] .$in['custom_module_dir'];
//
     ob_start();
     doSomething($in);

     $out=ob_get_clean();
//     $new_dir = $output_path .  strtolower($in['classname']) . '_' . $time;
//     //mkdir($new_dir);
//     //mkdir($new_dir.'\\lib');
//     //file_put_contents($new_dir . '\\appmodule-' . $time . '.module', html_entity_decode($drupal_info));
//
     echo '<pre>'.html_entity_decode($out).'</pre>';


// Create new Colors class
//	$colors = new ColorCLI();

	/* Test some basic printing with Colors class
	echo ColorCLI::getColoredString("Testing Colors class, this is purple string on yellow background.", "purple", "yellow") . "\n";
	echo ColorCLI::getColoredString("Testing Colors class, this is blue string on light gray background.", "blue", "light_gray") . "\n";
	echo ColorCLI::getColoredString("Testing Colors class, this is red string on black background.", "red", "black") . "\n";
	echo ColorCLI::getColoredString("Testing Colors class, this is cyan string on default background.", "cyan") . "\n";
	echo ColorCLI::getColoredString("Testing Colors class, this is default string on cyan background.", null, "cyan") . "\n";


ColorCLI::info('Created app-' . $time . 
	 */
?>
</div>
</body>
</html>
