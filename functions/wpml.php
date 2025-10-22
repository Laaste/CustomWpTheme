<?php
function get_translated_post_thumbnail_alt($postId) 
{
	// Get the featured image ID
	$thumbnailId = get_post_thumbnail_id($postId);

	if(! $thumbnailId)
	{
		return '';
	}

	if(function_exists('wpml_object_id'))
	{
		$thumbnailId = wpml_object_id($thumbnailId, 'attachment', true);
	}

	$altText = get_post_meta($thumbnailId, '_wp_attachment_image_alt', true);

	if(empty($altText))
	{
		$altText = get_the_title($thumbnailId);
	}

	return $altText;
}

function getTranslationId($originalId, $postType = 'post', $lang = 'en', $orginalIfNoTranslation = false)
{
	return apply_filters('wpml_object_id', $originalId, $postType, $orginalIfNoTranslation, $lang);
}

function getLangPost($originalId, $postType = 'post', $orginalIfNoTranslation = true)
{
	$currentLang = apply_filters('wpml_current_language', null);

	return get_post(getTranslationId($originalId, $postType, $currentLang, $orginalIfNoTranslation));
}