<?php 
$siteUrl = site_url();
$currentId = currentID();
pageRedirect($currentId);
?>

<?php get_header(); ?>

<section class="four-o-four">
	<h1 class="four-o-four__title">
		<?= __('Błąd 404', 'customtheme'); ?>
	</h1>

	<h2 class="four-o-four__excerpt">
		<?= __('Nie znaleziono strony', 'customtheme'); ?>
	</h2>

	<div class="four-o-four__button">
		<a href="<?= $siteUrl; ?>" class="button button--black" aria-label="<?= __('Strona główna', 'customtheme'); ?>">
			<?= __("Strona główna", 'customtheme'); ?>
		</a>
	</div>
</section>

<?php linkCssForTemplate(); ?>

<?php get_footer(); ?>