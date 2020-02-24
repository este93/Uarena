import UIkit from 'uikit';
import {gsap} from 'gsap';

export default class FlashInfo {
    
    constructor() {
           document.addEventListener("DOMContentLoaded",()=>{

              this.init();

           });
    }
    
    init() {
      var self = this;

      if (!!document.querySelector('.flashinfo-popup')) {
        let self = this,
            flashinfoPopup   = document.querySelector('.flashinfo-popup'),
            closeButton      = flashinfoPopup.querySelector('.uk-offcanvas-close'),
            cookieFlashInfo  = flashinfoPopup.dataset.cookiename,
            headerHeight     = 53,//document.querySelector('#masthead').offsetHeight,
            flashinfoHeight  = document.querySelector('.flashinfo-popup').offsetHeight;

        //flashinfoPopup.style.top = header.offsetHeight + 'px';

        gsap.set('.flashinfo-popup', {top:headerHeight+'px'});

        closeButton.addEventListener('click', (event) => {
                self.pldaAcceptCookies(cookieFlashInfo);
        });

        if(!self.pldaReadCookie(cookieFlashInfo)){ // If the cookie has not been set then show the bar
          document.querySelector("html").classList.add("has-flash-info");
          UIkit.offcanvas('.flashinfo-popup').show();
          gsap.from('.flashinfo-popup', {autoAlpha:0, top:"-"+flashinfoHeight+"px", ease:"power3.out", duration:0.4, delay:1});
        }

      }
        
    }

    //All the cookie setting stuff
    pldaSetCookie(cookieName, cookieValue, nDays) {
      var today = new Date();
      var expire = new Date();
      if (nDays==null || nDays==0) nDays=1;
      expire.setTime(today.getTime() + 3600000*24*nDays);
      document.cookie = cookieName+"="+escape(cookieValue)+ ";expires="+expire.toGMTString()+"; path=/";
    }

    pldaReadCookie(cookieName) {
      var theCookie=" "+document.cookie;
      var ind=theCookie.indexOf(" "+cookieName+"=");
      if (ind==-1) ind=theCookie.indexOf(";"+cookieName+"=");
      if (ind==-1 || cookieName=="") return "";
      var ind1=theCookie.indexOf(";",ind+1);
      if (ind1==-1) ind1=theCookie.length; 
      // Returns true if the versions match
      return ('PLDARENA_'+cookieName) == unescape(theCookie.substring(ind+cookieName.length+2,ind1));
    }
    
    catapultDeleteCookie(cookieName) {
      document.cookie = cookieName + '=;expires=Thu, 01 Jan 1970 00:00:01 GMT;path=/';
    }
    
    pldaAcceptCookies(cookieName) {
      this.pldaSetCookie(cookieName, 'PLDARENA_'+cookieName, 1);
      document.querySelector("html").classList.remove('has-flash-info');
    }
    
}
