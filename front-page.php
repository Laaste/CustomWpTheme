<?php 
$siteUrl = site_url();
$currentId = currentID();
pageRedirect($currentId);
?>

<?php get_header(); ?>

<?php linkCssForTemplate(); ?>

<?php get_footer(); ?>