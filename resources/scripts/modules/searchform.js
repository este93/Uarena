class SearchForm {

	constructor() {
		this.init();
	}

	init() {

		(function(window, $) {
			$(document).on('keypress', '.search-field', () => {

			  var empty = false;
			  $('.search-field').each(function() {
			      if ($(this).val().length == 0) {
			          empty = true;
			      }
			  });                   

			  if (empty) {
			      $('.js_go-search').attr('disabled', 'disabled');
			  } else {
			      $('.js_go-search').removeAttr('disabled');
			  }                
			});
			$('.search-field').each(function(index, el) {
				$(this).trigger('keyup');
			});
		})(window, jQuery);

	}
}

export default SearchForm;
