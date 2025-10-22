<?php
function add_shortcode_br()
{
	return '<br>';
}

add_shortcode( 'br', 'add_shortcode_br' );