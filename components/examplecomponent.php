<?php
if(isset($args)
&& ! empty($args))
{
	extract($args);
}

$exapleFieldOfComposableSection = get_sub_field('section_examplefield', $currentId);
?>

<section class="examplecomponent"  id="<?= !empty($sectionNo) ? 'section-' . $sectionNo : 'examplecomponent'; ?>" <?= (isset($sectionNo)) ? "data-section-no='$sectionNo'" : "" ?>>

</section>