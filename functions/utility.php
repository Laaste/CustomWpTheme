<?php
function pre($toDump) {
	echo '<pre style="position: relative; z-index: 9999; margin: 0; color: #fff; background-color: #000;">';
	var_dump($toDump);
	echo '</pre>';
}

function dd($obj)
{
	dump($obj);

	exit;
}

function dump($obj)
{
	echo '<pre style="background-color: #000000 !important; color: #ffffff !important; position: relative; z-index: 100000; text-wrap: wrap;">';
		var_dump($obj);
	echo '</pre>';
}

function uuid()
{
	$data = random_bytes(16);

	$data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
	$data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

	return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

function response($data, $status = 'success')
{
	$response = json_encode([
		'status' => $status,
		'data' => $data,
	]);

	header('Content-Type: application/json');

	echo $response;

	exit;
}

function getCurrentUrl()
{
	$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
	$host = $_SERVER['HTTP_HOST'];
	$uri = $_SERVER['REQUEST_URI'];

	return $scheme . '://' . $host . $uri;
}

function addQueryParamToUrl($url, $key, $value)
{
	$parsedUrl = parse_url($url);

	
	$query = isset($parsedUrl['query']) ? $parsedUrl['query'] : '';

	parse_str($query, $queryParams);

	$queryParams[$key] = $value;

	$newQuery = http_build_query($queryParams);

	$finalUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'] .
		(isset($parsedUrl['port']) ? ':' . $parsedUrl['port'] : '') .
		(isset($parsedUrl['path']) ? $parsedUrl['path'] : '') .
		'?' . $newQuery .
		(isset($parsedUrl['fragment']) ? '#' . $parsedUrl['fragment'] : '');

	return $finalUrl;
}

function arrayRecursiveMerge($array1, $array2)
{
	if(is_array($array2))
	{
		if(isset($array1)
		&& is_array($array1))
		{
			$array1 = [];
		}

		foreach($array2 as $key => $value)
		{
			if(is_array($value))
			{
				if(isset($array1[$key])
				&& is_array($array1[$key]))
				{
					$array1[$key] = arrayRecursiveMerge($array1[$key], $array2[$key]);
				}
				else
				{
					$array1[$key] = $array2[$key];
				}
			}
			else
			{
				$array1[$key] = $value;
			}
		}

		return $array1;
	}
	else
	{
		return $array2;
	}
}

function arraySortByItemField($items, $fieldName, $asc = true, $stripHtmlTags = false)
{
	$localItems = $items;

	$compareByField = function($a, $b) use ($fieldName, $asc, $stripHtmlTags)
	{
		$valueA = $a[$fieldName];
		$valueB = $b[$fieldName];

		if($stripHtmlTags)
		{
			$valueA = strip_tags($valueA);
			$valueB = strip_tags($valueB);
		}

		$valueA = strtolower($valueA);
		$valueB = strtolower($valueB);

		if($asc)
		{
			return strcmp($valueA, $valueB);
		}
		else
		{
			return strcmp($valueB, $valueA);
		}
	};

	usort($localItems, $compareByField);

	return $localItems;
}

function sortArray(array $array, bool $asc = true): array {
	// Define a custom order for Polish characters
	$polishAlphabet = 'aąbcćdeęfghijklłmnńoópqrsśtuvwxyzźż';

	usort($array, function ($a, $b) use ($asc, $polishAlphabet) {
		$a = mb_strtolower($a, 'UTF-8');
		$b = mb_strtolower($b, 'UTF-8');

		$length = min(mb_strlen($a, 'UTF-8'), mb_strlen($b, 'UTF-8'));

		for ($i = 0; $i < $length; $i++) {
			$posA = mb_strpos($polishAlphabet, mb_substr($a, $i, 1, 'UTF-8'));
			$posB = mb_strpos($polishAlphabet, mb_substr($b, $i, 1, 'UTF-8'));
			
			if ($posA !== $posB) {
				return $asc ? $posA - $posB : $posB - $posA;
			}
		}

		// If the strings are identical up to the length of the shorter string, compare lengths
		return $asc ? mb_strlen($a, 'UTF-8') - mb_strlen($b, 'UTF-8') : mb_strlen($b, 'UTF-8') - mb_strlen($a, 'UTF-8');
	});

	return $array;
}

function sortArrayByKeys(array $array, bool $asc = true): array 
{
	$polishAlphabet = 'aąbcćdeęfghijklłmnńoópqrsśtuvwxyzźż';

	uksort($array, function ($a, $b) use ($asc, $polishAlphabet) {
		$a = mb_strtolower($a, 'UTF-8');
		$b = mb_strtolower($b, 'UTF-8');

		$length = min(mb_strlen($a, 'UTF-8'), mb_strlen($b, 'UTF-8'));

		for ($i = 0; $i < $length; $i++) {
			$charA = mb_substr($a, $i, 1, 'UTF-8');
			$charB = mb_substr($b, $i, 1, 'UTF-8');

			$posA = mb_strpos($polishAlphabet, $charA);
			$posB = mb_strpos($polishAlphabet, $charB);

			// If either character is not in the alphabet, fallback to ASCII order
			if ($posA === false) $posA = ord($charA);
			if ($posB === false) $posB = ord($charB);

			if ($posA !== $posB) {
				return $asc ? $posA - $posB : $posB - $posA;
			}
		}

		// If the strings are identical up to the length of the shorter string, compare lengths
		return $asc ? mb_strlen($a, 'UTF-8') - mb_strlen($b, 'UTF-8') : mb_strlen($b, 'UTF-8') - mb_strlen($a, 'UTF-8');
	});

	return $array;
}

function getFloat($number)
{
	$number = preg_replace('/\s/', '', $number); //remove whitespace

	preg_match_all('/\d*(?:\.)?(?:\d*)?/', $number, $matches); //catch digits optional dot and digits

	if(
		$matches
		&& count($matches)
		&& $matches[0]
		&& count($matches[0])
	){
		return $matches[0][0];
	}
	else
	{
		return '';
	}
}
