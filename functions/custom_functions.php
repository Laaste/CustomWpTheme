<?php
register_nav_menus(
[
	'header_menu' => 'Menu w headerze',
	'footer_menu' => 'Menu w stopce',
]);

//////////////////////////////////////////////

function customSearch($searchInput)
{
	$searchStr = sanitize_text_field($searchInput);

	$foundItems = [];
	$foundItemsIds = [];

	$firstLoop = true;

	$shortenedStr = $searchStr;

	while(count($foundItems) < 10
	&& strlen($shortenedStr) >= 3) // more then 3 so it be still cut to 2
	{
		if($firstLoop)
		{
			$firstLoop = false;
		}
		else
		{
			$shortenedStr = substr_replace($shortenedStr, '', -1);
		}

		$searchItems = customSearchGet($shortenedStr, [
			'post__not_in' => $foundItemsIds,
		]);

		foreach($searchItems as $searchItem)
		{
			if(! in_array($searchItem['id'], $foundItemsIds))
			{
				$foundItems[] = $searchItem;
				$foundItemsIds[] = $searchItem['id'];
			}
		}
	}

	return array_slice($foundItems, 0, 10);
}

function customSearchGet($searchStr, $args = [])
{
	global $wpdb;

	$postTypes = [
		'post',
		'page',
	];

	$currentLang = apply_filters('wpml_current_language', NULL);

	$sql = $wpdb->prepare("
		SELECT p.ID, p.post_title, p.post_type, p.post_content
		FROM {$wpdb->posts} p
		JOIN {$wpdb->prefix}icl_translations t 
			ON p.ID = t.element_id 
			AND t.element_type = CONCAT('post_', p.post_type)
		WHERE p.post_status = 'publish'
		AND (
			p.post_title LIKE %s
			OR p.post_title LIKE %s
			OR p.post_content LIKE %s
			OR p.post_content LIKE %s
		)
		AND p.post_type IN (" . implode(", ", array_map(fn($postType) => "'" . $postType . "'", $postTypes)) . ")
		AND t.language_code = %s
		ORDER BY p.post_date DESC
		LIMIT 10
	", $searchStr . '%', '% ' . $searchStr . '%', $searchStr . '%', '% ' . $searchStr . '%', $currentLang);

	$results = $wpdb->get_results($sql);

	if($results
	&& is_array($results))
	{
		foreach($results as $postResult)
		{
			$foundItems[] = [
				'id' => $postResult->ID,
				'title' => $postResult->post_title,
				'content' => $postResult->post_content,
				'link' => get_permalink($postResult->ID),
				'post_type' => $postResult->post_type,
				'phrase' => $searchStr,
			];
		}
	}

	wp_reset_postdata();

	return $foundItems;
}

/*
 * Problem z wyszukiwaniem polskich znaków \b szukając "konferencje" w kontrahentów łamie się na <strong>kontrahent</strong>ów
 */
function strongerSearchPhrase($contentOrginal, $searchPhrase, $forceFullText = false)
{
	$content = $contentOrginal;

	// 1. Remove HTML tags but keep HTML entities
	$content = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
	$content = trim(preg_replace('/<[^>]*>/', ' ', $content));
	$content = preg_replace('/\s+/', ' ', $content);

	// 2. Find all occurrences of the search phrase
	$matches = strMatchFromStartPartial($searchPhrase, $content);

	$transformedContent = '';
	$targetedWordsCount = 23;

	$startPos = null;
	$endPos = null;

	if($forceFullText)
	{
		$words = explode(' ', $content);
		$lastWordIndex = array_key_last($words);

		foreach($words as $wordKey => $word)
		{
			if(stripos($word, $searchPhrase) === 0)
			{
				$transformedContent .= '<strong>' . $word . '</strong>';
			}
			else
			{
				$transformedContent .= $word;
			}

			if($lastWordIndex != $wordKey)
			{
				$transformedContent .= ' ';
			}
		}
	}
	else
	{
		foreach($matches as $match)
		{
			$matchStartPos = $match['offset'];

			if($startPos == null
			|| $startPos > $matchStartPos)
			{
				$startPos = $matchStartPos;
			}

			$matchEndPos = $matchStartPos + strlen($match['match']);

			if($endPos == null
			|| $endPos < $matchEndPos)
			{
				$endPos = $matchEndPos;
			}
		}

		$cuttedContent = substr($content, $startPos, $endPos - $startPos);

		$cuttedWordsCount = count(explode(' ', $cuttedContent));

		// transformed have to be after counting words to dont count strong
		$cuttedWords = explode(' ', $cuttedContent);
		$lastWordIndex = array_key_last($cuttedWords);

		foreach($cuttedWords as $wordKey => $word)
		{
			if(stripos($word, $searchPhrase) === 0)
			{
				$transformedContent .= '<strong>' . $word . '</strong>';
			}
			else
			{
				$transformedContent .= $word;
			}

			if($lastWordIndex != $wordKey)
			{
				$transformedContent .= ' ';
			}
		}

		$transformedContent = trim($transformedContent);

		if($cuttedWordsCount < $targetedWordsCount)
		{
			$getWordsBefore = floor($targetedWordsCount / 2) + $targetedWordsCount % 2;
			$getWordsAfter = floor($targetedWordsCount / 2);

			$contextBefore = substr($content, 0, $startPos);
			$contextAfter = substr($content, $endPos);

			// Get words before and after the matches
			$wordsBefore = array_reverse(explode(' ', $contextBefore));
			$wordsAfter = explode(' ', $contextAfter);

			$contextBefore = implode(' ', array_reverse(array_slice($wordsBefore, 0, $getWordsBefore)));
			$contextAfter = implode(' ', array_slice($wordsAfter, 0, $getWordsAfter));

			// fill transformedContent with before and after words
			$transformedContent = trim($contextBefore) . ' ' . $transformedContent . ' ' . trim($contextAfter);
		}
	}

	return $transformedContent;
}

/*
 * for search "kon"
 * 
 * konfre -> "konfre"
 * konferencje -> "konferencje"
 * kontrahentów -> "kontrahentów"
 * kon -> "kon"
 * 
 * doskonały -> not matching
 * on -> not matching
 * ko -> not matching
 * 
 * kon&nbsp;kob -> "kon"
 */
function strMatchFromStartPartial($searchPhrase, $content)
{
	$matches = [];
	$searchLength = strlen($searchPhrase);
	$offset = 0;

	// Loop through content to find occurrences of the search phrase
	while(($position = stripos($content, $searchPhrase, $offset)) !== false)
	{
		$startPos = $position;
		$endPos = $position + $searchLength;

		// Ensure the match starts at the beginning of the word (not in the middle)
		// Check if previous character is not a word character (or start of string)
		if ($startPos > 0
		&& ctype_alnum($content[$startPos - 1]))
		{
			$offset = $startPos + 1;

			continue; // Skip to the next potential match
		}

		// Continue checking until we encounter invalid characters: '&', ';', or whitespace
		while ($endPos < strlen($content)
		&& ! in_array($content[$endPos], ['&', ';', ' ']))
		{
			$endPos++;
		}

		// Capture the match and its position, including partial matches
		$matches[] = [
			'match' => substr($content, $startPos, $endPos - $startPos),
			'offset' => $startPos
		];

		// Update offset to continue searching after the current match
		$offset = $endPos;
	}

	return $matches;
}

// function customMetaTitle($title)
// {
// 	$metaTitleForLang = get_field('global_metadatas_lang_title', 'option');

// 	if($metaTitleForLang
// 	&& ! empty($metaTitleForLang))
// 	{
// 		$titleParts = explode('-', $title);

// 		$firstPartKey = array_key_first($titleParts);
// 		$lastPartKey = array_key_last($titleParts);

// 		$newTitle = '';

// 		$titlePartsCount = count($titleParts);

// 		foreach($titleParts as $titlePartKey => $titlePart)
// 		{
// 			if($titlePartKey == $firstPartKey)
// 			{
// 				if($titlePartsCount == 1)
// 				{
// 					$newTitle = $metaTitleForLang;
// 				}
// 				else
// 				{
// 					$newTitle = $titlePart;
// 				}
// 			}
// 			else if($titlePartKey != $lastPartKey)
// 			{
// 				$newTitle .= ' - ' . $titlePart;
// 			}
// 			else
// 			{
// 				$newTitle .= ' - ' . $metaTitleForLang;
// 			}
// 		}

// 		return $newTitle;
// 	}
// 	else
// 	{
// 		return $title;
// 	}
// }

// function customMetaDescription($description)
// {
// 	$metaDescForLang = get_field('global_metadatas_lang_slag', 'option');

// 	if(
// 	(	
// 		! $description
// 		|| !empty($description)
// 	)
// 	&& $metaDescForLang
// 	&& ! empty($metaDescForLang))
// 	{
// 		return $metaDescForLang;
// 	}
// 	else
// 	{
// 		return $description;
// 	}
// }

// add_filter('wpseo_title', 'customMetaTitle', 10, 1);
// add_filter('wpseo_metadesc', 'customMetaDescription', 10, 1);

///////////////////////////////////////
///////////////////////////////////////

// function hideAcfFields() 
// {
// 	$fieldsNamesToHide = [
// 		'job_deadline',
// 		'job_approver_administration',
// 		'job_approver_financial',
// 		'job_creator',
// 		'job_creator_email',
// 		'job_lead',
// 		'job_responsibilities',
// 		'job_salary',
// 		'job_sent_for_approval',
// 		'job_status',
// 		'job_guid',
// 	];

// 	echo "<script>
// 		jQuery(document).ready(function($)
// 		{
// 			var fieldsToHide = " . json_encode($fieldsNamesToHide) . ";

// 			fieldsToHide.forEach(function(field)
// 			{
// 				$('.acf-field[data-name=\"' + field + '\"]').hide();
// 			});
// 		});
// 	</script>";
// }
// add_action('admin_footer', 'hideAcfFields');



//////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////

function customFixLinksFromOldWebsite()
{
	global $wpdb;

	// $oldValue = 'test.pl/news/';
	// $newValue = 'testnew.pl/aktualnosci/';

	/**
	 * Didnt work for konferencja for some reasons
	 */
	// $oldValue = 'test.pl/konferencja/';
	// $newValue = 'testnew.pl/konferencja/';

	//////////////////////////////////////
	// Exact for post meta
	// $postMetas = $wpdb->get_results("SELECT meta_id, post_id, meta_key, meta_value
	// FROM {$wpdb->postmeta}
	// WHERE meta_value LIKE \"%{$oldValue}%\"
	// ");

	// dd($postMetas); // keep checking after update cause update_post_meta sometimes doesnt work
	
	// foreach($postMetas as $postMeta)
	// {
	// 	$procceed = str_ireplace($oldValue, $newValue, $postMeta->meta_value);
		
	// 	update_post_meta($postMeta->post_id, $postMeta->meta_key, $procceed);

	// 	dd(get_post_meta($postMeta->post_id, $postMeta->meta_key));
	// }

	//////////////////////////////////////
	// Generic for post meta
	// $oldValue = 'test.pl';
	// $newValue = 'testnew.pl';

	// $postMetas = $wpdb->get_results("SELECT meta_id, post_id, meta_key, meta_value
	// FROM {$wpdb->postmeta}
	// WHERE meta_value LIKE \"%{$oldValue}%\"
	// ");

	// foreach($postMetas as $postMeta)
	// {
	// 	$procceed = preg_replace_callback('/(.{0,4})(test\.pl)/i', function($matches) use($postMeta, $newValue)
	// 	{
	// 		// 0 full match
	// 		$prefix = $matches[1]; // http / https / www / or subdomain
	// 		$match = $matches[2];

	// 		// Skip mails
	// 		if(str_ends_with($prefix, '@'))
	// 		{
	// 			return $prefix . $match;
	// 		}

	// 		// Skip subdomains
	// 		if($prefix != 'www.'
	// 		&& $prefix != 'p://'
	// 		&& $prefix != 's://'
	// 		&& $prefix != null
	// 		&& $prefix != '')
	// 		{
	// 			return $prefix . $match;
	// 		}

	// 		$replaced = $prefix . $newValue;

	// 		// dump($postMeta->meta_value); // check manually after replace cause it skips 10 rows for some reasons

	// 		// dump(str_replace($prefix . $match, $replaced, $postMeta->meta_value)); // corrected version
	// 		// echo "<br><br>";

	// 		return $replaced;
	// 	}, $postMeta->meta_value);

	// 	// update_post_meta($postMeta->post_id, $postMeta->meta_key, $procceed);
	// }
	
	//////////////////////////////////////
	// Generic for post content
	$oldValue = 'test.pl';
	$newValue = 'testnew.pl';

	$postsMatched = $wpdb->get_results("SELECT ID, post_content
	FROM {$wpdb->posts}
	WHERE post_content LIKE \"%{$oldValue}%\"
	");

	foreach($postsMatched as $postObj)
	{
		$procceed = preg_replace_callback('/(.{0,4})(test\.pl)/i', function($matches) use($postObj, $newValue)
		{
			// 0 full match
			$prefix = $matches[1]; // http / https / www / or subdomain
			$match = $matches[2];

			// Skip mails
			if(str_ends_with($prefix, '@')) // ends with not contains cause it may be "@m.pl"
			{
				return $prefix . $match;
			}

			if($prefix == 'www.'
			|| $prefix == 'p://' //http
			|| $prefix == 's://' //https
			|| empty($prefix) // null or empty string
			|| preg_match('/\s$/', substr($prefix, -1))) // last char in prefix is whitepasce
			{
				return $prefix . $newValue;
			}
			else // Skip subdomains
			{
				return $prefix . $match;
			}
		}, $postObj->post_content);

		wp_update_post(
		[
			'ID' => $postObj->ID,
			'post_content' => $procceed,
		]);
	}

	die('koniec');
}

function pageRedirect($currentId)
{
	$activeRedirect = get_field('page_redirect_active', $currentId);

	if($activeRedirect)
	{
		$redirectObj = get_field('page_redirect', $currentId);

		if($redirectObj)
		{
			$redirectLink = get_permalink($redirectObj->ID);

			header('Location: ' . $redirectLink, true, 302);

			die();
			exit();
		}
	}
}

// add_filter('map_meta_cap', 'modify_taxonomy_role_capabilities', 10, 4);
// function modify_taxonomy_role_capabilities($caps, $cap, $user_id, $args)
// {
// 	// Define the taxonomies you want to modify
// 	$taxonomiesToModify = [
// 		'model-category',
// 	];

// 	// Check if the current capability is related to one of the taxonomies
// 	if (in_array($cap, ['manage_terms', 'edit_terms', 'delete_terms', 'assign_terms'])) {
// 		// Check if the capability is for a registered taxonomy
// 		foreach ($taxonomiesToModify as $taxonomy) {
// 			if (taxonomy_exists($taxonomy)) {
// 				// Check if the user has the capability for the taxonomy
// 				if (isset($args[0]) && $args[0] === $taxonomy) {
// 					// Modify the capabilities based on your needs
// 					// For example, we want to make sure these capabilities apply to specific roles
// 					if ($cap === 'manage_terms') {
// 						$caps[] = 'manage_categories'; // Can manage terms
// 					} elseif ($cap === 'edit_terms') {
// 						$caps[] = 'edit_others_terms'; // Can edit terms
// 					} elseif ($cap === 'delete_terms') {
// 						$caps[] = 'edit_others_terms'; // Can delete terms
// 					} elseif ($cap === 'assign_terms') {
// 						$caps[] = 'edit_terms'; // Can assign terms
// 					}
// 				}
// 			}
// 		}
// 	}

// 	return $caps;
// }

// add_filter( 'webpc_use_htaccess_redirect', '__return_true' );