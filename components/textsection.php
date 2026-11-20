<?php
if(isset($args)
&& ! empty($args))
{
	extract($args);
}
?>

<?php
$text = get_sub_field('textsection_text', $currentId);

if(! empty($text))
{
?>

<section class="textsection" id="<?= !empty($sectionNo) ? 'section-' . $sectionNo : 'examplecomponent'; ?>" <?= (isset($sectionNo)) ? "data-section-no='$sectionNo'" : "" ?>>
	<?= $text; ?>
</section>

<?php
}
?>