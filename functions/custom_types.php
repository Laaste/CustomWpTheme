<?php
#region Taxonomies
// function register_event_taxonomies()
// {
// 	$labels = [
// 		"name" => esc_html__("Event categories", "custom-post-type-ui"),
// 		"singular_name" => esc_html__("Event category", "custom-post-type-ui"),
// 	];

// 	$args = [
// 		"label" => esc_html__("Event categories", "custom-post-type-ui"),
// 		"labels" => $labels,
// 		"public" => true,
// 		"publicly_queryable" => true,
// 		"hierarchical" => true,
// 		"show_ui" => true,
// 		"show_in_menu" => 'true',
// 		"show_in_nav_menus" => true,
// 		"query_var" => true,
// 		"rewrite" => [
// 			'slug' => 'event-categories',
// 			'with_front' => true,
// 			'hierarchical' => true,
// 		],
// 		"show_admin_column" => true,
// 		"show_in_rest" => true,
// 		"show_tagcloud" => false,
// 		"rest_base" => "event-category",
// 		"rest_controller_class" => "WP_REST_Terms_Controller",
// 		"rest_namespace" => "wp/v2",
// 		"show_in_quick_edit" => false,
// 		"sort" => true,
// 		"show_in_graphql" => false,
// 		'capabilities' => [
// 			// 'manage_terms' => 'manage_event_categories',
// 			// 'edit_terms' => 'edit_event_categories',
// 			// 'delete_terms' => 'delete_event_categories',
// 			// 'assign_terms' => 'assign_event_categories'
// 			'manage_terms' => 'manage_categories',
// 			'edit_terms' => 'edit_categories',
// 			'delete_terms' => 'delete_categories',
// 			'assign_terms' => 'assign_categories'
// 		], 
// 	];

// 	register_taxonomy("event-category", ["event"], $args);
// }

// add_action('init', 'register_event_taxonomies', 0);
#endregion Taxonomies

////////////////////////////////////////////////
////////////////////////////////////////////////
////////////////////////////////////////////////

#region PostTypes
// function register_event_post_type()
// {
// 	/**
// 	 * Post Type: Event.
// 	 */

// 	$labels = [
// 		"name" => esc_html__("Events", "custom-post-type-ui"),
// 		"singular_name" => esc_html__("Event", "custom-post-type-ui"),
// 		"menu_name" => esc_html__("Events", "custom-post-type-ui"),
// 		"all_items" => esc_html__("All Events", "custom-post-type-ui"),
// 		"add_new" => esc_html__("Add Event", "custom-post-type-ui"),
// 		"add_new_item" => esc_html__("Add new Event", "custom-post-type-ui"),
// 		"edit_item" => esc_html__("Edit Event", "custom-post-type-ui"),
// 		"new_item" => esc_html__("New Events", "custom-post-type-ui"),
// 		"view_item" => esc_html__("See Event", "custom-post-type-ui"),
// 		"view_items" => esc_html__("See events", "custom-post-type-ui"),
// 		"search_items" => esc_html__("Search Eventu", "custom-post-type-ui"),
// 		"not_found" => esc_html__("Event not found", "custom-post-type-ui"),
// 		"not_found_in_trash" => esc_html__("Event not found in trash", "custom-post-type-ui"),
// 		"parent" => esc_html__("Event Parent", "custom-post-type-ui"),
// 		"archives" => esc_html__("Event Archive", "custom-post-type-ui"),
// 		"insert_into_item" => esc_html__("Insert to Event", "custom-post-type-ui"),
// 		"uploaded_to_this_item" => esc_html__("Send to event", "custom-post-type-ui"),
// 		"filter_items_list" => esc_html__("Filter events", "custom-post-type-ui"),
// 		"items_list_navigation" => esc_html__("Navigate Events", "custom-post-type-ui"),
// 		"items_list" => esc_html__("Events list", "custom-post-type-ui"),
// 		"attributes" => esc_html__("Event attributes", "custom-post-type-ui"),
// 		"name_admin_bar" => esc_html__("Events", "custom-post-type-ui"),
// 		"item_published" => esc_html__("Published Events", "custom-post-type-ui"),
// 		"item_published_privately" => esc_html__("Event published privately", "custom-post-type-ui"),
// 		"item_reverted_to_draft" => esc_html__("Event set as draw", "custom-post-type-ui"),
// 		"item_trashed" => esc_html__("Event moved to trash", "custom-post-type-ui"),
// 		"item_scheduled" => esc_html__("Event planned", "custom-post-type-ui"),
// 		"item_updated" => esc_html__("Event updated", "custom-post-type-ui"),
// 		"parent_item_colon" => esc_html__("Event Parent", "custom-post-type-ui"),
// 	];

// 	$args = [
// 		"label" => esc_html__("Events", "custom-post-type-ui"),
// 		"labels" => $labels,
// 		"description" => "",
// 		"public" => true,
// 		"publicly_queryable" => true,
// 		"show_ui" => true,
// 		"show_in_rest" => true,
// 		"rest_base" => "",
// 		"rest_controller_class" => "WP_REST_Posts_Controller",
// 		"rest_namespace" => "wp/v2",
// 		"has_archive" => false,
// 		"show_in_menu" => true,
// 		"show_in_nav_menus" => true,
// 		"delete_with_user" => false,
// 		"exclude_from_search" => false,
// 		"capability_type" => "post",
// 		"map_meta_cap" => true,
// 		"hierarchical" => false,
// 		"can_export" => false,
// 		"rewrite" => [
// 			"slug" => "events",
// 			"with_front" => true
// 		],
// 		"query_var" => true,
// 		"menu_icon" => "dashicons-format-aside",
// 		"supports" => [
// 			"title",
// 			"editor",
// 			"thumbnail",
// 			"excerpt",
// 			"revisions",
// 			"author"
// 		],
// 		"taxonomies" => ["event-category"],
// 		"show_in_graphql" => false,
// 	];

// 	register_post_type("event", $args);
// }

// add_action('init', 'register_event_post_type', 1);
#endregion PostTypes