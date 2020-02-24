
// Import libraries
import Swiper from 'swiper';
import Stickybits from 'stickybits';
import UIkit from 'uikit';
import {gsap} from 'gsap';

(function(window, $) {

	'use strict';

    $.exists = function(selector) {
        return ($(selector).length > 0);
    };

	class App {

		constructor() {

			this.config();
			this.bindEvents();

		};

		/**
		 * Elements de Config
		 *
		 */
		config() {

			this.config = {
				// Main
				$window                 : $( window ),
				$document               : $( document ),
				$windowWidth            : $( window ).width(),
				$windowHeight           : $( window ).height(),
				$windowTop              : $( window ).scrollTop(),
				$viewportWidth          : '',
				$body                   : $( 'body' ),
				$masterHeader           : $( '#masthead' ),
				$headerHeight           : $( '#masthead' ).height(),

				//Mobile
				$isMobile 				: false,
				$isScreenUnder768 		: false,
				$mobileNav				: $('#sidebar-nav'),

				//scroll
				$isScrolled				: false,

				// Header
				$hasFixedHeader         : false,

				// Pied d'article
				$hasEntryFooter        : 	$.exists('.entry-footer'),

			};
		}

		init() {
			console.info( 'App Initialized' );
		}

		bindEvents() {
			
			var self = this;

			this.scrollObserver();

			// Executé au document ready
			self.config.$document.ready( function() {

				// pb doubleclick des hover sur touchScreen
				//$("*").on("touchend", function(e) { $(this).focus(); });

				// Mise à jour des variables
				self.initUpdateConfig();
				self.resizeUpdateConfig();
				self.resizeUpdateMobileMenu();

				// sticky header
				//$('#masthead').Stickybits({useStickyClasses: true, useGetBoundingClientRect: true});
				UIkit.util.on('#sidebar-nav', 'show', function () {
					$('#sidebar-nav').css('marginTop', $( '#masthead' ).height());
					self.resizeUpdateMobileMenu();
				});


				// initialisation des carousels du site
				if ( $.exists('.slider--homeSlider') )
					self.initSlider();//'.js__carousel--actus', {nav:true,dots: false,slideBy:3,slideSpeed:300,paginationSpeed:400,items:3,loop: false,autoplay:false,autoWidth:true});
			
				// initialisation des carousels du site
				if ( $.exists('.b-experience--slider') )
					self.initSliderExperience();

				if ( $.exists('.b-issues--slider') )
					self.initSliderIssues();

				// initialisation des carousels du site
				if ( $.exists('.slider--newsSlider') )
					self.initSliderNews();//'.js__carousel--actus', {nav:true,dots: false,slideBy:3,slideSpeed:300,paginationSpeed:400,items:3,loop: false,autoplay:false,autoWidth:true});
				
				if ( $.exists('.slider--events') )
					self.initSliderEvents();

				if ( $.exists('.slider--related') )
					self.initSliderRelated();
				
				if ( $.exists('.nav-filter') )
					self.initFilterMenuAnimation('.nav-filter');


				if ( $.exists('.slider-hospitalite') )
					self.initSliderHospitalite('.slider-hospitalite');

				if ( $.exists('.offres-grid') )
					self.initFiltresOffres();

				$('.js_close-modal').on('click', function(event) {
					event.preventDefault();
					UIkit.modal($(this).closest('.uk-modal')).hide();
				});


				let deco = '<div class="deco-cercles"><span class="forme-disque bg-grad-green"></span><span class="forme-disque bg-grad-green"></span></div>';
				
				if ( $('#section--billetterie').length > 0 )
					$(deco).insertAfter('#section--billetterie');

				if ( $('.caldera_forms_form').length > 0 )
					$(deco).appendTo('.site-content');

				self.customizeMobileNavFilter();
				
				self.animationsPageProduit();
				self.resizeUpdateProductPage();

				$('.page-entreprise').on('click', '.back-rubbon', function(event) {
					event.preventDefault();
					window.history.back();
				});





			} );

			// Exécuté au window load
			self.config.$window.on( 'load', function() {

				// Mise à jour de la config
				self.windowLoadUpdateConfig();
					self.config.$mobileNav.css('marginTop', self.config.$headerHeight);

			} );

			// Executé au redimmensionnement
			self.config.$window.resize( function() {

				// Modification de la largeur
				if ( self.config.$window.width() != self.config.$windowWidth ) {
					self.resizeUpdateConfig(); // mise à jour des variables
					self.animationsPageProduitResizeHandler();
					self.resizeUpdateProductPage();
					self.resizeUpdateMobileMenu();
				}

				// Modification de la hauteur
				if ( self.config.$window.height() != self.config.$windowHeight ) {
				}

			} );


			// Changement d'orientation
			self.config.$window.on( 'orientationchange',function() {
				self.resizeUpdateConfig();
				self.animationsPageProduitResizeHandler();
				self.resizeUpdateProductPage();
				self.resizeUpdateMobileMenu();
			} );
			
		}

		/**
		 * MAJ config sur doc ready
		 *
		 */
		initUpdateConfig() {

			// Largeur du Viewport
			this.config.$viewportWidth = this.viewportWidth();
			this.config.$headerHeight = this.config.$masterHeader.height(); 
			this.config.$mobileNav.css('marginTop', this.config.$headerHeight);

			// Detection mobile simple
			if ( this.mobileCheck() ) {
				this.config.$isMobile = true;
				this.config.$body.addClass( 'arena-is-mobile-device' );
			}
			// Detection taille ecran au load
			if ( this.screenUnder768() ) {
				this.config.$isScreenUnder768 = true;
				this.config.$body.addClass( 'arena-is-under-768' );
			}


		}

		/**
		 * MAJ config sur window load
		 *
		 */
		windowLoadUpdateConfig() {

		}

		/**
		 * Mise à jour de la config à chaque redimmensionnement
		 *
		 * @since 1.0.0
		 */
		resizeUpdateConfig() {

			// Paramètres globaux
			this.config.$windowHeight  = this.config.$window.height();
			this.config.$windowWidth   = this.config.$window.width();
			this.config.$windowTop     = this.config.$window.scrollTop();
			this.config.$viewportWidth = this.viewportWidth();
			this.config.$headerHeight  = this.config.$masterHeader.height(); 

			if (this.config.$windowWidth < 768) {
				//move RS menu to sidebar
				if ( !$.exists('#sidebar-nav .uk-offcanvas-bar .header__topnav') ) {
					$('.header__topnav').appendTo('#sidebar-nav .uk-offcanvas-bar');
				}
			} else {
				if ( !$.exists('#masthead .header__topnav') ) {
					$('.header__topnav').appendTo('#masthead>.container>.row');
				}
			}

			this.config.$mobileNav.css('marginTop', this.config.$headerHeight);

			this.config.$headerHeight  = this.config.$masterHeader.height(); 
		}

		/**
		 * Some mobile menu parameters to update on resize
		 *
		 * @since 1.0.0
		 */
		resizeUpdateMobileMenu() {
			$('#sidebar-nav .uk-offcanvas-bar').css('paddingBottom', $('.header__topnav').innerHeight()+'px');
			$('#sidebar-nav .uk-offcanvas-bar .uk-nav').css('maxHeight', 'calc(100vh - '+$('#masthead').innerHeight()+'px - '+$('.header__topnav').innerHeight() + 'px)');
		}

		/**
		 * Porduct page responsive resize handler
		 *
		 * @since 1.0.0
		 */
		resizeUpdateProductPage() {

			let $listeBillets = $('<div class="widget widget-meetings"><h2 class="">BILLETTERIE</h2><ul class="list-group list-group--billets"></ul></div>');
			let $listeOffres  = $('<div class="widget widget-offres"><h2 class="">OFFRES</h2><ul class="list-group list-group--offres"></ul></div>');

			// close other open panel
			$('.sidebar-mobile .sm__header').on('click', 'button', function(event) {
				var header = $(this).parent()[0];
				//$(this).parent().siblings('.sm__header').find('button').trigger('click');
				//$('.sidebar-mobile .sm__content[aria-hidden="false"]').attr('hidden', '').attr('aria-hidden', 'true');
				$('.sidebar-mobile .sm__content[aria-hidden="false"]').each(function(index, val) {
					 if ( $(this).prev().not(header).length > 0 ){
						$(this).attr('hidden', '').attr('aria-hidden', 'true');
					 }
				});
				
			});

			if (this.config.$windowWidth < 768) {
				//move billetterie sidebar to bottom
				if ( $.exists('.single-produit') && ! $('.sidebar').hasClass('mode-mobile')) {
					if ($('.list-group--billets>li').length > 0) {
						if (!$('aside .sm__content-reserver .list-group--billets').length > 0)
							$listeBillets.appendTo('aside .sm__content-reserver');
						$('.list-group--billets>li').appendTo('aside .sm__content-reserver .list-group--billets');
					}
					if ($('.list-group--offres>li').length > 0) {
						if (!$('aside .sm__content-reserver .list-group--offres').length > 0)
							$listeOffres.appendTo('aside .sm__content-reserver');
						$('.list-group--offres>li').appendTo('aside .sm__content-reserver .list-group--offres');
					}
					if ($('#form_vip_offre_contact').length > 0) {
						$('#form_vip_offre_contact').appendTo('aside .sm__content-contactform');
					}
					$('.sidebar').addClass('mode-mobile');
				}
			} else {
				if ( $.exists('aside.mode-mobile')) {
					$('.list-group--billets>li').appendTo('aside .sd__content .list-group--billets');
					$('.list-group--offres>li').appendTo('aside .sd__content .list-group--offres');
					if ($('#form_vip_offre_contact').length > 0) {
						$('#form_vip_offre_contact').appendTo('.wrapper-contact-form>div');
					}
					$('.sidebar').removeClass('mode-mobile');
				}
			}
		}

		/**
		 * customizeMobileNavFilter : used on hospitalities pages 
		 * to extract some nav subitems to be standalone buttons
		 *
		 * @since 1.0.0
		 */
		customizeMobileNavFilter() {
				$('#modal-nav .js-alone-mobile').each(function(index, el) {
					let buttonAlone = $('<button class="btn btn-outline-white btn-has-no-icon"></button>').appendTo('.modal-button-wrapper');
					$(this).children().appendTo(buttonAlone);
				});
		}

		/**
		 * Initialize sliders
		 *
		 * @since 1.0.0
		 */
		initSliderNews() {

			var homeSlider = new Swiper('.slider--newsSlider', {
			    speed: 400,
			    spaceBetween: 10,
			    slidesPerView: 1,
			    watchOverflow: true,

				// Navigation arrows
				navigation: {
					nextEl: '.swiper-button-next',
					prevEl: '.swiper-button-prev',
				},

				//event
				on: {
					init: updateClass,
					transitionEnd: updateClass,
				}
			});

			function updateClass() {
			  $(this.el).attr('data-context', $(this.el).find('.swiper-slide-active').attr('data-theme') );
			  $(this.el).attr('data-slidetype', $(this.el).find('.swiper-slide-active').attr('data-slidetype') );
			}
		}

		/**
		 * Initialize sliders
		 *
		 * @since 1.0.0
		 */
		initSlider() {

			var homeSlider = new Swiper('.slider--homeSlider', {
			    speed: 400,
			    spaceBetween: 10,
			    slidesPerView: 1,
			    watchOverflow: true,

				// Navigation arrows
				navigation: {
					nextEl: '.swiper-button-next',
					prevEl: '.swiper-button-prev',
				},

				//event
				on: {
					init: updateClass,
					transitionEnd: updateClass,
				}
			});

			function updateClass() {
			  $(this.el).attr('data-context', $(this.el).find('.swiper-slide-active').attr('data-theme') );
			  $(this.el).attr('data-slidetype', $(this.el).find('.swiper-slide-active').attr('data-slidetype') );

			}

		}

		/**
		 * Initialize sliders
		 *
		 * @since 1.0.0
		 */
		initSliderExperience() {

			var experienceSlider = new Swiper('.b-experience--slider', {
		    	slidesPerView: 1,
				centeredSlides: false,
				spaceBetween: 30,
		    	grabCursor: true,
			    watchOverflow: true,

				// Navigation arrows
				navigation: {
					nextEl: '.swiper-button-next',
					prevEl: '.swiper-button-prev',
				}
			});
		}

		initSliderIssues() {
			if(window.innerWidth < 768){
				var experienceSlider = new Swiper('.b-issues--slider', {
	      			slidesPerView: 'auto',
					centeredSlides: true,
					spaceBetween: 30
				});
			}
		}

		/**
		 * Initialize sliders
		 *
		 * @since 1.0.0
		 */
		initSliderRelated() {

			let dataPerSlide = parseInt($('.slider--related').attr('data-perslide'));

			if (this.config.$isScreenUnder768)
				dataPerSlide = 'auto';

			console.log('dataPerSlide:'+dataPerSlide);

			var relatedSlider = new Swiper('.slider--related', {
		    	slidesPerView: dataPerSlide,
				centeredSlides: false,
				spaceBetween: 30,
		    	grabCursor: true,
			    watchOverflow: true,

				// Navigation arrows
				navigation: {
					nextEl: '.swiper-button-next',
					prevEl: '.swiper-button-prev',
				}
			});
		}

		/**
		 * Initialize list filters
		 *
		 * @since 1.0.0
		 * @doc https://stackoverflow.com/questions/49317780/idangero-us-swiper-update-function-does-not-work-after-show-hide-of-slides
		 */
		initSliderEvents() {
			let sliderElt = '.slider--events';

			if (this.config.$isScreenUnder768)
				this.initFilterHomeSliderMobile( sliderElt );
			else
				this.initFilterHomeSliderDesktop( sliderElt );
		}

		/**
		 * Initialize list filters
		 *
		 * @since 1.0.0
		 * @doc https://stackoverflow.com/questions/49317780/idangero-us-swiper-update-function-does-not-work-after-show-hide-of-slides
		 */
		initFilterHomeSliderMobile( sliderElt ) {

			var $navFilter   = $(sliderElt).closest('.wrapper-slider').siblings('.wrapper-filters').find('.nav-filter'),
				$slide	     = $(sliderElt).find('.swiper-slide'),
				$cards	     = $(sliderElt).find('.card'),
				$hiddenSlide;

			let options = {
				slidesPerView: 'auto',
				centeredSlides: false,
				spaceBetween: 15,
				grabCursor: true,
				navigation: false,
				observer:true
			}

			let homeSlider = new Swiper(sliderElt, options);

			//put all cards as a slide
			$cards.appendTo($(sliderElt).find('.swiper-wrapper')).addClass('swiper-slide');
			$slide.filter('.card__desk').remove();

			
			$navFilter.on('click', 'li', function(event) {
				event.preventDefault();
				var filter = $(this).attr('data-filter');
				if (filter=="racing92")
					$('#section--billetterie').attr('data-context', filter);
				else
					$('#section--billetterie').attr('data-context', 'concert');
				
				$cards.addClass('plda-fade-enter');
			  
			  	if(filter=="all"){
			  		$cards.removeClass("non-swiper-slide").addClass("swiper-slide").show();
				}
				else
				{
					$cards.removeClass("swiper-slide").addClass("non-swiper-slide").hide();
					$cards.filter("[data-js-filter='"+filter+"']").removeClass("non-swiper-slide").addClass("swiper-slide").show();
				}

				homeSlider.destroy();
				homeSlider = new Swiper(sliderElt, options);

			})
		}

		/**
		 * Initialize list filters
		 *
		 * @since 1.0.0
		 * @doc https://stackoverflow.com/questions/49317780/idangero-us-swiper-update-function-does-not-work-after-show-hide-of-slides
		 */
		initFilterHomeSliderDesktop( sliderElt ) {

			var $navFilter   = $(sliderElt).closest('.wrapper-slider').siblings('.wrapper-filters').find('.nav-filter'),
				$slide	     = $(sliderElt).find('.swiper-slide'),
				$cards	     = $(sliderElt).find('.card'),
				$hiddenSlide;

			let options = {
					slidesPerView: 'auto',
					centeredSlides: true,
					spaceBetween: 150,
					grabCursor: true,
					observer:true,
					navigation: {
						nextEl: '.swiper-button-next',
						prevEl: '.swiper-button-prev',
					}
				}
			let homeSlider = new Swiper(sliderElt, options);

				if (! ($('.hidden-slide').length > 0)) {
					$hiddenSlide = $('<div class="hidden-slide"></div>');
					$(sliderElt).find('.swiper-wrapper').append($hiddenSlide);
					$hiddenSlide.hide();
				}
				
				$navFilter.on('click', 'li', function(event) {
					event.preventDefault();
					var filter = $(this).attr('data-filter');
					if (filter=="racing92")
						$('#section--billetterie').attr('data-context', filter);
					else
						$('#section--billetterie').attr('data-context', 'concert');
					
					$cards.addClass('plda-fade-enter');
				  
				  	if(filter=="all"){
				  		$slide.removeClass("non-swiper-slide").addClass("swiper-slide").show();
				  		$cards.each(function(index, el) {
				  			$(this).appendTo($slide.eq(Math.floor(index/6)));
							$cards.removeClass('plda-fade-enter').addClass('plda-fade-enter');
				  		});
					}
					else
					{
						$cards.appendTo($hiddenSlide);
						$slide.removeClass("swiper-slide").addClass("non-swiper-slide").hide();
						$cards.filter("[data-js-filter='"+filter+"']").each(function(index, el) {
							$(this).appendTo($slide.eq(Math.floor(index/6)));
							$slide.eq(Math.floor(index/6)).removeClass("non-swiper-slide").addClass("swiper-slide").show();
							$(this).removeClass('plda-fade-enter').addClass('plda-fade-enter');
						});
					}

					homeSlider.destroy();
					homeSlider = new Swiper(sliderElt, options);

				})

		}

		/**
		 * Animations page billetterie
		 *
		 * @since 1.0.0
		 */
		animationsPageProduit() {
			let self = this;
			$('.list-group:not(.no-animation)').each(function(index, el) {
				self.animationsPageProduitResizeHandler();
				$('.single-produit')
				.on('mouseover', '.list-item:not(.item--no-animation)', function(event) {
					$(this).find('.svg--dgrad').addClass('full');
				})
				.on('mouseleave', '.list-item:not(.item--no-animation)', function(event) {
					$(this).find('.svg--dgrad').removeClass('full');
				});
			});
		}
		
		animationsPageProduitResizeHandler() {
			$('.list-item:not(.item--no-animation)').each(function(index, el) {
				$(this).find('.svg--dgrad').css('width', $(this).find('.list-item__extra').outerWidth()+'px');
			});
		}


		/**
		 * Animate filter menu items
		 *
		 * @since 1.0.0
		 */
		initFilterMenuAnimation( elt ) {

			var handle = function(el) {
			  var $el = $(el)
			  $el
			    .addClass("selected")
			    .siblings()
			    .removeClass("selected");
				if ($.exists($el.siblings(".nav_indicator"))){
  				  $el.siblings(".nav_indicator")
  				    .css("left", $el.position().left + "px")
  				    .css("width", $el.width() + "px")
  				    .css("border-width", "1px")
  				    .css("padding-left", $el.css("padding-left"))
  				    .css("padding-right", $el.css("padding-right"))
  				}
  				if ($.exists($el.closest('.uk-modal.js-close-on-click')))
  					UIkit.modal($el.closest('.uk-modal')).hide();

			}

			var $target, $subFilter, $subLi;
			var self = this;

			if ( $target = $(elt).attr('data-target') )
				this.initFilterBilletterie( elt, $target );

			// Click handler
			$( elt + " > li" ).on("click", function(event) {
			  handle(this)
			})

			if ( $subFilter = $(elt).siblings('.nav-subfilter') ){
				$subFilter.on('click', '>li', function(event) {
					event.preventDefault();
					handle(this);

				});
			}

			// Initialize nav indicator position
			var hash = $(location).attr('hash');
			if(hash.length > 1)
			{
				hash = hash.substring(1);

				if ( $(elt).parent().find('li[data-filter="'+hash+'"]').length > 0 ){
					$(elt).parent().find("li.selected").removeClass('selected');
					$(elt).parent().find("li").filter('[data-filter="'+hash+'"]').addClass('selected');
					$(elt).parent().find("li").filter('[data-filter="'+hash+'"]').trigger('click');
				}
				if ($('.nav-subfilter .selected').length > 0)
					$( elt + ' > li[data-filter="offres"]' ).addClass('selected');
				else
					$subFilter.slideUp(400); 

			}else{
				$subFilter.slideUp(400); 
			}
			handle(elt + ' > li.selected');

			//handle($(elt + " > li.selected"));

			self.config.$window.resize(function(){
				handle($(elt + " > li.selected"));
			})

		}

		/*!
		 * Init filter behaviors for Billetterie page
		 */
		initFilterBilletterie( eltName, gridName ) {

			var $navFilter   	= $(eltName),
				$subFilter,
				$cards	     	= $('.'+gridName).find('.card'),
				$categoriesList = $('.categories-list');
			
			//var tl = gsap.timeline();

			const animDescription = (filtre) => {
				var tl = gsap.timeline({defaults:{ease:"power2.out"}});
					tl.to($categoriesList.find('>div'), 0.3, {opacity:0,display:"none"});
					//gsap.to($categoriesList, 1, {height:0});
					// $categoriesList.find('>div').removeClass('plda-fade-enter').addClass('plda-fade-exit');
					// $categoriesList.css('height', '0');

					if ($.exists('.categories-list>[data-category="'+filtre+'"]'))
					{
						let $catItem = $('.categories-list>[data-category="'+filtre+'"]');

						$('.titre-categorie').html($catItem.find('.category__titre').clone());

						tl.set($catItem,{display:'block',opacity:0});
						tl.set($categoriesList,{height:$categoriesList.height()});
						tl.to($categoriesList, 0.5, {height:'auto'});
						tl.to($catItem, 0.3, {opacity:1},"-=0.3");

						// $categoriesList.css('height', $catItem.height());
						// $catItem.removeClass('plda-fade-exit').addClass('plda-fade-enter');
						// setTimeout(() => $categoriesList.css('height', 'auto'), 500);
					}else{
						tl.to($categoriesList, 0.5, {height:0});
						$('.titre-categorie').html('BILLETTERIE');
					}
			};

			$cards.filter('.famille_produit_offre').removeClass("active-card").addClass("hidden-card").removeClass('plda-fade-enter').hide();
			
			//replacer la promo en 3eme position des 'events' (car les offres sont cachées)
			if ( $cards.filter('.famille_produit_promo').length>0 )
				$cards.filter('.famille_produit_event').eq(1).after($cards.filter('.famille_produit_promo'));

			$(document).on('click', '.back-rubbon', function(event) {
				event.preventDefault();
				$('.uk-modal .filter--offres .selected').removeClass('selected');
				$('li[data-filter=all]').trigger('click');
			});
			$navFilter.on('click', 'li', function(event) {
				event.preventDefault();
					  				
				if ( !($(this).closest('.uk-modal').length > 0) )
					$(".filter--offres").slideUp(400); 

				if ( $(this).closest('.filter--offres').length > 0 ) {
					$('.back-rubbon').show();
					gsap.to('.modal-button-wrapper button', 0.3, {opacity:0,display:"none"});
					gsap.to('.modal-button-wrapper', 0.3, {margin:0});
					gsap.to('.entry-header', 0.3, {marginTop:'2rem'});
				}
				else {
					$('.back-rubbon').hide();
					gsap.to('.modal-button-wrapper button', 0.3, {opacity:1,display:"block"});
					gsap.to('.modal-button-wrapper', 0.3, {clearProps: "all"});
					gsap.to('.entry-header', 0.3, {clearProps: "all"});
				}

				var filter = $(this).attr('data-filter');

				$('body').attr('data-context', filter);
				
				$cards.removeClass('plda-fade-enter');

				animDescription(filter);
			  
			  	if(filter=="all"){
					$cards.filter('.famille_produit_offre').removeClass("active-card").addClass("hidden-card").removeClass('plda-fade-enter').hide();
			  		$cards.filter(':not(.famille_produit_offre)').removeClass("hidden-card").addClass("active-card").addClass('plda-fade-enter').show();
			  		$('.modal-button-wrapper button.js-btn-events-list-toggle').contents().filter(function() {
					    return this.nodeType == 3
					}).remove();
					$('.modal-button-wrapper button.js-btn-events-list-toggle').prepend('Types d\'événements');
				} else if ( filter=="offres" ) {
					//$(".filter--offres li").eq(0).addClass('selected');
					$(".filter--offres").slideDown(400);
					$navFilter.siblings('.nav-subfilter').find('li').first().trigger( "click" );
			  		$('.modal-button-wrapper button.js-btn-events-list-toggle').contents().filter(function() {
					    return this.nodeType == 3
					}).remove();
					$('.modal-button-wrapper button.js-btn-events-list-toggle').prepend('Types d\'événements');
					
				} else if ( filter=="packages" ) {
					window.location = $(this).attr('data-link');
				}
				else
				{
					$cards.removeClass("active-card").addClass("hidden-card").removeClass('plda-fade-enter').hide();
					$cards.filter("[data-js-filter='"+filter+"']").each(function(index, el) {
			  			$(this).removeClass("hidden-card").addClass("active-card").addClass('plda-fade-enter').show();
					});
			  		$('.modal-button-wrapper button.js-btn-events-list-toggle').contents().filter(function() {
					    return this.nodeType == 3
					}).remove();
					$('.modal-button-wrapper button.js-btn-events-list-toggle').prepend(filter);
				}

			});

			if ( $subFilter = $(eltName).siblings('.nav-subfilter') ){
				
				$subFilter.on('click', '>li', function(event) {
				
					var filter = $(this).attr('data-filter');

					$('body').attr('data-context', filter);
					
					$cards.removeClass('plda-fade-enter');

					animDescription(filter);

					if ( filter=="packages" ) {
						window.location = $(this).attr('data-link');
					}
				  
				  	$cards.removeClass("active-card").addClass("hidden-card").removeClass('plda-fade-enter').hide();
					$cards.filter("[data-js-filter='"+filter+"']").each(function(index, el) {
			  			$(this).removeClass("hidden-card").addClass("active-card").addClass('plda-fade-enter').show();
					});
				});
			}
		}

		/*!
		 * Offres filter in page Hospitality
		 */
		initFiltresOffres() {

			let self = this;

			var $checkboxes    = $('.nav-filter').find('input[type=checkbox]'),
				$cards		   = $('.offres-grid').find('.card'),
	        	filters,
	            filtersMobile  = {},
	            filtersDesktop = {};

	        const initFilterMobile = () => {
		          filters = filtersMobile;
		          updateGridElements();
	          if ( filters != filtersMobile ) {
	          }
	        };
	        const initFilterDesktop = () => {
		          filters = filtersDesktop;
		          updateGridElements();
	          if ( filters != filtersDesktop ) {
	          }
	        };

	        self.config.$window.resize( function() {

				if (self.config.$windowWidth < 768) {
	        		initFilterMobile();
				}else{
	        		initFilterDesktop();
				}
			});
			
			filters = (self.config.$isScreenUnder768) ? filtersMobile : filtersDesktop;
	        

	        function updateGridElements() {
				$cards.removeClass("active-card").addClass("hidden-card").removeClass('plda-fade-enter').hide();
				$cards.filter(getComboFilter())
					  .each(function(index, el) {
		  				$(this).removeClass("hidden-card").addClass("active-card").addClass('plda-fade-enter').show();
					});
	        }

	        function getComboFilter() {
	        	var combo = [];
	        	for ( var prop in filters ) {
	        		var group = filters[ prop ];
	        		if ( !group.length ) {
						// no filters in group, carry on
						continue;
					}
					// add first group
					if ( !combo.length ) {
						combo = group.slice(0);
						continue;
					}
					// add additional groups
					var nextCombo = [];
					// split group into combo: [ A, B ] & [ 1, 2 ] => [ A1, A2, B1, B2 ]
					for ( var i=0; i < combo.length; i++ ) {
						for ( var j=0; j < group.length; j++ ) {
							var item = combo[i] + group[j];
							nextCombo.push( item );
						}
					}
				combo = nextCombo;
				}
				var comboFilter = combo.join(', ');
				comboFilter = comboFilter ? comboFilter : '*';
				return comboFilter;
			};


			// actions sur checkbox change
			$checkboxes.on( 'change', function( event ) {
			  var checkbox = event.target;
			  var $checkbox = $( checkbox );
			  var group = $checkbox.parents('.filter__group').attr('data-group');
			  // create array for filter group, if not there yet
			  var filterGroup = filters[ group ];
			  if ( !filterGroup ) {
			    filterGroup = filters[ group ] = [];
			  }
			  // add/remove filter
			  if ( checkbox.checked ) {
			    // add filter
			    filterGroup.push( '.'+group+'_'+checkbox.value );
			  } else {
			    // remove filter
			    var index = filterGroup.indexOf( '.'+group+'_'+checkbox.value );
			    filterGroup.splice( index, 1 );
			  }
			    updateGridElements();
			  
			});
			if (default_filtre_offre){
				$checkboxes.filter(function() {
				  return this.value == default_filtre_offre;
				}).prop("checked","checked").change();
			};
		}

		/*!
		 * Construct Hospitality page slider
		 * @param  {Node}  elem The element containing slider + Text
		 */
		initSliderHospitalite( elem ) {
			var $sliderWrapper  = $(elem),
				$sliderText 	= $(elem+' .slider-text').first();

			$sliderWrapper.find('.bg--slider-ecailles').clone().prependTo($sliderText);

			var hospitaliteSlider = new Swiper(elem+' .swiper-container', {
				speed: 400,
				spaceBetween: 0,
				slidesPerView: 1,
				pagination: {
					el: '.swiper-dots-nav',
					type: 'bullets',
					clickable: true,
				},

				// Navigation arrows
				navigation: {
					nextEl: '.swiper-button-next',
					prevEl: '.swiper-button-prev',
				}
		  });

		}

		/*!
		 * Check if an element is out of the viewport
		 * @param  {Node}  elem The element to check
		 * @return {Object}     A set of booleans 	for each side of the element
		 */
		isOutOfViewport( elem ) {

			// Get element's bounding
			var bounding = elem.getBoundingClientRect();

			// Check if it's out of the viewport on each side
			var out = {};
			out.top = bounding.top < 0;
			out.left = bounding.left < 0;
			out.bottom = bounding.bottom > (window.innerHeight || document.documentElement.clientHeight);
			out.right = bounding.right > (window.innerWidth || document.documentElement.clientWidth);
			out.any = out.top || out.left || out.bottom || out.right;
			out.all = out.top && out.left && out.bottom && out.right;

			return out;

		}

		/**
		 * Déclenchement de toutes les fonctions liées au scroll
		 *
		 * @since 1.0.0
		 */
		scrollEventHandlers() {

			var self = this;

			(this.config.$windowTop === 0 ) ? this.config.$body.removeClass('is-scrolled') : this.config.$body.addClass('is-scrolled');

			// detect if sharebox over footer article

			if ( this.config.$hasEntryFooter ){
				var elem = document.querySelector('.entry-footer');
				var isOut = self.isOutOfViewport(elem);

				if (isOut.top) {
					self.config.$body.addClass('is-over-footer');
				}else{
					self.config.$body.removeClass('is-over-footer');
				}
			}
		}

		/**
		 * Gestion du scroll
		 *
		 * @since 1.0.0
		 */
		scrollObserver() {
			var self = this;

			// Paramètres globaux
			var raf = window.requestAnimationFrame ||
			    window.webkitRequestAnimationFrame ||
			    window.mozRequestAnimationFrame ||
			    window.msRequestAnimationFrame ||
			    window.oRequestAnimationFrame;

			if (raf) {
			    loop();
			}else{
				var waiting = false, endScrollHandle;
				self.config.$window.scroll(function () {

				    if (waiting) {
				        return;
				    }
				    waiting = true;

				    // clear previous scheduled endScrollHandle
				    clearTimeout(endScrollHandle);

				    self.scrollEventHandlers();

				    setTimeout(function () {
				        waiting = false;
				    }, 100);

				    // schedule an extra execution of scroll() after 200ms
				    // in case the scrolling stops in next 100ms
				    endScrollHandle = setTimeout(function () {
				        self.scrollEventHandlers();
				    }, 200);
				});
			}

			function loop() {
			    var scrollTop = self.config.$window.scrollTop();
			    if (self.config.$windowTop === scrollTop) {
			        raf(loop);
			        return;
			    } else {
			        self.config.$windowTop = scrollTop;

			        // Execution des fonctions liées au scroll
			        self.scrollEventHandlers();
			        raf(loop);
			    }
			}

		}

		/**
		 * Detection mobile
		 *
		 * @since 1.0.0
		 */
		mobileCheck() {
			if ( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test( navigator.userAgent ) ) {
				return true;
			}
		}

		/**
		 * Detection taille ecran
		 *
		 * @since 1.0.0
		 */

		screenUnder768() {      
		    let isMobile = window.matchMedia("only screen and (max-width: 767px)").matches;

		    if (isMobile) {
		        return true;
		    }
		 }


		/**
		 * Viewport width
		 *
		 */
		viewportWidth() {
			var e = window, a = 'inner';
			if ( !( 'innerWidth' in window ) ) {
				a = 'client';
				e = document.documentElement || document.body;
			}
			return e[ a+'Width' ];
		}

	}
    window.App = App;
})(window, jQuery);

export default App;
