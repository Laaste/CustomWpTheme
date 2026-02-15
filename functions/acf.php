<?php
remove_filter('acf_the_content', 'wpautop');

function disable_wpautop_for_acf_wysiwyg( $value, $post_id, $field )
{
	remove_filter( 'acf_the_content', 'wpautop' );
	remove_filter( 'the_content', 'wpautop' );
	remove_filter( 'acf/format_value/type=wysiwyg', 'wpautop' );
	return $value;
}
add_filter( 'acf/format_value/type=wysiwyg', 'disable_wpautop_for_acf_wysiwyg', 10, 3 );


function get_tax_field($fieldName, $id, $taxonomy)
{
	return get_field($fieldName, $taxonomy . '_' . $id);
}

function update_tax_field($fieldName, $value, $taxonomy, $termID)
{
	update_field($fieldName, $value, $taxonomy . '_' . $termID);
}

/**
 * Disable editing of field
 */
// function disable_acf_load_field($field)
// {
// 	$field['disabled'] = 1;
// 	return $field;
// }
// add_filter('acf/load_field/name=event_ai', 'disable_acf_load_field');

/**
 * Get exact field value of item by this item other field value
 */
function acfGetValueByValue($items, $fieldNameToGetValue, $conditionalFieldName, $conditionFieldValue)
{
	$value = null;

	foreach($items as $item)
	{
		if(
			isset($item[$conditionalFieldName])
			&& $item[$conditionalFieldName] === $conditionFieldValue
		){
			if(isset($item[$fieldNameToGetValue]))
			{
				$value = $item[$fieldNameToGetValue];
			}

			break;
		}
	}

	return $value;
}

/**
 * Get custom value by alias (stored in settings -> Pola niestandardowe)
 */
function acfInfo($alias)
{
	$acfIdent = 'option';
	$itemsFieldName = 'info-items'; // repeater
	$aliasFieldName = 'info-item-alias'; //selector (text field nested in repeater)
	$valueFieldName = 'info-item-content'; //value (wyswig field nested in repeater)

	$items = acfGetRepeaterItems($itemsFieldName, $acfIdent);

	return acfGetValueByValue($items, $valueFieldName, $aliasFieldName, $alias);
}

/**
 * Get link url by alias (stored in settings -> Pola niestandardowe)
 */
function acfLink($alias)
{
	$acfIdent = 'option';
	$itemsFieldName = 'link-items'; // repeater
	$aliasFieldName = 'link-item-alias'; //selector (text field nested in repeater)

	if($alias == 'news-page')
	{
		$items = acfGetRepeaterItems($itemsFieldName, $acfIdent, '', true);
	}
	else
	{
		$items = acfGetRepeaterItems($itemsFieldName, $acfIdent);
	}

	$linkObj = null;

	foreach($items as $item)
	{
		if(isset($item[$aliasFieldName])
		&& $item[$aliasFieldName] == $alias)
		{
			$linkObj = $item;
		}
	}

	return $linkObj;
}

/**
 * Get link url by alias (stored in settings -> Pola niestandardowe)
 */
function acfLinkUrl($alias)
{
	$acfIdent = 'option';
	$itemsFieldName = 'link-items'; // repeater
	$aliasFieldName = 'link-item-alias';
	$valueFieldName = 'link-item-href';
	$pageFieldName = 'link-item-page';

	$items = acfGetRepeaterItems($itemsFieldName, $acfIdent);
	$link = acfGetValueByValue($items, $valueFieldName, $aliasFieldName, $alias);

	if(empty($link))
	{
		$page = acfGetValueByValue($items, $pageFieldName, $aliasFieldName, $alias);

		if(is_array($page))
		{
			$page = reset($page);
		}

		if($page)
		{
			$link = get_permalink($page);
		}
	}

	return $link;
}

function acfGetRepeaterItems($mainField, $acfIdent)
{
	$items = [];

	if(have_rows($mainField, $acfIdent))
	{
		$repeater_field = get_field_object($mainField, $acfIdent);
		if(!$repeater_field || empty($repeater_field['sub_fields'])) return $items;

		while(have_rows($mainField, $acfIdent))
		{
			the_row();

			$item = [];

			foreach($repeater_field['sub_fields'] as $sub_field)
			{
				$key = $sub_field['name'];
				$item[$key] = get_sub_field($key);
			}

			$items[] = $item;
		}
	}

	return $items;
}

function acfGetSelectValue($fieldName, $postId)
{
	$field = get_field_object($fieldName, $postId);

	if($field
	&& isset($field['choices']))
	{
		$value = get_field($fieldName, $postId);

		return isset($field['choices'][ $value ]) ? $field['choices'][ $value ] : null;
	}

	return null;
}

function acfAddRowToRepeater($postId, $repeaterFieldName, $rowData)
{
	$repeaterRows = get_field($repeaterFieldName, $postId) ?: [];

	$repeaterRows[] = $rowData;

	return update_field($repeaterFieldName, $repeaterRows, $postId);
}

function acfAddToRelationField($postId, $fieldName, $relatedPostId)
{
	$relatedPosts = get_field($fieldName, $postId) ?: [];

	$relatedPostsIds = [];

	if($relatedPosts)
	{
		foreach($relatedPosts as $relatedPost)
		{
			$relatedPostsIds[] = $relatedPost->ID;
		}
	}

	if(! in_array($relatedPostId, $relatedPostsIds))
	{
		$relatedPostsIds[] = $relatedPostId;

		return update_field($fieldName, $relatedPostsIds, $postId);
	}

	return false;
}


/*
 * Those methods requires WPML
 */
function acfGetLangOption($fieldName, $targetLang)
{
	$currentLang = apply_filters( 'wpml_current_language', null);

	acf_update_setting('current_language', $targetLang);

	$value = get_field($fieldName, 'option' );

	acf_update_setting('current_language', $currentLang);

	return $value;
}

function acfGetBaseLangOption($fieldName)
{
	$defaultLang = apply_filters('wpml_default_language', null);

	return acfGetLangOption($fieldName, $defaultLang);
}

// This is only an alias acfGetCurrentLangOption get_field work by it self on current lang
function acfGetCurrentLangOption($fieldName)
{
	return get_field($fieldName, 'option');
	// $currentLang = apply_filters('wpml_current_language', null);

	// return acfGetLangOption($fieldName, $currentLang);
}

///////////////////////////////////////////////
#region htaccess

// function acfPopulateSelectFieldWithPostTypes($field) 
// {
// 	$post_types = get_post_types(['public' => true], 'objects');

// 	$field['choices'] = [];

// 	foreach ($post_types as $post_type) 
// 	{
// 		$field['choices'][$post_type->name] = $post_type->label;
// 	}

// 	return $field;
// }
// add_filter('acf/load_field/name=page_agregates_post_type', 'acfPopulateSelectFieldWithPostTypes');



////////////////////


// function acfUpdateHtaccessWithAcfRedirectsOnSave($post_id, $menu_slug)
// {
// 	if($post_id !== 'options'
// 	|| $menu_slug !== 'ustawienia-strony')
// 	{
// 		return;
// 	}

// 	$redirects = acfGetBaseLangOption('config_redirects');

// 	if(empty($redirects))
// 	{
// 		return;
// 	}

// 	$htaccessFile = ABSPATH . '.htaccess';

// 	if(! file_exists($htaccessFile))
// 	{
// 		error_log('htaccess file not found: ' . $htaccessFile);
// 		return;
// 	}

// 	$regionStart = "# BEGIN ACF CustomRedirects" . PHP_EOL . "<IfModule mod_rewrite.c>";
// 	$regionEnd = "</IfModule>" . PHP_EOL . "# END ACF CustomRedirects";

// 	$redirectRules = [];

// 	foreach ($redirects as $redirect)
// 	{
// 		$from = isset($redirect['config_redirects_item_from']) ? trim($redirect['config_redirects_item_from']) : '';
// 		$to = isset($redirect['config_redirects_item_to']) ? trim($redirect['config_redirects_item_to']) : '';

// 		if(empty($from) || empty($to))
// 		{
// 			continue;
// 		}

// 		if(! str_starts_with($from, '/'))
// 		{
// 			$from = '/' . $from;
// 		}

// 		if(! str_starts_with($to, 'https://') && !str_starts_with($to, 'http://'))
// 		{
// 			if(str_starts_with($to, '/'))
// 			{
// 				$to = rtrim(site_url(), '/') . $to;
// 			} else
// 			{
// 				$to = site_url() . '/' . $to;
// 			}
// 		}

// 		$redirectRules[] = "Redirect 301 {$from} {$to}";
// 	}

// 	if(empty($redirectRules))
// 	{
// 		return;
// 	}

// 	$customRedirects = $regionStart . PHP_EOL;
// 	$customRedirects .= implode(PHP_EOL, $redirectRules) . PHP_EOL;
// 	$customRedirects .= $regionEnd;

// 	$htaccessContents = file_get_contents($htaccessFile);

// 	$newHtaccessContents = preg_replace(
// 		"#" . preg_quote($regionStart, '#') . ".*?" . preg_quote($regionEnd, '#') . "#s",
// 		$customRedirects,
// 		$htaccessContents
// 	);

// 	if($newHtaccessContents === $htaccessContents)
// 	{
// 		if(strpos($htaccessContents, $regionStart) === false)
// 		{
// 			$newHtaccessContents .= PHP_EOL . $customRedirects . PHP_EOL;
// 		}
// 	}

// 	if($newHtaccessContents !== $htaccessContents)
// 	{
// 		file_put_contents($htaccessFile, $newHtaccessContents);
// 		error_log("Updated .htaccess with new redirects.");
// 	}
// }

// add_action('acf/options_page/save', 'acfUpdateHtaccessWithAcfRedirectsOnSave', 20, 2);



#endRegion htaccess
///////////////////////////////////////////////////////

function acfListComposableLayoutsUsage($field_name = 'composable')
{
	global $wpdb;

	$results = [];

	// --- POSTY (skip revisions and nav_menu_item) ---
	$post_ids = $wpdb->get_col($wpdb->prepare(
		"SELECT DISTINCT pm.post_id
		FROM {$wpdb->postmeta} pm
		JOIN {$wpdb->posts} p ON p.ID = pm.post_id
		WHERE pm.meta_key = %s
		AND p.post_type NOT IN ('revision', 'nav_menu_item')",
		$field_name
	));

	foreach($post_ids as $post_id)
	{
		$field = get_field($field_name, $post_id);
		if(!empty($field) && is_array($field))
		{
			$layouts = array_unique(wp_list_pluck($field, 'acf_fc_layout'));
			$results[] = [
				'object'  => 'post',
				'id'      => $post_id,
				'title'   => get_the_title($post_id),
				'layouts' => implode(', ', $layouts)
			];
		}
	}

	// --- TERMY ---
	$term_rows = $wpdb->get_results($wpdb->prepare(
		"SELECT DISTINCT m.term_id, tt.taxonomy
		FROM {$wpdb->termmeta} m
		JOIN {$wpdb->term_taxonomy} tt ON m.term_id = tt.term_id
		WHERE m.meta_key = %s",
		$field_name
	), ARRAY_A);

	foreach($term_rows as $row)
	{
		$field = get_field($field_name, $row['taxonomy'] . '_' . $row['term_id']);
		if(!empty($field) && is_array($field))
		{
			$layouts = array_unique(wp_list_pluck($field, 'acf_fc_layout'));
			$term = get_term($row['term_id'], $row['taxonomy']);
			$results[] = [
				'object'  => 'term',
				'id'      => $row['term_id'],
				'title'   => $term ? $term->name : '—',
				'layouts' => implode(', ', $layouts)
			];
		}
	}

	// --- OPTIONS PAGE ---
	$field = get_field($field_name, 'option');
	if(!empty($field) && is_array($field))
	{
		$layouts = array_unique(wp_list_pluck($field, 'acf_fc_layout'));
		$results[] = [
			'object'  => 'option',
			'id'      => 0,
			'title'   => 'Opcje strony',
			'layouts' => implode(', ', $layouts)
		];
	}

	if(empty($results))
	{
		return '<p>No elements with fields "' . esc_html($field_name) . '".</p>';
	}

	$html = '<table border="1" cellpadding="5" cellspacing="0">';
	$html .= '<tr><th>Typ obiektu</th><th>ID</th><th>Tytuł/Nazwa</th><th>Layouty</th></tr>';

	foreach($results as $row)
	{
		$html .= '<tr>';
		$html .= '<td>' . esc_html($row['object']) . '</td>';
		$html .= '<td>' . esc_html($row['id']) . '</td>';
		$html .= '<td>' . esc_html($row['title']) . '</td>';
		$html .= '<td>' . esc_html($row['layouts']) . '</td>';
		$html .= '</tr>';
	}

	$html .= '</table>';

	echo $html;
}


/////////////////////////////////////////////////////////
#region Options and Fields registration

add_action('acf/init', function()
{
	if(function_exists('acf_add_options_page'))
	{
		// Footer
		// With WPML set to "Diffrent fields in all languages"
		acf_add_options_sub_page(
		[
			'page_title'    => 'Footer',
			'menu_title'    => 'Footer',
			'parent_slug'   => 'options-general.php',
			'menu_slug'     => 'footer'
		]);

		// With WPML set to "Same fields in all languages"
		acf_add_options_sub_page(
		[
			'page_title'    => 'Setting independent from lang',
			'menu_title'    => 'Setting independent from lang',
			'parent_slug'   => 'options-general.php',
			'menu_slug'     => 'independent-settings'
		]);

		// With WPML set to "Diffrent fields in all languages"
		acf_add_options_sub_page(
		[
			'page_title'    => 'Settings for lang',
			'menu_title'    => 'Settings for lang',
			'parent_slug'   => 'options-general.php',
			'menu_slug'     => 'language-settings'
		]);
	}

	if(function_exists('acf_add_local_field_group'))
	{
		acf_add_local_field_group(
		[
			'key' => 'group_config_redirects',
			'title' => 'Redirects',
			'fields' => [
				[
					'key' => 'field_config_redirects',
					'label' => 'Redirects',
					'name' => 'config_redirects',
					'type' => 'repeater',
					'button_label' => 'Dodaj przekierowanie',
					'sub_fields' => [
						[
							'key' => 'field_config_redirects_item_from',
							'label' => 'Redirect from (eg. /work)',
							'name' => 'config_redirects_item_from',
							'type' => 'text',
							'required' => 1,
						],
						[
							'key' => 'field_config_redirects_item_to',
							'label' => 'Redirect to',
							'name' => 'config_redirects_item_to',
							'type' => 'text',
							'required' => 1,
						],
					],
				],
			],
			'location' => [
				[
					[
						'param' => 'options_page',
						'operator' => '==',
						'value' => 'independent-settings', // slug
					],
				],
			],
		]);

		acf_add_local_field_group(
		[
			'key' => 'group_link_items',
			'title' => 'Link shortcodes [link alias="name_from_alias_field"]',
			'fields' => [
				[
					'key' => 'field_link_items',
					'label' => 'Link shortcodes',
					'name' => 'link-items',
					'type' => 'repeater',
					'button_label' => 'Add link',
					'sub_fields' => [
						[
							'key' => 'field_link_item_alias',
							'label' => 'Alias',
							'name' => 'link-item-alias',
							'type' => 'text',
						],
						[
							'key' => 'field_link_item_text',
							'label' => 'Text',
							'name' => 'link-item-text',
							'type' => 'text',
						],
						[
							'key' => 'field_link_item_aria',
							'label' => 'Aria label',
							'name' => 'link-item-aria',
							'type' => 'text',
						],
						[
							'key' => 'field_link_item_href',
							'label' => 'Outer link',
							'name' => 'link-item-href',
							'type' => 'text',
						],
						[
							'key' => 'field_link_item_page',
							'label' => 'Inner link',
							'name' => 'link-item-page',
							'type' => 'post_object',
							'post_type' => ['post','page'],
							'return_format' => 'object',
						],
						[
							'key' => 'field_link_item_target',
							'label' => 'Open in new tab',
							'name' => 'link-item-target',
							'type' => 'true_false',
							'ui' => 1,
							'ui_on_text' => 'Yes',
							'ui_off_text' => 'No',
						],
					],
				],
			],
			'location' => [
				[
					[
						'param' => 'options_page',
						'operator' => '==',
						'value' => 'language-settings',
					],
				],
			],
		]);

		acf_add_local_field_group(
		[
			'key' => 'group_info_items',
			'title' => 'Info [info alias="name_of_alias_field"]',
			'fields' => [
				[
					'key' => 'field_info_items',
					'label' => 'Info',
					'name' => 'info-items',
					'type' => 'repeater',
					'button_label' => 'Dodaj info',
					'sub_fields' => [
						[
							'key' => 'field_info_item_alias',
							'label' => 'Alias',
							'name' => 'info-item-alias',
							'type' => 'text',
							'required' => 1,
						],
						[
							'key' => 'field_info_item_content',
							'label' => 'Content',
							'name' => 'info-item-content',
							'type' => 'wysiwyg',
							'required' => 1,
						],
					],
				],
			],
			'location' => [
				[
					[
						'param' => 'options_page',
						'operator' => '==',
						'value' => 'language-settings',
					],
				],
			],
		]);

		$taxonomiesToAddBaseFields = [
			'category',
		];

		foreach($taxonomiesToAddBaseFields as $taxonomy)
		{
			acf_add_local_field_group(
			[
				'key' => 'group_' . $taxonomy . '_fields',
				'title' => 'Additional fields ' . $taxonomy,
				'fields' => [
					[
						'key' => 'field_' . $taxonomy . '_published',
						'label' => 'Published',
						'name' => 'term_published',
						'type' => 'true_false',
						'instructions' => 'Check if should be published',
						'ui' => 1,
						'ui_on_text' => 'Yes',
						'ui_off_text' => 'No',
					],
					[
						'key' => 'field_' . $taxonomy . '_title',
						'label' => 'Full title',
						'name' => 'term_title',
						'type' => 'text',
						'required' => 1,
					],
				],
				'location' => [
					[
						[
							'param' => 'taxonomy',
							'operator' => '==',
							'value' => $taxonomy,
						],
					],
				],
			]);

			acf_add_local_field_group(
			[
				'key' => 'group_page_redirect',
				'title' => 'Page redirect',
				'fields' => [
					[
						'key' => 'field_page_redirect_active',
						'label' => 'Turn on page redirects',
						'name' => 'page_redirect_active',
						'type' => 'true_false',
						'instructions' => 'Fill to active redirect',
						'ui' => 1,
						'ui_on_text' => 'Yes',
						'ui_off_text' => 'No',
					],
					[
						'key' => 'field_page_redirect',
						'label' => 'Redirect',
						'name' => 'page_redirect',
						'type' => 'post_object',
						'post_type' => ['page'], // tylko strony
						'return_format' => 'id',
					],
				],
				'location' => [
					[
						[
							'param' => 'post_type',
							'operator' => '==',
							'value' => 'page',
						],
					],
				],
			]);
		}
	}
});

#endregion Options and Fields registration