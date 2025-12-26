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
?>

<!DOCTYPE html>
<html lang="en">
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

<body>
	<style>
	:root
	{
		--fontsize: <?= $fontSize; ?>
	}
	</style>

	<div class="main-wrapper">
		<div>
			<header class="header">
				<a href="<?= $siteUrl; ?>" class="header__logo__container" aria-label="<?= __('Site logo', 'customtheme'); ?>">
					<img data-lazy-img="<?= get_template_directory_uri(); ?>/assets/images" class="header__logo" alt="<?= __('Site logo', 'customtheme'); ?>">
				</a>

				<?php
				$headerMenu = getMenuItems('header_menu');

				if($headerMenu)
				{
				?>

				<nav class="header__links">
					<?php
					foreach($headerMenu as $primaryMenuEl)
					{
						$target = '_self';

						if($primaryMenuEl->type == 'custom')
						{
							$target = '_blank';
						}
					?>

					<a href="<?= $primaryMenuEl->url; ?>" target="<?= $target; ?>" class="header__link <?= ($currentId == $primaryMenuEl->object_id) ? 'active' : '' ?>">
						<div>
							<?= $primaryMenuEl->title; ?>
						</div>
					</a>

					<?php
					}
					?>
				</nav>

				<?php
				}
				?>

				<div class="header__utility">
					<div class="header__utility__fontsizes js-csl-container">
						<div class="header__utility__fontsizes__current js-csl-current" data-value="<?= $fontSize ?>"><?= $fontSizes[$fontSize]; ?></div>

						<ul class="header__utility__fontsizes__options js-csl-options" style="display: none;">
							<?php
							foreach($fontSizes as $value => $text)
							{
							?>

							<li class="header__utility__fontsizes__option js-csl-option" data-value="<?= $value; ?>">
								<button type="button" class="js-font-size" aria-label="<?= __("Set font-size to:", "customtheme") . " $value%"; ?>" data-size="<?= $value; ?>">
									<?= $text; ?>
								</button>
							</li>

							<?php
							}
							?>
						</ul>
					</div>

					<button type="button" class="js-hamburger" aria-label="<?= __("Toggle nav menu visibility", "customtheme"); ?>">
						<span></span>
						<span></span>
						<span></span>
					</button>
				</div>
			</header>