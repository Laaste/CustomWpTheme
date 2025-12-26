<?php
add_filter('xmlrpc_enabled', '__return_false');

function disable_wp_oembeds()
{
	// Remove the REST API endpoint.
	remove_action('rest_api_init', 'wp_oembed_register_route');
	// Turn off oEmbed auto discovery.
	add_filter('embed_oembed_discover', '__return_false');
	// Don't filter oEmbed results.
	remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);
	// Remove oEmbed discovery links.
	remove_action('wp_head', 'wp_oembed_add_discovery_links');
	// Remove oEmbed-specific JavaScript from the front-end and back-end.
	remove_action('wp_head', 'wp_oembed_add_host_js');
}
add_action('init', 'disable_wp_oembeds');

add_theme_support('title-tag');

remove_filter('the_content', 'wpautop');
remove_filter('the_excerpt', 'wpautop');
remove_filter('acf_the_content', 'wpautop');
remove_filter('wpcf7_form_elements', 'wpautop');
add_filter('wpcf7_autop_or_not', '__return_false');

add_theme_support('site-icon');
remove_action( 'wp_head', 'wp_site_icon', 99 );
add_action( 'wp_head', 'wp_site_icon', 0 );

add_filter( 'rest_endpoints', 'disable_default_endpoints' );
function disable_default_endpoints( $endpoints ) {
	$endpoints_to_remove = array(
		'/oembed/1.0',
		'/wp/v2',
		'/wp/v2/media',
		'/wp/v2/types',
		'/wp/v2/statuses',
		'/wp/v2/taxonomies',
		'/wp/v2/tags',
		'/wp/v2/users',
		'/wp/v2/comments',
		'/wp/v2/settings',
		'/wp/v2/themes',
		'/wp/v2/blocks',
		'/wp/v2/oembed',
		'/wp/v2/posts',
		'/wp/v2/pages',
		'/wp/v2/block-renderer',
		'/wp/v2/search',
		'/wp/v2/categories'
	);

	if ( ! is_user_logged_in() ) {
		foreach ( $endpoints_to_remove as $rem_endpoint ) {
			// $base_endpoint = "/wp/v2/{$rem_endpoint}";
			foreach ( $endpoints as $maybe_endpoint => $object ) {
				if ( stripos( $maybe_endpoint, $rem_endpoint ) !== false ) {
					unset( $endpoints[ $maybe_endpoint ] );
				}
			}
		}
	}

	return $endpoints;
}

add_action('wp_enqueue_scripts', function(){
	// Unload Gutenberg-related stylesheets.
	wp_dequeue_style('wp-block-library'); // Wordpress core
	wp_dequeue_style('wp-block-library-theme'); // Wordpress core

	/**
	 * Remove global script
	 */
	wp_dequeue_style('global-styles');
	wp_deregister_style('global-styles');
}, PHP_INT_MAX);

remove_action('wp_head', 'wp_generator');

// remove wp-includes/css/classic-themes.min.css?
remove_action('wp_enqueue_scripts', 'wp_enqueue_classic_theme_styles');

remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

add_filter('tiny_mce_before_init', function($init)
{
	$init['remove_linebreaks'] = false;
	$init['keep_styles'] = true;
	$init['media_strict'] = false;
	$init['paste_remove_styles'] = false;
	$init['paste_remove_spans'] = false;
	$init['paste_strip_class_attributes'] = 'none';
	$init['paste_text_use_dialog'] = true;
	$init['wpeditimage_disable_captions'] = true;
	$init['convert_newlines_to_brs'] = true; 
	$init['remove_redundant_brs'] = false;
	$init['apply_source_formatting'] = false;
	$init['allow_conditional_comments'] = true;
	$init['allow_html_in_named_anchor'] = true;
	$init['allow_unsafe_link_target'] = false;
	$init['convert_fonts_to_spans'] = true;
	$init['element_format'] = 'html';
	$init['entities'] = '';
	$init['entity_encoding'] = 'raw';
	$init['fix_list_elements'] = false;
	$init['force_hex_style_colors'] = true;
	$init['forced_root_block'] = '';
	$init['forced_root_block_attrs'] = '';
	$init['invalid_elements'] = '';
	$init['invalid_styles'] = '';
	$init['remove_trailing_brs'] = false;
	$init['br_in_pre'] = false;
	$init['object_resizing'] = false;
	$init['typeahead_urls'] = false;
	$init['wpautop'] = false;

	return $init;
});

function disable_emojis()
{
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' ); 
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' ); 
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	add_filter( 'tiny_mce_plugins', 'disable_emojis_tinymce' );
	add_filter( 'wp_resource_hints', 'disable_emojis_remove_dns_prefetch', 10, 2 );
}

add_theme_support('menus');
add_theme_support('post-thumbnails');

function add_site_title()
{
	if(class_exists('WPSEO_Frontend'))
	{
		return; // Yoast SEO is active, do nothing
	}

	$title = get_bloginfo('name');

	echo '<title>' . $title . '</title>';
}
add_action('wp_head', 'add_site_title');

add_filter('wpseo_debug_markers', '__return_false');

function add_meta_description()
{
	if(class_exists('WPSEO_Frontend'))
	{
		return; // Yoast SEO is active, do nothing
	}

	$description = get_bloginfo('description');

	echo '<meta name="description" content="' . $description . '">';
}
add_action('wp_head', 'add_meta_description');

function add_meta_format_detection()
{
	echo '<meta name="format-detection" content="telephone=no">';
}
add_action('wp_head', 'add_meta_format_detection');

add_filter('wp_img_tag_add_auto_sizes', '__return_false');
/* wp_img_tag_add_auto_sizes removes <style>img:is([sizes="auto" i], [sizes^="auto," i]) { contain-intrinsic-size: 3000px 1500px }</style>  if wordpress media.php was loaded */

if(WP_DEBUG)
{
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
}