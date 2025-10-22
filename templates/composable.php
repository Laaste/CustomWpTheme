<?php /** Template Name: Flexible template */ ?>

<?php get_header(); ?>

<link rel="stylesheet" href="<?= get_template_directory_uri(); ?>/assets/css/composable.css">

<?php
$currentId = currentID();
pageRedirect($currentId);
?>

<div>
	<?php composableSections($currentId); ?>
</div>

<?php get_footer(); ?>