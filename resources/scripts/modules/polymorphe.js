export function Polymorphe(el) {
	let button = $('.b-polymorphe--buttons--item');
	let div = $('.b-polymorphe--tab');
	let id = '';
	button.on('click', function(){
		id = $(this).attr('data-id'); 
		div.hide('fast');
		button.removeClass('active');
		$(this).addClass('active');
		$('#' + id).show('fast');
		console.log(id);
	})

	$('.b-introduction--scroll').on('click', function(){
		document.querySelector('.b-bienvenue').scrollIntoView({
            behavior: 'smooth'
        });
	})
}