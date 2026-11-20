<?php
$siteUrl = site_url();
$currentId = currentID();
$currentPostType = get_post_type($currentId);
$currentLang = apply_filters( 'wpml_current_language', NULL);

$templateSlug = get_page_template_slug($currentId);
?>

		<footer class="footer">
			<?php
			$footerMenu = getMenuItems('footer_menu');

			if($footerMenu)
			{
			?>

			<div class="">
				<?php
				$groupZIndex = 99;

				foreach($footerMenu as $primaryMenuEl)
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

			</div>
			<?php
			}
			?>
		</footer>
	</main>

	<!-- filled by js -->
	<div class="popup__container js-popup-container" style="display: none;">
	</div>

	<?php wp_footer(); ?>

	<script src="<?= get_template_directory_uri(); ?>/assets/js/main-compiled.js?v=<?= filemtime(get_template_directory() . '/assets/js/main-compiled.js'); ?>"></script>
</body>
</html>
