<?php
$siteUrl = site_url();
$currentId = currentID();
$currentLang = apply_filters('wpml_current_language', NULL); //wpml

get_header();

$fontSize = intval($_COOKIE['fontsize'] ?? 100);

$fontSizes = [
	100 => 'A',
	150 => 'A+',
	200 => 'A++',
];

$templateSlug = get_page_template_slug($currentId);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<meta name="format-detection" content="telephone=no">

	<?= wp_head(); ?>

	<link rel="icon" type="image/png" href="<?= get_template_directory_uri(); ?>/assets/images/favicon/favicon-96x96.png" sizes="96x96" />
	<link rel="icon" type="image/svg+xml" href="<?= get_template_directory_uri(); ?>/assets/images/favicon/favicon.svg" />
	<link rel="shortcut icon" href="<?= get_template_directory_uri(); ?>/assets/images/favicon/favicon.ico" />
	<link rel="apple-touch-icon" sizes="180x180" href="<?= get_template_directory_uri(); ?>/assets/images/favicon/apple-touch-icon.png" />
	<meta name="apple-mobile-web-app-title" content="<?= get_bloginfo('name'); ?>" />
	<link rel="manifest" href="<?= get_template_directory_uri(); ?>/assets/images/favicon/site.webmanifest" crossorigin="use-credentials">

	<link rel="stylesheet" href="<?= get_template_directory_uri(); ?>/assets/css/styles.css">
</head>

<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>

	<style>
	:root
	{
		--fontsize: <?= $fontSize; ?>
	}
	</style>

	<main class="main-wrapper" role="main">
		<header class="header js-header">
			<a href="<?= $siteUrl; ?>" class="header__logo__container" aria-label="<?= __('Site logo', 'customtheme'); ?>">
				<img src="<?= get_template_directory_uri(); ?>/assets/images/global/logo.svg" class="header__logo js-nav-click" alt="<?= __('Site logo', 'customtheme'); ?>">
			</a>

			<?php
			$headerMenu = getMenuItems('header_menu');

			if($headerMenu)
			{
			?>

			<nav class="header__links js-header-links" role="navigation">
				<?php
				foreach($headerMenu as $primaryMenuEl)
				{
					$target = '_self';

					if($primaryMenuEl->type == 'custom')
					{
						$target = '_blank';
					}

					$children = $primaryMenuEl->children;
					$childrenCount = ($children) ? count($children) : 0;
				?>

				<?php
				}
				?>

			</nav>

			<?php
			}
			?>

			<div class="header__utility">
				<button type="button" class="js-hamburger js-general-click" aria-label="<?= __("Toggle nav menu visibility", "customtheme"); ?>">
					<span></span>
					<span></span>
					<span></span>
				</button>
			</div>
		</header>
