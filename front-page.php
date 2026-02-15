<?php 
$siteUrl = site_url();
$currentId = currentID();
pageRedirect($currentId);
?>

<?php get_header(); ?>

<?php linkCssForTemplate(); ?>

<link rel="stylesheet" href="<?= get_template_directory_uri() . '/assets/css/composable.css' ?>">

<?php composableSections($currentId, []); ?>

<?php get_footer(); ?>