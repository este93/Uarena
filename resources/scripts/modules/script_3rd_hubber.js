class HubberScript {

	constructor() {
		this.init();
	}

	init() {

		(function(window, $) {

            $(function() {
                window.Hubber3rdsites.configure({
                    refUrl: "https://billetterie.parisladefense-arena.com/sites/arena927.ap2s.fr",
                    initComplete: function (userInfo) {
                        // console.log(userInfo);
                        if (userInfo.signedIn) {
                            let url = 'https://billetterie.parisladefense-arena.com/fr/user/{id}/edit'.replace('{id}', userInfo.account.uid);
                            $('.user__profile').addClass('user__profile_connected').attr('href', url);
                            if (userInfo.cart.nbplaces > 0) {
                                $('.user__cart-count').text(userInfo.cart.nbplaces);
                            }
                        }
                    }
                });
            });
		})(window, jQuery);


	}
}

export default HubberScript;
