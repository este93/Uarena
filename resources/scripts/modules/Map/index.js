import GoogleMapsApi from './GoogleMapsApi'
import { stylers }   from './stylers'
import markerTmpl from './marker.tmpl'

var mapContainer,
    deplacements,
    map,
    directionsDisplay,
    directionsService;

/**
 * Location Map
 * Main map rendering function that uses our GMaps API class
 * @param {string} el - Google Map selector
 */
export function LocationMap(el) {

  mapContainer = document.querySelector(el)
  const data         = {
    lat:     parseFloat(mapContainer.dataset.lat ? mapContainer.dataset.lat : 0),
    lng:     parseFloat(mapContainer.dataset.lng ? mapContainer.dataset.lng : 0),
    address: mapContainer.dataset.address,
    title:   mapContainer.dataset.title ? mapContainer.dataset.title: "Map",
    zoom:    parseFloat(mapContainer.dataset.zoom ? mapContainer.dataset.zoom: 16)
  }
  deplacements = JSON.parse(mapContainer.dataset.deplacements);
  const gApiKey   = mapContainer.dataset.gapikey
  const gmapApi   = new GoogleMapsApi(gApiKey)
  const mapEl     = document.querySelector(mapContainer.dataset.mapcanvas)

  // Call map renderer
  gmapApi.load().then(() => {
    renderMap(mapEl, data)
 })
}

/**
 * Render Map
 * @param {map obj} mapEl - Google Map
 * @param {obj} data - map data
 */
function renderMap(mapEl, data) {

  const options = {
    mapTypeId: google.maps.MapTypeId.ROADMAP,
    //styles:    stylers.styles,
    zoom:      data.zoom,
    center:    {
      lat: data.lat,
      lng: data.lng
    },
    scrollwheel: false,
    mapTypeControlOptions: {
      style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
      position: google.maps.ControlPosition.TOP_RIGHT
    }
  }

  map               = new google.maps.Map(mapEl, options);
  directionsDisplay = new google.maps.DirectionsRenderer({draggable: true});
  directionsService = new google.maps.DirectionsService();

  directionsDisplay.setMap(map);

  directionsDisplay.setPanel(document.getElementById("directions"));

  renderMarker(map, data);

  handleModeClick(map)
}

/**
 * Render Marker
 * Renders custom map marker and infowindow
 * @param {map element} mapEl
 * @param {object} data
 */
function renderMarker(map, data) {

  const icon = {
    url:        stylers.icons.red,
    scaledSize: new google.maps.Size(80, 80)
  }

  const tmpl = markerTmpl(data)

  const marker = new google.maps.Marker({
    position:  new google.maps.LatLng(data.lat, data.lng),
    map:       map,
    //icon:      icon,
    //title:     data.title,
    //content:   tmpl,
    animation: google.maps.Animation.DROP
  })

  //const infowindow = new google.maps.InfoWindow()

  //handleMarkerClick(map, marker, infowindow)
}

/**
 * Handle Mode Click
 *
 * @param {map obj} mapEl
 * @param {marker} marker
 * @param {infowindow} infoWindow
 */
function handleModeClick(map) {
  var _this = this;

  console.log(window.PldaApp.customSelect);

  //empty select
  let selectList   = document.querySelector('.routeTo'),
      styledSelect = selectList.parentNode.querySelector('ul.select-options'),
      goSearch     = document.querySelector('.js_go-search');
  

  const modeSelectors = mapContainer.querySelectorAll('input[name=transportMode]');
  modeSelectors.forEach((item) => {
    item.addEventListener('change', (event) => {
      let listAdressesTo = deplacements.modeTransport[event.target.dataset.transport];
      selectList.options.length=0;
      listAdressesTo.forEach((item,index) => {
        selectList.options[selectList.length] = new Option(deplacements.destinations[item].label, item);
      });
      window.PldaApp.customSelect.populate(styledSelect,selectList);
      selectList.nextElementSibling.classList.remove('is-placeholder');
      calculateRoute();
    });
  })
  //document.querySelector('#tab-01').dispatchEvent(new Event('change', { 'bubbles': true }));
  document.getElementById('tab-01').checked = true;
  document.getElementById('tab-01').dispatchEvent(new Event('change', { 'bubbles': true }));

  goSearch.addEventListener('click', (event) => {
    event.preventDefault();
    if ( document.querySelector('#routeFrom').value === '')
      return;
    calculateRoute();
  });

}
/**
 * Calculate Route fomr-to
 *
 */
function calculateRoute() {

  if (!document.querySelector('input[name="transportMode"]:checked') || document.querySelector('#routeFrom').value === '') {
      return;
  }

  var request = {
      origin: $("#routeFrom").val(),
      destination: deplacements.destinations[$("#routeTo").val()]['name'],//or destination: {lat: 37.768, lng: -122.511}
      travelMode: google.maps.TravelMode[$("input[name=transportMode]:checked").val()],
      provideRouteAlternatives: true
  };

  directionsService.route(request, function (response, status) {
      if (status == google.maps.DirectionsStatus.OK) {
          directionsDisplay.setDirections(response);
      }else{
        window.alert('Erreur de calcul d\'itinéraire : ' + status);
      }
  });
}
/**
 * Handle Marker Click
 *
 * @param {map obj} mapEl
 * @param {marker} marker
 * @param {infowindow} infoWindow
 */
function handleMarkerClick(map, marker, infowindow) {

  google.maps.event.addListener(marker, 'click', function() {
    infowindow.setContent(marker.content)
    infowindow.open(map, marker)
  })

  google.maps.event.addListener(map, 'click', function(event) {
    if (infowindow) {
      infowindow.close(map, infowindow)
    }
  })
}
