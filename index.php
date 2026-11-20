<?php
if(! is_front_page() && wp_safe_redirect(home_url('/'), 302))
{
	exit;
}