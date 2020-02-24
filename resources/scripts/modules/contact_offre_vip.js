import validate from 'validate.js';
import UIkit from 'uikit';

class LoadFormOffreVip {

	constructor() {
		this.init();
	}

	init() {
		let self = this;
		// Constraints to validate the form
		let constraints = {
		  email: {
		    presence: true, // Required
		    email: {
		    	message: "doit etre renseigné et valide",
		    }
		  },
		  nom: {
		    presence: true,
		  },
		  prenom: {
		    presence: true,
		  },
		  societe: {
		    presence: true,
		  },
		};

		validate.validators.presence.options = {message: "est obligatoire"};

		this.constraints = constraints;
		document.addEventListener("DOMContentLoaded", function() {

			document.querySelectorAll('#form_vip_offre_contact').forEach( item => {

				if ( self.viewportWidth() > 768 && !document.querySelector('.list-group--billets li') )
					UIkit.accordion('.widget-contact-form').toggle();
				
				
				//var form = document.querySelector("form#main");
				item.addEventListener("submit", function(ev) {
				  ev.preventDefault();
				  self.handleFormSubmit(item);
				});

				// Hook up the inputs to validate on the fly
				var inputs = item.querySelectorAll("input:not([type=hidden]), textarea, select")
				for (var i = 0; i < inputs.length; ++i) {
				  inputs.item(i).addEventListener("change", function(ev) {
				    var errors = validate(item, constraints) || {};
				    self.showErrorsForInput(this, errors[this.name])
				  });
				}
			});
		});

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

	handleFormSubmit(form) {
		let self = this;
		let constraints = this.constraints;
		// validate the form against the constraints
		var errors = validate(form, constraints);
		// then we update the form to reflect the results
		self.showErrors(form, errors || {});
		if (!errors) {
		  self.submitAjaxForm(form);
		}
	}

	// Updates the inputs with the validation errors
	showErrors(form, errors) {
		let self = this;
		// erase old alerts
		form.querySelectorAll(".messages .help-block.error").forEach( el => {
			el.parentNode.removeChild(el);
		});
		// We loop through all the inputs and show the errors for that input
		form.querySelectorAll("input[name], select[name]").forEach( input => {
		  // Since the errors can be null if no errors were found we need to handle
		  // that
		  self.showErrorsForInput(input, errors && errors[input.name]);
		});
	}

	// Shows the errors for a specific input
	showErrorsForInput(input, errors) {
		let self = this;
		// This is the root of the input
		var formGroup = self.closestParent(input.parentNode, "form-group")
		  // Get the form
		  , form = self.closestParent(input.parentNode, "plda-form")
		  // Find where the error messages will be insert into
		  , messages = form.querySelector(".messages");
		// First we remove any old messages and resets the classes
		self.resetFormGroup(formGroup);
		// If we have errors
		if (errors) {
		  // we first mark the group has having errors
		  formGroup.classList.add("has-error");
		  // then we append all the errors
		  errors.forEach(error => {
		  	self.addError(messages, error);
		  });
		} else {
		  // otherwise we simply mark it as success
		  formGroup.classList.add("has-success");
		}
	}

	resetFormGroup(formGroup) {
		let self = this;
		// Remove the success and error classes
		formGroup.classList.remove("has-error");
		formGroup.classList.remove("has-success");
		// and remove any old messages
		formGroup.querySelectorAll(".help-block.error").forEach( el => {
			el.parentNode.removeChild(el);
		});
	}

	// Adds the specified error with the following markup
	// <p class="help-block error">[message]</p>
	addError(messages, error) {
		var block = document.createElement("span");
		block.classList.add("help-block");
		block.classList.add("error");
		block.innerText = error + ' ';
		messages.appendChild(block);
	}


	// Recusively finds the closest parent that has the specified class
	closestParent(child, className) {
	  let self = this;
	  if (!child || child == document) {
	    return null;
	  }
	  if (child.classList.contains(className)) {
	    return child;
	  } else {
	    return self.closestParent(child.parentNode, className);
	  }
	}


	submitAjaxForm(form) {

		let $form     = $(form),
			$listItem = $form.parent();

		$form.css('opacity', '0.5').find('.btn').prop( "disabled", true );


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
						
						if (response.success) {
						    $form.parent().html(response.data);
						} else {
						    $form.html('<p>Une erreur est survenue lors de votre enregistrement. Merci de rafraichir la page et renouveler l\'inscription.</p>');
						}
					}
				},
				error:function(){
					$form.html('<p>Une erreur est survenue lors de votre enregistrement. Merci de rafraichir la page et renouveler l\'inscription.</p>');
				},
			});
		})(window, jQuery);
	}

};

export default LoadFormOffreVip;