<?php
/**
 * Template Name: Listing offres VIP
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package plda
 */

get_header(); ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">

		<?php

		/* Start the Loop */
		while ( have_posts() ) :
			the_post();

			get_template_part( 'views/content', 'offres-vip' );

		endwhile;

		?>

	</main><!-- #main -->
</div><!-- #primary -->
<script type="text/javascript">
	var default_filtre_offre = '';
	<?php if ( $default_filtre_offre = carbon_get_the_post_meta('page_filtre_offre_init') ) {	?>
		default_filtre_offre = '<?php echo $default_filtre_offre ?>';
	<?php } ?>
</script>
<?php
get_footer();
