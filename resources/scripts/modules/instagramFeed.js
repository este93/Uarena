
import Swiper from 'swiper';

/**
 * Instagram slider
 */
export function InstagramFeed(el) {
	let instagramFeedElt = el,
		instagramFeedContainer;
	document.addEventListener("DOMContentLoaded", function(event) {
		if (!!(instagramFeedContainer = document.querySelector(instagramFeedElt))) {
			instagramFeedContainer.classList.add('swiper-container');
			instagramFeedContainer.firstElementChild.classList.add('swiper-wrapper');
			let slides = instagramFeedContainer.firstElementChild.children;
			for (var i = slides.length - 1; i >= 0; i--) {
				slides[i].classList.add('swiper-slide');
			}
			setTimeout(function(){ 
				var swiper = new Swiper( instagramFeedElt, 
					{
						slidesPerView: 'auto',
						centeredSlides: false,
						spaceBetween: 30,
						grabCursor: true
					}
				); 
			}, 2000);
			
		}

	});
}