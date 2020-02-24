export default class AjaxNavigation {

	constructor() {
		this.init();
	}

	init() {

		let self = this,
			ajaxBtn = document.querySelectorAll('.sub-nav a'),
			PostsContainer = document.getElementsByClassName('entry-content'),
			baseURL = window.location.protocol + "//" + window.location.host + "/wp-json/wp/v2/";

		 
		ajaxBtn.forEach((item) => {
			item.addEventListener("click", (event) => {
				event.preventDefault();
			    console.log('CLicked');
			    var ourRequest = new XMLHttpRequest();
			    ourRequest.open('GET', baseURL + event.target.dataset.posttype + 's/' + event.target.dataset.postid);
			    ourRequest.onload = function() {
			        if(ourRequest.status >= 200 && ourRequest.status < 400) {
			            var data = JSON.parse(ourRequest.responseText);
			            console.log(data);
			            self.displayContent(data);
			        } else {
			            console.log('We connected to the server, but it returned an error.');
			        }
			    };
			
			    ourRequest.onerror = function() {
			        console.log('Connection error');
			    }
			
			    ourRequest.send();
			});
		})

	}

	displayContent( postData ) {
		let postHTMLString = '',
			PostsContainer = document.querySelector('.entry-content');

		console.log(postData.title.rendered);
		
	    postHTMLString += '<h2>' + postData.title.rendered + '</h2>';
	    postHTMLString += postData.content.rendered;
		
		
		PostsContainer.innerHTML = postHTMLString;
	}
}
