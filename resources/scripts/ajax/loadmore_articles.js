
let canBeLoaded = true; // Ajax call only if needed

const loadOnBottom = function() {
		let windowHeight = (window.innerHeight || document.documentElement.clientHeight),
			elem = document.querySelector('.site-footer'),
			bounding = elem.getBoundingClientRect();

		if ( bounding.top > 0 && bounding.top < windowHeight && canBeLoaded == true ) {

			var data = {
				'action': 'loadmore_articles',
				'query': plda_loadmore_articles_params.posts, // params from wp_localize_script() function
				'page' : plda_loadmore_articles_params.current_page
			};

			(function(window, $) {
				$.ajax({
					url : plda_loadmore_articles_params.ajaxurl,
					data:data,
					type:'POST',
					beforeSend: function( xhr ){
						// TODO preloader
						// don't run again
						canBeLoaded = false; 
					},
					success:function(data){
						if( data && data != 'EOF' ) {
							$('.articles-grid>div:last-child').after( data ); // Posrts insertion
							canBeLoaded = true; // ajax completed, can run it again
							plda_loadmore_articles_params.current_page++;
						}
					}
				});
			})(window, jQuery);
		}
	}

class LoadMoreArticles {

	constructor() {
	    this.windowTop = 0;
		this.init();
	}

	init() {
		let self = this;
		document.addEventListener("DOMContentLoaded", function() {
					// Paramètres globaux
			const raf = window.requestAnimationFrame ||
			    window.webkitRequestAnimationFrame ||
			    window.mozRequestAnimationFrame ||
			    window.msRequestAnimationFrame ||
			    window.oRequestAnimationFrame;

			if (raf) {

			    (function scrollObserver() {
			        
			        let scrollTop = Math.max(0, document.documentElement.scrollTop || window.pageYOffset || 0);
			        //console.log('scrollTop:'+scrollTop);

			        if (self.windowTop === scrollTop) {
			            raf(scrollObserver);
			            return;
			        } else {
			            self.windowTop = scrollTop;

			            // Execution des fonctions liées au scroll
			            loadOnBottom();
			            raf(scrollObserver);
			        }
			    })();
			}

		});
	}

};

export default LoadMoreArticles;