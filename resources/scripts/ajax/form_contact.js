
import UIkit from 'uikit';

class ContactForm {

	constructor() {
	    
		// callback retour serveur ajax de Caldera Form (précisé dans le BO/edition du formulaire)
		window.onAjaxContactFormCallback = this.onAjaxFormCallback;

	}

	onAjaxFormCallback(obj) {
		let self = this,
			formContainer = document.querySelector('[data-cf-form-id="'+obj.form_id+'"]').parentNode;
			if (!!formContainer) {
				let contentPage = formContainer.closest('.entry-content').parentNode;
				contentPage.innerHTML = '<img class="bg-image uk-preserve" src="/themes/pld_arena/assets/images/svg/inline-bkgn-slider-parallax.svg" uk-svg><div class="callback-message">' + obj.html + '</div>';
				contentPage.classList.add('full-window');
				contentPage.classList.add('form-callback');
				UIkit.scroll('.adresse-arena').scrollTo('#page');
			}
	}

};

export default ContactForm;