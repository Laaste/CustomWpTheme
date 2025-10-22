<?php
function create_page_link_shortcode($atts)
{
	// Set default attributes
	$atts = shortcode_atts(
	[
		'alias' => '',
		'text' => '',
		'class' => '',
		'target' => '',
		'rel' => '',
		'aria-label' => '',
		'style' => '',
	], $atts, 'page');

	$linkItems = get_field('link-items', 'option');

	if(! $linkItems
	|| empty($linkItems))
	{
		return '';
	}

	$url = '';
	$text = '';
	$target = '_self';
	$rel = '';
	$ariaLabel = '';

	foreach($linkItems as $item)
	{
		if($item['link-item-alias'] === $atts['alias'])
		{
			if(isset($item['link-item-page'])
			&& ! empty($item['link-item-page']))
			{
				$url = get_permalink($item['link-item-page']->ID);
				$text = get_the_title($item['link-item-page']->ID);
			}
			else if(isset($item['link-item-href'])
			&& ! empty($item['link-item-href']))
			{
				$url = $item['link-item-href'];
				$text = $url;
			}

			if(isset($item['link-item-text'])
			&& ! empty($item['link-item-text']))
			{
				$text = $item['link-item-text'];
			}

			if(isset($item['link-item-target'])
			&& ! empty($item['link-item-target']))
			{
				$target = explode(' ', $item['link-item-target']);
				$target = trim($target[0], ':');
			}

			if(isset($item['link-item-aria'])
			&& ! empty($item['link-item-aria']))
			{
				$ariaLabel = $item['link-item-aria'];
			}

			break;
		}
	}

	// Override with shortcode attributes if provided
	if($atts['text'])
	{
		$text = $atts['text'];
	}

	if($atts['target'])
	{
		$target = $atts['target'];
	}

	if($atts['rel'])
	{
		$rel = $atts['rel'];
	}

	if($atts['aria-label'])
	{
		$ariaLabel = $atts['aria-label'];
	}

	// Build the link tag
	$link = '<a href="' . $url . '" target="' . $target . '"';

	if($atts['class'])
	{
		$link .= ' class="' . $atts['class'] . '"';
	}

	if($rel)
	{
		$link .= ' rel="' . $rel . '"';
	}

	if($ariaLabel)
	{
		$link .= ' aria-label="' . $ariaLabel . '"';
	}

	if($atts['style'])
	{
		$link .= ' style="' . esc_attr($atts['style']) . '"';
	}

	$link .= '>' . $text . '</a>';

	return $link;
}

add_shortcode('link', 'create_page_link_shortcode');
