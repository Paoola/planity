var defaultBounds = new google.maps.LatLngBounds(
  new google.maps.LatLng(-33.8902, 151.1759),
  new google.maps.LatLng(-33.8474, 151.2631)
);

var input = document.getElementById('saloon_location');
var options = {
  types: [],
  componentRestrictions: {country: 'fr'}
};

autocomplete = new google.maps.places.Autocomplete(input, options);

google.maps.event.addListener(autocomplete, 'place_changed', function() {
  var place = autocomplete.getPlace();
  document.getElementById("saloon_locationId").value = place.place_id;
  document.getElementById("saloon_latitude").value = place.geometry.location.lat();
  document.getElementById("saloon_longitude").value = place.geometry.location.lng();
});