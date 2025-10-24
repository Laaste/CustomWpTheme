<?php 
$siteUrl = site_url();
$currentId = currentID();
pageRedirect($currentId);
?>

<?php get_header(); ?>

<section class="four-o-four">
	<h1 class="four-o-four__title">
		<?= __('Error 404', 'customtheme'); ?>
	</h1>

	<h2 class="four-o-four__excerpt">
		<?= __('Page not found', 'customtheme'); ?>
	</h2>

	<div class="four-o-four__button">
		<a href="<?= $siteUrl; ?>" class="button">
			<?= __("Home page", 'customtheme'); ?>
		</a>
	</div>
</section>

<?php linkCssForTemplate(); ?>

<?php get_footer(); ?>