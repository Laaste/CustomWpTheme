<?php
$shorcodesFiles = [
	'sc_br_tag.php',
	'sc_str_current_year.php',
	'sc_link.php',
	'sc_info.php',
	'sc_get_permalink.php',
];

foreach($shorcodesFiles as $file)
{
	include_once(__DIR__ . DIRECTORY_SEPARATOR . 'shortcodes' . DIRECTORY_SEPARATOR . $file);
}