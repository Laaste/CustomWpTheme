<?php
function setTimezoneAjax()
{
	date_default_timezone_set('Europe/Warsaw');
}

setTimezoneAjax();

function echoAjaxUrlInFooter()
{
	$ajaxUrl = admin_url('admin-ajax.php');

	echo "<script type='text/javascript'>var ajaxUrl = '{$ajaxUrl}';</script>";
}
add_action('wp_head', 'echoAjaxUrlInFooter');

// function regiterSubscriber()
// {
// 	global $wpdb;

// 	$tableName = $wpdb->prefix . 'customforms';
// 	$response = [];

// 	$formGuid = sanitize_text_field(@$_POST['formdata']['form_guid']);
// 	$postId = intval(@$_POST['formdata']['post_id']);
// 	$name = sanitize_text_field(@$_POST['formdata']['name']);
// 	$surname = sanitize_text_field(@$_POST['formdata']['surname']);
// 	$email = sanitize_email(@$_POST['formdata']['email']);

// 	if(! $postId)
// 	{
// 		$response = [
// 			'success' => false,
// 			'message' => 'missing post_id',
// 		];

// 		return wp_send_json($response);
// 	}

// 	if(empty($formGuid))
// 	{
// 		$response = [
// 			'success' => false,
// 			'message' => 'missing form_guid',
// 		];

// 		return wp_send_json($response);
// 	}

// 	if(empty($name)
// 	|| empty($surname))
// 	{
// 		$response = [
// 			'success' => false,
// 			'message' => 'atleast one of required param is empty',
// 		];

// 		return wp_send_json($response);
// 	}

// 	if(mb_strlen($name) > 256
// 	|| mb_strlen($surname) > 256
// 	|| mb_strlen($email) > 256)
// 	{
// 		$response = [
// 			'success' => false,
// 			'message' => 'atleast one of param is too long',
// 		];

// 		return wp_send_json($response);
// 	}

// 	$insertSql = "INSERT INTO " . $tableName . "
// 	(
// 		form_guid,
// 		post_id,
// 		name,
// 		surname,
// 		email
// 	)
// 	VALUES
// 	(
// 		%s,
// 		%d,
// 		%s,
// 		%s,
// 		%s
// 	)";

// 	$sql = $wpdb->prepare($insertSql,
// 		$formGuid,
// 		$postId, 
// 		$name,
// 		$surname,
// 		$email,
// 	);

// 	$result = $wpdb->query($sql);

// 	if($result === false)
// 	{
// 		error_log("Database query failed: " . $wpdb->last_error);

// 		$response = [
// 			'success' => false,
// 			'message' => 'error',
// 		];
// 	}
// 	else if($result === 0)
// 	{
// 		$response = [
// 			'success' => false,
// 			'message' => 'no rows inserted',
// 		];
// 	}
// 	else
// 	{
// 		$response = [
// 			'success' => true,
// 			'message' => 'success',
// 		];
// 	}

// 	return wp_send_json($response);
// }
// add_action('wp_ajax_regiterSubscriber', 'regiterSubscriber');
// add_action('wp_ajax_nopriv_regiterSubscriber', 'regiterSubscriber');


// function searchPostsAjax()
// {
// 	$postsFound = [];

// 	$response = [
// 		'success' => true,
// 		'posts' => $postsFound,
// 	];

// 	return wp_send_json($response);
// }
// add_action('wp_ajax_searchPostsAjax', 'searchPostsAjax');
// add_action('wp_ajax_nopriv_searchPostsAjax', 'searchPostsAjax');

function clearSessionUtm()
{
	unset($_SESSION['utm']);
	unset($_SESSION['utm_save_time']);
	unset($_SESSION['utm_end_time']);
}

function saveSessionUtm()
{
	if(session_status() === PHP_SESSION_NONE)
	{
		session_start();
	}

	$raw = $_POST['params'] ?? '{}';
	$params = json_decode(stripslashes($raw), true);

	if(! is_array($params))
	{
		wp_send_json([
			'success' => false,
			'message' => [
				'title' => 'Błąd',
				'text'  => 'Nieprawidłowe dane UTM'
			]
		]);
	}

	$now = time();
	$ttl = 1 * 60 * 60; // 1h
	$endTime = $now + $ttl;

	// Clear
	if(isset($_SESSION['utm_end_time'])
	&& $_SESSION['utm_end_time'] < $now)
	{
		clearSessionUtm();
	}

	// Add new
	if(! empty($params))
	{
		$_SESSION['utm'] = $params;
		$_SESSION['utm_save_time'] = $now;
		$_SESSION['utm_end_time'] = $endTime;
	}

	wp_send_json([
		'success' => true
	]);
}
add_action('wp_ajax_saveSessionUtm', 'saveSessionUtm');
add_action('wp_ajax_nopriv_saveSessionUtm', 'saveSessionUtm');

function appendUtmParams(array &$contactParams, $source)
{
	if(!isset($_POST['params']))
	{
		return;
	}

	if(!is_array($source))
	{
		return;
	}

	$utmKeys = [
		'utm_source',
		'utm_medium',
		'utm_campaign',
		'utm_term',
		'utm_content'
	];

	foreach($utmKeys as $key)
	{
		if(isset($source[$key]) && $source[$key] !== '')
		{
			$contactParams[$key] = (string)$source[$key];
		}
	}
}