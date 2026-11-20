<?php
function add_shortcode_current_year()
{
	return date("Y");
}

add_shortcode('current_year', 'add_shortcode_current_year');