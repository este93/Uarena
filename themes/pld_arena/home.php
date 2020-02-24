<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package plda
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
			<?php 
			// Slider des actualités
			echo do_shortcode( '[plda_home_slider nbre_item=5 tag_selection=newsSlider page=blog post_type=post default_button_label=LIRE]', false );
			?>

			<div class="wrapper-articles" id="section-articles">
				<div class="to-anchor display-block-up-md"><a href="#section-articles" uk-scroll><?php inlineSvg('icon-bulle-fleche') ?></a></div>
				<div class="container wrapper__content--contracted">
					
					<header>
						<h1 class="text-center titre-categorie">LES ARTICLES</h1>
					</header>

					<div class="wrapper-grid articles-grid">

					<?php
					if ( have_posts() ) :
					?>
						<?php
						/* Start the Loop */
						while ( have_posts() ) :

							the_post();

							get_template_part( 'components/article', 'cards' );	 
							
						endwhile;

						the_posts_navigation();

					else :

						get_template_part( 'views/content', 'none' );

					endif;
					?>

					</div>
				</div>
			</div>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();
