<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package plda
 */

get_header(); ?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
			<div class="container">
				<div class="row row-main"><div class="col col-main">
	
	        	<?php get_search_form(); ?>

				<?php
				if ( have_posts() ) :
				?>

					<header>
						<h1 class="page-title">
						<?php
						global $wp_query;

						printf(
							'%1$s/%2$s résultat(s) pour : %3$s',
							(string)$wp_query->post_count,
							(string)$wp_query->found_posts,
							'<span>' . get_search_query() . '</span>'
						);
						?>
						</h1>
					</header>

					<?php
					$currentPostType = $wrapperEnd = '';

					while ( have_posts() ) :
						
						the_post();
						
						if ( ! ( get_post_type() == $currentPostType ) ) {
							$currentPostType = get_post_type();
							echo $wrapperEnd;
							echo '<div class="wrapper__content wrapper--' . $currentPostType . '">';
							$titreCategorie = ( $currentPostType == 'produit' ) ? 'LES ÉVÉNEMENTS' : (( $currentPostType == 'post' ) ? 'LES ARTICLES' : 'AUTRES' );
							echo '<h2 class="titre-categorie text-left text-white">' . $titreCategorie . '</h2>';
							echo '<div class="results-grid ' . $currentPostType . '-grid">';
							$wrapperEnd = '</div></div>';
						}

						switch ($currentPostType) {
							case 'produit':
								echo \Plda\Core\Tags::get_product_card( get_the_ID() );

								break;
							case 'post':
								get_template_part( 'components/article', 'cards' );
								
								break;
							case 'page':
								get_template_part( 'components/article', 'cards' );
								
								break;
							
							default:

								break;
						}

					endwhile;
					echo $wrapperEnd;

				else :

					get_template_part( 'views/content', 'none' );

				endif;
				?>
			</div><!-- .col- -->
		</div><!-- .row -->
	</div><!-- .container -->
</main><!-- #main -->
</div><!-- #primary -->

<?php
get_footer();
