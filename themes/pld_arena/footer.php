<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package plda
 */

?>

	</div><!-- #content -->

	<?php
	if ( is_customize_preview() ) {
		echo '<div id="awps-footer-control" style="margin-top:-30px;position:absolute;"></div>';
	}
	?>

	<footer class="site-footer container-fluid" role="contentinfo">
		
		<?php get_template_part( 'partial-templates/footer', 'newsletter' ); ?>

		<?php get_template_part( 'partial-templates/footer', 'bottom' ); ?>

	</footer><!-- #colophon -->
</div><!-- #page -->

<?php get_template_part( 'partial-templates/search', 'modal' ); ?>

<?php wp_footer(); ?>

</body>
</html>
