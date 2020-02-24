<?php 
$genre_evenement = Plda\Core\tags::get_post_tax_terms( get_the_ID(), 'category', true );
$category 		 = (!empty($genre_evenement)) ? '<p class="card__category">' . implode(' ', $genre_evenement) . '</p>':'';
?>

<div>
<div class="card card-article">
	<a href="<?php the_permalink() ?>" class="full-zone" title="<?php echo esc_attr(get_the_title()) ?>"></a>
    <div class="card__block-image">
        <?php 
		if( has_post_thumbnail() ){
			the_post_thumbnail('vignette_news_@x2', array('class' => 'card-img-top'));
		} ?>
    </div>
    <div class="card__body">
    	<?php echo $category; ?>
        <h3 class="card__title"><?php the_title() ?></h3>
    </div>
</div>
</div>