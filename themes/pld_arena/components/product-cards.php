					
					<div class="card <?php implode(' ', \Plda\Core\Tags::get_class_of_tax( $post_id ) ) ?>" data-type="<?php array_key_first($genre_evenement) ?>">
					<?php if( has_post_thumbnail( $post_id ) ){  ?>
						<div class="card__block-image">
						<?php get_the_post_thumbnail( $post_id, 'vignette_affiche', array('class' => 'card-img-top') );  ?>
						</div>
					<?php } ?>
						<div class="card__items text-center">
						  <div class="card__item">
						    <span class="gpicon gpicon-type"></span>
						  </div>
						</div>

						<div class="card__body">
							<p class="card__category"><?php implode(' ', $genre_evenement) ?></p>
							<h3 class="card__title"><?php get_the_title( $post_id ) ?></h3>
							<p class="card__meta"><?php carbon_get_post_meta( $post_id, 'product_date_text' ) ?></p>
						</div>
						<div class="card__actions"><a href="<?php get_permalink( $post_id ) ?>" class="btn btn-context">RÉSERVER</a></div>

					</div>