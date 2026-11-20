<?php
#region Taxonomies
// function register_dealers_taxonomies()
// {
// 	$labels = [
// 		"name" => esc_html__("Usługi sprzedawców", "custom-post-type-ui"),
// 		"singular_name" => esc_html__("Usługa sprzedawcy", "custom-post-type-ui"),
// 	];

// 	$args = [
// 		"label" => esc_html__("Usługi sprzedawców", "custom-post-type-ui"),
// 		"labels" => $labels,
// 		"public" => true,
// 		"publicly_queryable" => true,
// 		"hierarchical" => true,
// 		"show_ui" => true,
// 		"show_in_menu" => true,
// 		"show_in_nav_menus" => true,
// 		"query_var" => true,
// 		"rewrite" => [
// 			'slug' => 'uslugi-sprzedawcow',
// 			'with_front' => true,
// 			'hierarchical' => true,
// 		],
// 		"show_admin_column" => true,
// 		"show_in_rest" => true,
// 		"show_tagcloud" => false,
// 		"rest_base" => "uslugi-sprzedawcow",
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

// 	register_taxonomy("uslugi-sprzedawcow", ["sprzedawca"], $args);
// }

// add_action('init', 'register_dealers_taxonomies', 0);
#endregion Taxonomies

////////////////////////////////////////////////
////////////////////////////////////////////////
////////////////////////////////////////////////

#region PostTypes
// function register_model_post_type()
// {
// 	/**
// 	 * Post Type: Model.
// 	 */

// 	$labels = [
// 		"name" => esc_html__("Modele", "custom-post-type-ui"),
// 		"singular_name" => esc_html__("Model", "custom-post-type-ui"),
// 		"menu_name" => esc_html__("Modele", "custom-post-type-ui"),
// 		"all_items" => esc_html__("Wszystkie modele", "custom-post-type-ui"),
// 		"add_new" => esc_html__("Dodaj model", "custom-post-type-ui"),
// 		"add_new_item" => esc_html__("Dodaj nowy model", "custom-post-type-ui"),
// 		"edit_item" => esc_html__("Edytuj model", "custom-post-type-ui"),
// 		"new_item" => esc_html__("Nowy model", "custom-post-type-ui"),
// 		"view_item" => esc_html__("Zobacz model", "custom-post-type-ui"),
// 		"view_items" => esc_html__("Zobacz modele", "custom-post-type-ui"),
// 		"search_items" => esc_html__("Szukaj modelu", "custom-post-type-ui"),
// 		"not_found" => esc_html__("Modelu nie znaleziony", "custom-post-type-ui"),
// 		"not_found_in_trash" => esc_html__("Model nie znaleziony w koszu", "custom-post-type-ui"),
// 		"parent" => esc_html__("Rodzic modelu", "custom-post-type-ui"),
// 		"archives" => esc_html__("Archiwum modelu", "custom-post-type-ui"),
// 		"insert_into_item" => esc_html__("Wstaw do Modelu", "custom-post-type-ui"),
// 		"uploaded_to_this_item" => esc_html__("Wysłano do modelu", "custom-post-type-ui"),
// 		"filter_items_list" => esc_html__("Filtruj liste modeli", "custom-post-type-ui"),
// 		"items_list_navigation" => esc_html__("Nawigacja modeli", "custom-post-type-ui"),
// 		"items_list" => esc_html__("Lista modeli", "custom-post-type-ui"),
// 		"attributes" => esc_html__("Atrybuty modeli", "custom-post-type-ui"),
// 		"name_admin_bar" => esc_html__("Modele", "custom-post-type-ui"),
// 		"item_published" => esc_html__("Modele opublikowane", "custom-post-type-ui"),
// 		"item_published_privately" => esc_html__("Model opublikowany prywatnie", "custom-post-type-ui"),
// 		"item_reverted_to_draft" => esc_html__("Model ustawiony jako szkic", "custom-post-type-ui"),
// 		"item_trashed" => esc_html__("Model przeniesiony do kosza", "custom-post-type-ui"),
// 		"item_scheduled" => esc_html__("Model zaplanowany", "custom-post-type-ui"),
// 		"item_updated" => esc_html__("Model zmodyfikowany", "custom-post-type-ui"),
// 		"parent_item_colon" => esc_html__("Rodzic modelu: ", "custom-post-type-ui"),
// 	];

// 	$args = [
// 		"label" => esc_html__("Modele", "custom-post-type-ui"),
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
// 			"slug" => "modele",
// 			"with_front" => true
// 		],
// 		"query_var" => true,
// 		"menu_icon" => "dashicons-car",
// 		"supports" => [
// 			"title",
// 			"editor",
// 			"thumbnail",
// 			"excerpt",
// 			"revisions",
// 			"author",
// 			"custom-fields"
// 		],
// 		"taxonomies" => [""],
// 		"show_in_graphql" => false,
// 	];

// 	register_post_type("model", $args);
// }

// add_action('init', 'register_model_post_type', 1);
#endregion PostTypes