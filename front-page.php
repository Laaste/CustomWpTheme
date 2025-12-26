<?php 
$siteUrl = site_url();
$currentId = currentID();
pageRedirect($currentId);
?>

<?php get_header(); ?>

<?php linkCssForTemplate(); ?>

<?php composableSections($currentId, []); ?>
<?php get_footer(); ?>