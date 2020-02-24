<form role="search" method="get" class="search-form" action="<?php echo home_url( '/' ); ?>">
	<div class="uk-position-relative">
		<label class="screen-reader-text"><?php echo _x( 'Search for:', 'label' ) ?></label>
        <button type="submit" class="uk-form-icon uk-form-icon-flip js_go-search btn-link" disabled="disabled"><i  class="plda fa-search"></i></button>
        <input type="search" class="uk-input search-field"
            placeholder="VOTRE RECHERCHE"
            value="<?php echo get_search_query() ?>" name="s"
            title="<?php echo esc_attr_x( 'Search for:', 'label' ) ?>" />
    </div>
</form>