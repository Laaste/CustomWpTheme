<?php
function create_info_shortcode($atts = [])
{
	if($atts
	&& isset($atts['alias'])
	&& ! empty($atts['alias']))
	{
		return acfInfo($atts['alias']);
	}
	else
	{
		return null;
	}
}

add_shortcode('info', 'create_info_shortcode');
