import UIkit from 'uikit';

class LoadFormResa {

	constructor() {
		this.init();
	}

	init() {
		let self = this;
		document.addEventListener("DOMContentLoaded", function() {

		document.querySelectorAll('#form_newsletter').forEach(item => {
			item.addEventListener('submit', self.postNewsletterForm );
		})

		document.querySelectorAll('.btn.is-reservable').forEach( item => {
			item.addEventListener('click', self.displayFormHandler);
		});
		document.querySelectorAll('aside .dropdown-list').forEach( item => {
			item.addEventListener('beforeshow', () => {
				document.querySelectorAll('.dropdown__header .list-item').forEach((element) => {
					element.parentNode.removeChild(element);
					document.querySelector('.dropdown__header button').style.display = 'flex';
					let eltResa = document.querySelector('.list-item--resa');
					eltResa.parentNode.removeChild(eltResa);
				})
			});
		});

		(function(window, $, self) {
			$(document).on('submit', '.form-resa', function(event) {
				event.preventDefault();
				/* Act on the event */
				self.resaFormHandler(this);
				console.log(self);
			});
		})(window, jQuery, self);

		});
	}

	displayFormHandler(event) {
		event.preventDefault();
		let target = event.target;

		(function(window, $) {
			let $liItem = $(target).closest('.list-item'),
				$divForm,
				$containerTag = 'li',
				$dropdown_list = $liItem.closest('.dropdown-list');

			if ( $dropdown_list.length > 0 )
				$containerTag = 'div';

			$divForm = $('<' + $containerTag + ' class="list-item list-item--resa" data-statut="'+$liItem.attr('data-statut')+'"><form class="form-resa">'
							+ '<p><span class="message-statut message-available-soon">L\'ouverture de la billetterie pour cet événement est imminente. Inscrivez-vous pour être alerté par email.</span>'
							+ '<span class="message-statut message-alert-me">Il n\'y a actuellement plus de place pour cet événement. Inscrivez-vous pour être alerté par email d\'une éventuelle remise en vente.</span></p>'
							+ '<div class="form-row">'
							+ '	 <div class="form-group col">'
							+ '    <input type="email" name="email" class="form-control" id="inputEmail" required="" placeholder="ENTREZ VOTRE ADRESSE EMAIL">'
							+ '  </div>'
							+ '  <div class="form-group col-w-auto">'
							+ '		<input type="hidden" name="meeting_date" value="'
							+ 		$liItem.find('time').attr('datetime')
							+ '		"/>'
							+ '		<input type="hidden" name="meeting_title" value="'
							+ 		encodeURI(document.title)
							+ '		"/>'
							+ '		<input type="hidden" name="services" value="ALERTING"/>'
							+ '		<input type="hidden" name="action" value="openfield_subscribe"/>'
							+ '		<button type="submit" class="btn"><i class="fas fa-angle-right stack-circle stack-outline-circle fz-24 plda-icon-context"></i></button>'
							+ '  </div>'
							+ '</div>'
							+ '<div class="form-row">'
							+ '	 <div class="form-group col">'
							+ '    <input type="checkbox" name="rgpd" class="form-control" required="" id="rgpd"><label for="rgpd">J&rsquo;accepte que Paris La D&eacute;fense Arena m&rsquo;adresse par email toutes les informations li&eacute;es &agrave; la mise en vente de cet &eacute;v&eacute;nement.</label>'
							+ '  </div>'
							+ '</div>'
							+ '</form></' + $containerTag + '>').slideToggle();

			if ( $dropdown_list.length > 0 ){
				UIkit.dropdown('.list-group--billets').hide();
				$liItem.clone().appendTo($dropdown_list.find('.dropdown__header'));
				$dropdown_list.find('.dropdown__header .list-item .btn').html($dropdown_list.find('.dropdown__header .btn>*').clone());
				$dropdown_list.find('.dropdown__header button').hide();
				$divForm.appendTo($dropdown_list)
						.slideToggle()
						.addClass('animation-slideDown');
			}
			else 
			{
				$liItem.before($divForm)
						.css('maxHeight', $liItem.outerHeight())
						.addClass('animation-slideDown')
						.addClass('animation-slideUp')
						.delay(100)
						  .queue(function (next) { 
						    $(this).css('maxHeight', '0'); 
						    next(); 
						  });
						//.css('maxHeight', '0');
				$divForm.slideToggle()
						.addClass('animation-slideDown');
			}

		})(window, jQuery);
	}


	resaFormHandler(form) {

		let $form     		= $(form),
			$listItem 		= $form.parent(),
			$dropdown_list 	= $listItem.closest('.dropdown-list');

		$form.css('opacity', '0.5').find('button').prop( "disabled", true );

		(function(window, $) {
			$.ajax({
				url : ajaxurl,
				type:'POST',
	            data: $form.serialize(),
				beforeSend: function( xhr ){
					
				},
				success:function(data){
					if( data ) {

						let response = data;
						$listItem.addClass('notification--sended');

						if (response.success) {
						    $listItem.html('<div class="list-item__image"><span class="plda fa-notification-checked"></span></div>'
						    	+ '<div class="list-item__content"><p>'
						    	+ 'Merci, votre email est enregistré pour l\'alerte d\'ouverture.<br>'
						    	+ 'Afin de gagner du temps lors de votre réservation, créez maintenant votre compte Paris La Défense Arena</p>'
						    	+ '<a class="btn btn-grad-green btn-icon btn-xs" href="https://billetterie.parisladefense-arena.com/fr/user/" target="_blank"><i class="plda fa-account"></i> Inscrivez vous</a>'
						    	+ '</div>');
							if ( $dropdown_list.length > 0 ){
							    $listItem.parent().find('.dropdown__header .list-item').hide();
							    $dropdown_list.find('.dropdown__header button').show();
							}
						} else {
						    $listItem.html('<p>Une erreur est survenue lors de votre enregistrement. Merci de rafraichir la page et renouveler l\'inscription.</p>');
						}
					}
				},
				error:function(){
					$listItem.html('<p>Une erreur est survenue lors de votre enregistrement. Merci de rafraichir la page et renouveler l\'inscription.</p>');
				},
			});
		})(window, jQuery);
	}

	postNewsletterForm(ev) {
		
		event.preventDefault();

		let $form     	   = $(this),
			$formRow = $form.find('.form-row');

		$form.find('input, button').hide();

		(function(window, $) {
			$.ajax({
				url : ajaxurl,
				type:'POST',
	            data: $form.serialize(),
				beforeSend: function( xhr ){
					
				},
				success:function(data){
					if( data ) {

						let response = data;
						$formRow.addClass('notification--sended');

						if (response.success) {
						    $formRow.html('<div class="message_success_ajax"><p>'
						    	+ 'Merci, votre email est enregistré. '
						    	+ 'Vous recevrez régulièrement notre newsletter.</p>'
						    	+ '</div>');
						} else {
						    $formRow.html('<p>Une erreur est survenue lors de votre enregistrement. Merci de rafraichir la page et renouveler l\'inscription.</p>');
						}
					}
				},
				error:function(){
					$formRow.html('<p>Une erreur est survenue lors de votre enregistrement. Merci de rafraichir la page et renouveler l\'inscription.</p>');
				},
			});
		})(window, jQuery);
	}

};

export default LoadFormResa;