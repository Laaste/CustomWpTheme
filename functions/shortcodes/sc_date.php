<?php
function add_shortcode_date($atts)
{
	if (!is_array($atts))
	{
		$atts = [];
	}

	return shortCodeDate('date', $atts);
}

add_shortcode('date', 'add_shortcode_date');

function add_shortcode_publish_date($atts)
{
	if (!is_array($atts))
	{
		$atts = [];
	}
	
	$currentId = currentID();

	$date = get_the_date('Y-m-d H:i:s', $currentId);

	return shortCodeDate('publish-date', $atts, $date);
}

add_shortcode('publish-date', 'add_shortcode_publish_date');

function shortCodeDate($shortCodeName, $atts, $date = '')
{
	$atts = shortcode_atts(
		[
			'format' => 'd-m-Y H:i:s',
			'day-as-text' => '',
			'month-as-text' => '',
		],
		$atts,
		$shortCodeName
	);

	$trueValues = [1, true, '1', 'true', 'yes', 'on'];

	$dayAsText = (empty($atts['day-as-text'])) ? false : in_array($atts['day-as-text'], $trueValues, true);
	$monthAsText = (empty($atts['month-as-text'])) ? false : in_array($atts['month-as-text'], $trueValues, true);

	$args = [];

	if($dayAsText !== null) $args['day_as_text'] = $dayAsText;
	if($monthAsText !== null) $args['month_as_text'] = $monthAsText;

	if(empty($date))
	{
		return date($atts['format']);
	}

	return dateText($date, $args);
}