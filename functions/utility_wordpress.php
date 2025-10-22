<?php
function asset($path = '', $url = true)
{
	if(empty($path))
	{
		if($url)
		{
			return get_template_directory_uri() . '/';
		}
		else
		{
			return get_template_directory_uri() . DIRECTORY_SEPARATOR;
		}
	}
	else //abs path
	{
		if($url)
		{
			return get_template_directory_uri() . '/' . 'assets' . '/' . $path;
		}
		else
		{
			return get_template_directory() . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . $path;
		}
	}
}

function currentID()
{
	$obj = get_queried_object();

	if($obj)
	{
		if(property_exists($obj, 'term_id'))
		{
			return $obj->term_id;
		}
		else if(property_exists($obj, 'ID'))
		{
			return $obj->ID;
		}
		else
		{
			return get_the_ID();
		}
	}
	else
	{
		return get_the_ID();
	}
}

function getMenuItems($location)
{
	$menus = wp_get_nav_menus();
	$menuLocations = get_nav_menu_locations();

	if(isset($menuLocations[$location])
	&& $menuLocations[$location] != 0)
	{
		$menuID = null;
		$menuPostType = 'nav_menu';

		if(function_exists('wpml_object_id_filter'))
		{
			$menuID = wpml_object_id_filter($menuLocations[$location], $menuPostType, false);
		}
		else
		{
			$menuID = $menuLocations[$location]; // Fallback to default
		}

		foreach($menus as $menu)
		{
			if($menu->term_id == $menuID)
			{
				$menuItems = wp_get_nav_menu_items($menu);

				if(! $menuItems) 
				{
					return [];
				}

				$nestedMenuItems = buildMenuTree($menuItems);

				return $nestedMenuItems;
			}
		}
	}

	return [];
}

function buildMenuTree($menuItems, $parent_id = 0)
{
	$tree = [];

	foreach($menuItems as $menuItem)
	{
		if($menuItem->menu_item_parent == $parent_id)
		{
			$children = buildMenuTree($menuItems, $menuItem->ID);

			if(!empty($children))
			{
				$menuItem->children = $children;
			}
			else
			{
				$menuItem->children = [];
			}

			$tree[] = $menuItem;
		}
	}

	return $tree;
}

function getUploadsUrl($restOfUrl = '')
{
	$uploadsDir = wp_upload_dir();

	if(isset($uploadsDir['baseurl']))
	{
		return $uploadsDir['baseurl'] . $restOfUrl;
	}

	return $restOfUrl;
}

function shortenTitle($title, $targetLength = 150)
{
	return strlen($title) > $targetLength ? substr($title, 0, $targetLength) : $title;
}

// Wordpress names of post term has max 200 length
function makeTitle($title, $passedArgs = [])
{
	$defaultArgs = [
		'allow_tags' => false,
	];

	$args = array_merge($defaultArgs, $passedArgs);

	if(! is_string($title))
	{
		return $title;
	}

	$urlReplace = [
		"%26%23039%3B" => "%", // strange '
		"%27" => "'",
		"%24" => "$",
		"%5E" => "^",
		"%5C" => "",
		"%3A" => ":",
		"%5C%5Cn" => "",
	];

	if(! $args['allow_tags'])
	{
		$title = strip_tags($title);
	}

	$title = str_replace(['\n', '\r', '\v'], ['', '', ''], $title);
	$title = esc_sql($title);

	$title = urlencode($title);
	$title = str_replace(array_keys($urlReplace), array_values($urlReplace), $title);
	$title = urldecode($title);

	$title = trim(preg_replace('/\s\s+/', ' ', $title));
	$title = trim($title);

	return trim($title);
}

/**
 * @param array $args['lang'] wpml lang
 */
function getPostsByTitle($title, $args = [])
{
	global $wpdb;

	$defaultArgs = [
		'post_type' => 'post',
		'posts_per_page' => -1,
		'no_found_rows' => true, // Skip pagination for performance
		// 'lang' => apply_filters('wpml_current_language', NULL) //for wpml lang
	];

	$args = array_merge($defaultArgs, $args);

	$title = makeTitle($title);

	$filterFunction = function($search, $query) use($title, $wpdb, $args)
	{
		$search = $wpdb->prepare(" AND {$wpdb->posts}.post_title LIKE %s", $title);
		// $search = " AND {$wpdb->posts}.post_title LIKE {$title}";

		if(function_exists('icl_object_id'))
		{
			$lang = isset($args['lang']) ? $args['lang'] : '';

			if($lang)
			{
				$search .= " AND EXISTS
				(
					SELECT 1
					FROM {$wpdb->prefix}icl_translations AS t
					WHERE t.element_id = {$wpdb->posts}.ID
					AND t.language_code = '{$lang}'
					AND t.element_type = 'post_{$args['post_type']}'
				)";
			};
		}

		return $search;
	};

	add_filter('posts_search', $filterFunction, 10, 2);

	$query = new WP_Query($args);

	$posts = (! empty($query->posts)) ? $query->posts : [];

	remove_filter('posts_search', $filterFunction, 10, 2);

	return $posts;
}

function getTermsByTitle($title, $args = []) 
{
	if(! $title)
	{
		return [];
	}

	$defaultArgs = [
		'taxonomy' => 'category',
		'name' => $title,
		'hide_empty' => false,
		'number' => 0, // Retrieve all matching terms
	];

	$args = array_merge($defaultArgs, $args);

	$filterFunction = function($clauses, $taxQuery) use($title, $args)
	{
		global $wpdb;

		$title = makeTitle($title);

		$clauses['where'] .= $wpdb->prepare(" AND t.name LIKE %s", $title);

		if(function_exists('icl_object_id'))
		{
			$lang = isset($args['lang']) ? $args['lang'] : '';

			if($lang)
			{
				$clauses['join'] .= " INNER JOIN {$wpdb->prefix}icl_translations AS t ON t.element_id = tt.term_taxonomy_id ";
				$clauses['where'] .= $wpdb->prepare(
					" AND t.language_code = %s
					AND t.element_type = %s",
				$lang, 'tax_' . $args['taxonomy']);
			}
		}

		return $clauses;
	};

	add_filter('terms_clauses', $filterFunction, 10, 2);

	$terms = get_terms($args);

	remove_filter('terms_clauses', $filterFunction, 10, 2);

	return (! is_wp_error($terms)) ? $terms : [];
}

function findOrCreateTermByMetaField($termTitle, $taxonomy, $metaValue, $metaField)
{
	$logDir = ABSPATH . '/logs';
	$logFile = $logDir . '/findOrCreateTermByMetaField.txt';

	$term = null;

	if(! $termTitle
	|| empty($termTitle))
	{
		return $term;
	}

	$args = [
		'taxonomy' => $taxonomy,
		'hide_empty' => false,
		'meta_query' => [
			[
				'key' => $metaField,
				'value' => $metaValue,
				'compare' => '='
			]
		]
	];

	$terms = get_terms($args);

	if($terms
	&& is_array($terms)
	&& count($terms) > 0)
	{
		$term = reset($terms);
	}
	else
	{
		$termArray = wp_insert_term($termTitle, $taxonomy);

		if(is_array($termArray))
		{
			$term = get_term($termArray['term_id'], $taxonomy);

			update_tax_field('term_published', $metaValue, $taxonomy, $term->term_id);
			update_tax_field($metaField, $metaValue, $taxonomy, $term->term_id);
		}
		else //WP Error Object
		{
			$errorCode = $termArray->get_error_code();

			if($errorCode === 'term_exists')
			{
				$suffix = '-new';
				$newTermTitle = $termTitle . $suffix;
				$counter = 1;

				while(term_exists($newTermTitle, $taxonomy))
				{
					$counter++;

					$newTermTitle = $termTitle . $suffix . '-' . $counter;
				}

				$termArray = wp_insert_term($newTermTitle, $taxonomy);

				if(! is_wp_error($termArray))
				{
					$term = get_term($termArray['term_id'], $taxonomy);

					update_tax_field('term_published', $metaValue, $taxonomy, $term->term_id);
					update_tax_field($metaField, $metaValue, $taxonomy, $term->term_id);
				}
			}
			else
			{
				$logMessage = date('Y-m-d H:i:s') . " - Term findOrCreateTermByMetaField error on title: {$termTitle} term fulltitle: {$metaValue} error: " . json_encode($termArray) . PHP_EOL;

				file_put_contents($logFile, $logMessage, FILE_APPEND);
			}
		}
	}

	wp_reset_postdata();

	return $term;
}

function addTermToPost($postId, $taxonomy, $termId) 
{
	$postTerms = wp_get_post_terms($postId, $taxonomy, ['fields' => 'ids']);

	if(! in_array($termId, $postTerms))
	{
		$postTerms[] = $termId;

		wp_set_post_terms($postId, $postTerms, $taxonomy);
	}
}

/**
 * This function doesn't check if similar file already was downloaded so will make multiple entities
 */
function addMediaFromUrlAsFeaturedImage($imageUrl, $postIds)
{
	if(! function_exists('media_handle_sideload'))
	{
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';
	}

	$fileName = basename($imageUrl);
	$fileName = preg_replace("/([?#].*)$/", '', $fileName); // remove get ande id

	$tempFile = download_url($imageUrl);

	if(is_wp_error($tempFile))
	{
		error_log('Error downloading image: ' . $tempFile->get_error_message());
		return false;
	}

	$fileData = [
		'name' => $fileName,
		'tmp_name' => $tempFile,
	];

	$firstPostId = $postIds;

	if(is_array($firstPostId))
	{
		$firstPostId = reset($firstPostId);
	}

	$attachmentId = media_handle_sideload($fileData, $firstPostId);

	if(is_wp_error($attachmentId))
	{
		error_log('Error sideloading image: ' . $attachmentId->get_error_message());

		@unlink($tempFile);

		return false;
	}

	if(is_array($postIds))
	{
		foreach($postIds as $postId)
		{
			set_post_thumbnail($postId, $attachmentId);
		}
	}
	else
	{
		$postId = $postIds;

		set_post_thumbnail($postId, $attachmentId);
	}

	return $attachmentId;
}

function addMediaFromUrlToLibrary($url, $getUrl = false)
{
	if(! function_exists('media_handle_sideload'))
	{
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';
	}

	$tmpFile = download_url($url);

	if(is_wp_error($tmpFile))
	{
		error_log('Failed to download ' . $url);

		return false;
	}

	$fileName = basename($url);

	$wpUploadDir = wp_upload_dir();

	$filePath = $wpUploadDir['path'] . '/' . $fileName;

	rename($tmpFile, $filePath);

	$file_type = wp_check_filetype($fileName, null);

	$attachment = [
		'post_mime_type' => $file_type['type'],
		'post_title' => sanitize_file_name($fileName),
		'post_content' => '',
		'post_status' => 'inherit',
	];

	$attachId = wp_insert_attachment($attachment, $filePath);

	$attachData = wp_generate_attachment_metadata($attachId, $filePath);
	wp_update_attachment_metadata($attachId, $attachData);

	if($getUrl)
	{
		return wp_get_attachment_url($attachId);
	}
	else
	{
		return $attachId;
	}
}

/** 
 * Note this also catch with sub domains
 */
function replaceLinkWithDownloadedToMedia($string, $linksToIgnore = [])
{
	$regex = '/[^\s"\']*webflow\.com[^\s"\']*/i';

	$newString = preg_replace_callback($regex, function($matches) use ($linksToIgnore)
	{
		$match = reset($matches);

		if(in_array($match, $linksToIgnore))
		{
			return $match;
		}

		$newFileUrl = addMediaFromUrlToLibrary($match, true);

		return $newFileUrl ?: $match;
	}, $string);

	return $newString;

	// $tests = [
	// 	"webflow.com test 0",
	// 	"sub.webflow.com test 0",

	// 	"test 1 webflow.com",
	// 	"test 1 sub.webflow.com",
	// 	"test 2 webflow.com/test",
	// 	"test 2 sub.webflow.com/test",
	// 	"test 3 webflow.com/test.jpg dalszy text",
	// 	"test 3 sub.webflow.com/test.jpg dalszy text",
	// 	"test 4 webflow.com/test.jpgbezspacji", //this should fail
	// 	"test 4 sub.webflow.com/test.jpgbezspacji", //this should fail

	// 	"test 5 webflow.com\"",
	// 	"test 5 sub.webflow.com\"",
	// 	"test 6 webflow.com/test\"",
	// 	"test 6 sub.webflow.com/test\"",
	// 	"test 7 webflow.com/test.jpg dalszy text\"",
	// 	"test 7 sub.webflow.com/test.jpg dalszy text\"",
	// 	"test 8 webflow.com/test.jpgbezspacji\"", //this should fail
	// 	"test 8 sub.webflow.com/test.jpgbezspacji\"", //this should fail

	// 	"test 9 webflow.com'",
	// 	"test 9 sub.webflow.com'",
	// 	"test 10 webflow.com/test'",
	// 	"test 10 sub.webflow.com/test'",
	// 	"test 11 webflow.com/test.jpg dalszy text'",
	// 	"test 11 sub.webflow.com/test.jpg dalszy text'",
	// 	"test 12 webflow.com/test.jpgbezspacji'", //this should fail
	// 	"test 12 sub.webflow.com/test.jpgbezspacji'", //this should fail
	// ];

	// foreach($tests as $test)
	// {
	// 	preg_match_all($regex, $test, $matches);

	// 	dump($test);
	// 	dump($matches[0]);

	// 	echo '<br>';
	// }
}

// if(isset($_GET['reptest']))
// {
// 	storePostContentLink();
// }

function storePostContentLink()
{
	global $wpdb;

	$externalDomainsToStore = [
		'uploads-ssl.webflow.com',
	];

	$linksToIgnore = [

	];

	foreach($externalDomainsToStore as $externalDomain)
	{
		$postsWithExternalLink = $wpdb->get_results("SELECT ID, post_content
			FROM {$wpdb->posts}
			WHERE post_content LIKE \"%{$externalDomain}%\"
		");

		foreach($postsWithExternalLink as $postWithExternalLink)
		{
			$newPostContent = replaceLinkWithDownloadedToMedia($postWithExternalLink->post_content, $linksToIgnore);

			if($newPostContent)
			{
				wp_update_post(
				[
					'ID' => $postWithExternalLink->ID,
					'post_content' => $newPostContent,
				]);
			}
		}
	}
}

/**
 * NOTE: IT MAY REMOVE MORE NEED TO CHECK BEFORE USE
 * After use check if it correctly deleted only duplicate images
 */
function removeDuplicatedMedia()
{
	// Check if WordPress is loaded, if not, include it
	if(! defined('ABSPATH'))
	{
		$path = dirname(__FILE__);
		while(! file_exists($path . '/wp-load.php')
		&& $path !== dirname($path))
		{
			$path = dirname($path);
		}

		if(file_exists($path . '/wp-load.php'))
		{
			require_once $path . '/wp-load.php';
		}
		else
		{
			die("Error: Could not find wp-load.php");
		}
	}

	global $wpdb;

	// Define log file location inside the 'logs' directory
	$logDir = ABSPATH . 'logs/';
	$logFile = $logDir . 'duplicate_media_cleanup.log';
	
	// Ensure logs directory exists
	if(! is_dir($logDir))
	{
		die("Error: Logs directory does not exist at $logDir");
	}
	
	// Write log header
	file_put_contents($logFile, "Duplicate Media Cleanup - " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
	
	// Find duplicate media entries
	$duplicates = $wpdb->get_results("SELECT post_title, COUNT(*) as count
		FROM {$wpdb->posts}
		WHERE post_type = 'attachment'
		GROUP BY post_title
		HAVING count > 1
	");
	
	foreach($duplicates as $duplicate)
	{
		$title = $duplicate->post_title;

		$attachments = $wpdb->get_results($wpdb->prepare("SELECT ID, guid, post_name
			FROM {$wpdb->posts}
			WHERE post_type = 'attachment' AND post_title = %s
			ORDER BY ID ASC
		", $title));

		if(count($attachments) > 1)
		{
			$original = array_shift($attachments); // Keep the first one
			$originalUrl = $original->guid;

			file_put_contents($logFile, "Found duplicate: {$title}. Keeping original: {$original->ID}\n", FILE_APPEND);

			foreach($attachments as $dup)
			{
				$dupId = $dup->ID;
				$dupUrl = $dup->guid;

				// Replace references in post content
				$wpdb->query($wpdb->prepare("
					UPDATE {$wpdb->posts}
					SET post_content = REPLACE(post_content, %s, %s)
				", $dupUrl, $originalUrl));

				// Replace references in post meta
				$wpdb->query($wpdb->prepare("
					UPDATE {$wpdb->postmeta}
					SET meta_value = REPLACE(meta_value, %s, %s)
				", $dupUrl, $originalUrl));

				// Log the replacement
				file_put_contents($logFile, "Replaced references of $dupUrl with $originalUrl\n", FILE_APPEND);

				// Delete the duplicate media entry
				wp_delete_attachment($dupId, true);
				file_put_contents($logFile, "Deleted duplicate attachment ID: $dupId\n", FILE_APPEND);
			}
		}
	}
	
	file_put_contents($logFile, "Cleanup completed!\n", FILE_APPEND);

	echo "Duplicate media cleanup completed. Check log file: $logFile";
}

function getParentChildren($currentId)
{
	$page = get_post($currentId); 

	if(! $page)
	{
		return [];
	}

	$parentId = $page->post_parent;

	if($parentId)
	{
		$childrenArgs = [
			'post_parent' => $parentId,
			'post_type' => 'page',
			'post_status' => 'publish',
			'orderby' => 'title',
			'order' => 'ASC',
		];

		return get_children($childrenArgs);
	}
	else
	{
		return [];
	}
}

function getPostsCount($args = [])
{
	$excludeIds = $args['post__not_in'] ?? [];

	$getModelsCountArgs = [
		'post_type' => 'post',
		'post_status' => 'publish',
		'posts_per_page' => -1,
		'post__not_in' => $excludeIds,
		'fields' => 'ids', // only get IDs, faster
	];

	$query = new WP_Query($getModelsCountArgs);
	return $query->found_posts;
}