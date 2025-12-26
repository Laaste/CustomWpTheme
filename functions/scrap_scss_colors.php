<?php
$pathToColors = asset('scss/variables/colors.scss', false);

function parseScssColors($filePath = '')
{
	$colors = [];

	if(file_exists($filePath))
	{
		$scss = file_get_contents($filePath);

		preg_match_all('/"([^"]+)"\s*:\s*(#[0-9A-Fa-f]{3,6}|[a-zA-Z]+)\s*,?/', $scss, $matches);

		if(!empty($matches[1]))
		{
			foreach($matches[1] as $i => $name)
			{
				$colors[$name] = $matches[2][$i]; // hex albo transparent
			}
		}
	}

	return $colors;
}

$colors = parseScssColors($pathToColors);

define('PROJECT_COLORS', $colors);