<?php
$siteUrl = site_url();
$currentId = currentID();
$currentLang = apply_filters( 'wpml_current_language', NULL);
?>

			<footer class="footer">
				<div class="footer__top">
					<a href="<?= $siteUrl; ?>" aria-label="<?= __("Homepage", 'customtheme'); ?>" class="footer__top__logo__container">
						<img data-lazy-img="<?= get_template_directory_uri(); ?>/assets/images/global/logo_white.png" alt="<?= __('Logo', 'customtheme'); ?>" class="footer__top__logo">
					</a>
				</div>

				<div class="footer__bottom">
				</div>
			</footer>
		</div>
	</div>

	<?php wp_footer(); ?>

	<script src="<?= get_template_directory_uri(); ?>/assets/js/main-compiled.js"></script>
</body>
</html>