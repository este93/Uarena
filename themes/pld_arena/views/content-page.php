<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package plda
 */

	$iconePage = carbon_get_the_post_meta( 'page_filtre_icone_header' );

	$iconePage = ($iconePage)? '<span class="reduce-text-80"><i class="' . $iconePage . '"></i></span> ' : '';

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="bg-produit content-empty"></div>
	<?php if ( ! is_front_page() ) : ?>
	<div class="container">
		<div class="row row-main"><div class="col col-main">
			<header class="entry-header">
				<?php the_title( '<h1 class="entry-title">'.$iconePage, '</h1>' ); ?>
			</header><!-- .entry-header -->
	<?php endif ?>

		<div class="entry-content">
			<?php
				the_content();
			?>
		</div><!-- .entry-content -->
	<?php if ( ! is_front_page() ) : ?>
	</div></div><!-- .col-main .row-main -->
	</div><!-- .container -->
	<?php endif ?>
</article><!-- #post-## -->
