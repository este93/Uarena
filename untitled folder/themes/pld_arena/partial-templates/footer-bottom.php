<?php
/**
 * Footer setup for footer bottom.
 *
 * @package plda
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>

	<!-- ******************* The Footer Bottom Area ******************* -->

	<div class="wrapper-section wrapper-footer_bottom" id="wrapper-footer--bottom">

		<div class="container" id="footer-bottom__content" tabindex="-1">
			<?php if ( is_active_sidebar( 'plda-footer-partenaires' ) ) : ?>

				<!-- ******************* The Footer Partners Widget Area ******************* -->

						<div class="row row--partenaires">

							<?php dynamic_sidebar( 'plda-footer-partenaires' ); ?>

						</div>

			<?php endif; ?>

			<?php if ( is_active_sidebar( 'plda-footer-rs' ) ) : ?>

				<!-- ******************* The Footer RS Widget Area ******************* -->

						<div class="row row--rs">

							<?php dynamic_sidebar( 'plda-footer-rs' ); ?>

						</div>

			<?php endif; ?>
			
			<?php if ( is_active_sidebar( 'plda-footer-menu' ) ) : ?>

				<!-- ******************* The Footer Menu Widget Area ******************* -->

						<div class="row row--menu">

							<?php dynamic_sidebar( 'plda-footer-menu' ); ?>

						</div>

			<?php endif; ?>

		</div>

	</div><!-- #wrapper-footer-full -->
