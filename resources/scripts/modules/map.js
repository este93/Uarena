import * as Map from './Map/'


/**
 * initMap
 * Main map module initialization
 * @param {string} el - Google Map Canvas selector
 */
export default function InitMap() {

	document.addEventListener('DOMContentLoaded', () => {
    	if (document.querySelector('.block--map')) {
			Map.LocationMap('.block--map');
		}
	})
}