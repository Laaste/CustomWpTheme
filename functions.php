<?php
if(session_id() == ''
|| ! session_id())
{
	session_start();
}

/////////////////////////////////////////////////////
/////////////////////////////////////////////////////
/////////////////////////////////////////////////////

if(file_exists(__DIR__ . '/vendor/autoload.php'))
{
	require_once(__DIR__ . '/vendor/autoload.php');
}

$baseFunctionsPath = __DIR__ . DIRECTORY_SEPARATOR . 'functions' . DIRECTORY_SEPARATOR;

$functionsFiles = [
	'utility.php',
	'normalize_wordpress.php',
	'utility_wordpress.php',
	'custom_types.php',
	'css_for_template.php',
	'scrap_scss_colors.php',
	'dates.php',
	'wpml.php',
	'acf.php',
	// 'carbon_fields.php',
	'shortcodes.php',
	'ajax.php',
	'custom_functions.php',
];

foreach($functionsFiles as $functionsFile)
{
	$functionFilePath = $baseFunctionsPath . $functionsFile;

	if(file_exists($functionFilePath))
	{
		include_once($functionFilePath);
	}
}

/////////////////////////////////////////////////////
/////////////////////////////////////////////////////
/////////////////////////////////////////////////////