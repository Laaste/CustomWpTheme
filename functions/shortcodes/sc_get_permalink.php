<?php
function shortcode_for_get_permalink($atts) {
	if(!isset($atts['id'])){
		return false;
	}

	return get_permalink($atts['id']);
}
add_shortcode( 'get-permalink', 'shortcode_for_get_permalink' );