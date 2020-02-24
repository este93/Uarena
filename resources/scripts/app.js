/**
 * Manage global libraries like jQuery or THREE from the package.json file
 */

if (!window.PldaApp) {
 window.PldaApp = {};
}

import UIkit from 'uikit';

// Import custom modules
import AppIcons 		from './modules/icons.js';
import App 				from './modules/app.js';
//import AjaxNavigation	from './modules/ajaxNavigation.js';
import InitMap		 	from './modules/map.js';
import CustomSelect 	from './modules/select.js';
import LoadFormResa 	from './modules/form_openfield.js';
import LoadFormOffreVip from './modules/contact_offre_vip.js';
import ContactForm 		from './ajax/form_contact.js';
import {LogoAnim} 		from './modules/logo.js';
import {Polymorphe} 		from './modules/polymorphe.js';
import {InstagramFeed} 	from './modules/instagramFeed.js';
import HubberScript 	from './modules/script_3rd_hubber.js';
import SearchForm	 	from './modules/searchform.js';
import FlashInfo	 	from './modules/flashinfo.js';

const appIcons 			= new AppIcons();
const app 				= new App();
//const ajaxNavigation 	= new AjaxNavigation();
const customSelect 		= new CustomSelect();
const loadFormResa 		= new LoadFormResa();
const loadFormOffreVip  = new LoadFormOffreVip();
const contactForm 		= new ContactForm();
const logoAnim 			= new LogoAnim('.header__logo');
const polymorphe 		= new Polymorphe;
const instagramFeed		= new InstagramFeed('#sb_instagram');
const hubberScript 		= new HubberScript();
const searchForm 		= new SearchForm();
const flashInfo 		= new FlashInfo();

window.PldaApp.customSelect = customSelect;

InitMap();