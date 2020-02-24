import { gsap } from "gsap";

/**
 * Logo Animation
 */
export function LogoAnim(el) {
	let logoContainer = document.querySelector(el);
	let logoEcailles = el + ' .logo-formes path';
	document.addEventListener("DOMContentLoaded", function(event) {
	  //const logoEcailles = '.logo-formes path';
	  let tl = gsap.timeline({delay:2})
	  		.set(logoEcailles, {scaleX:-1,alpha:1,transformOrigin: "50% 50%"})
	  .from(logoEcailles, {duration:2, x:"-600px", stagger:{amount:0.4,from:'end'},ease:"elastic"})
	  .to(logoEcailles,{duration:1,scaleX:1,stagger:{amount:0.4,from:'end'}}, "-=2");

	  // Home slider Background svg
	  document.querySelectorAll('.float-animation').forEach(item => {
	  	let tl = gsap.timeline({repeat:-1,yoyo:true});
	  	tl.to(item, {duration:'random(3, 5)', y:'random(-30, 30, 1)',ease:"power1.inout"})
	  })

	});
}