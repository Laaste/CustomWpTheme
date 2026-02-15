<?php
function add_shortcode_current_id()
{
	return currentID();
}

add_shortcode('current_id', 'add_shortcode_current_id');