<?php
/**
 * Footer setup for footer newsletter.
 *
 * @package plda
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>

	<!-- ******************* The Footer Newsletter Area ******************* -->

	<div class="wrapper-section wrapper-footer_top container" id="wrapper-footer--newsletter">


		<div class="row row--form">

			<div class="col-md-11">
				<div class="newsletter-main">
					<form class="text-center text-bleu-newsletter form_newsletter" id="form_newsletter" action="#">
						<h2 class="titre-hero col-md-11">RESTEZ INFORMÉS</h2>
					  <div class="form-row">
					    <div class="form-group col">
					      <input name="email" type="email" class="form-control" id="inputEmail" required placeholder="ENTREZ VOTRE ADRESSE EMAIL" />
					    </div>
					    <div class="form-group col-w-auto">
							<input type="hidden" name="services" value="NEWSLETTER_A92"/>
							<input type="hidden" name="action" value="openfield_subscribe"/>
					  		<button type="submit" class="btn"><i class="fas fa-angle-right stack-circle fz-24 plda-icon-context"></i></button>
					    </div>
					  </div>
					  <div class="form-group">
					    <div class="form-check">
					      <input class="form-check-input" type="checkbox" id="rgpdCheck" required>
					      <label class="form-check-label" for="rgpdCheck">
					        J&rsquo;accepte que Paris La D&eacute;fense Arena m&rsquo;adresse par email toute l&rsquo;actualit&eacute; de la salle. Pour en savoir plus, voir <a href="/donnees-personnelles/">la politique de confidentialit&eacute;</a>.
					      </label>
					    </div>
					  </div>
					</form>
				</div>
			</div>

		</div>
		<div class="row row--goto text-center">

			<div class="col-12">
				<?php inlineSvg('logo-parisladefense-arena-vertical') ?>
				<div class="adresse-arena">
					<span class="h2">PARIS LA DÉFENSE ARENA</span>
					<span class="titre-20">99 JARDINS DE L'ARCHE, 92000 NANTERRE</span>
				</div>
				<div class="row--btn-goto">
					<a href="/itineraire/" class="btn btn-grad-green">ITINÉRAIRE</a>
				</div>
			</div>

		</div>


	</div><!-- #wrapper-footer-full -->
