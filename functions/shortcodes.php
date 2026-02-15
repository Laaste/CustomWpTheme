<?php
$shorcodesFiles = [
	'sc_date.php',
	'sc_current_id.php',
	'sc_br_tag.php',
	'sc_link.php',
	'sc_info.php',
	'sc_get_permalink.php',
];

foreach($shorcodesFiles as $file)
{
	include_once(__DIR__ . DIRECTORY_SEPARATOR . 'shortcodes' . DIRECTORY_SEPARATOR . $file);
}