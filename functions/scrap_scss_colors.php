<?php
$pathToColors = asset('scss/variables/colors.scss', false);

function parseScssColors($filePath)
{
	$colors = [];

	if(file_exists($filePath))
	{
		$scssContent = file_get_contents($filePath);

		preg_match_all("/'([^']+)'\s*:\s*(#[0-9A-Fa-f]{6})\s*,?/", $scssContent, $matches);

		if(isset($matches[1]) && isset($matches[2]))
		{
			$colorNames = $matches[1];
			$colorValues = $matches[2];

			foreach($colorNames as $index => $name)
			{
				$colors[$name] = $colorValues[$index];
			}
		}
	}

	return $colors;
}

$colors = parseScssColors($pathToColors);

define('PROJECT_COLORS', $colors);