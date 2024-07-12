
var map = L.map('map').setView([38.290399042463136, 21.79564239581478], 14);
L.tileLayer('https://api.maptiler.com/maps/basic/256/{z}/{x}/{y}.png?key=dVhthbXQs3EHCi0XzzkL', {
attribution:
  '<a href="https://www.maptiler.com/copyright/" target="_blank">&copy; MapTiler</a> <a href="https://www.openstreetmap.org/copyright" target="_blank">&copy; OpenStreetMap contributors</a>'
}).addTo(map);

var userLocationMarker;
var baseMarker = L.marker([38.290399042463136, 21.79564239581478], { draggable: true });
var popup1 = baseMarker.bindPopup('Address: 25th March, Patras Greece<br>Postcode: 265 04<br>Phone: +30 2610 529 090<br>Email: carelink@gmail.com').openPopup();

var line = L.polyline([], { color: 'black' }).addTo(map); // Initialize an empty polyline

if ('geolocation' in navigator) {
navigator.geolocation.getCurrentPosition(function (position) {
  var userLat = position.coords.latitude;
  var userLng = position.coords.longitude;

  map.setView([userLat, userLng], 13);

  initializeBaseMarker(userLat, userLng);
  initializeUserLocationMarker(userLat, userLng);

  updateLine();

  // Calculate distance between user's location and baseMarker's initial position
  var initialDistance = calculateDistance(userLocationMarker.getLatLng(), baseMarker.getLatLng());
  userLocationMarker.bindPopup(`Your Location - Distance: ${initialDistance.toFixed(2)} kilometers`).openPopup();


});
} else {
console.log('Geolocation is not supported by your browser.');
}

function initializeBaseMarker(userLat, userLng) {
var redIcon = L.icon({
  iconUrl: 'images/pin.png',
  iconSize: [41, 41],
  iconAnchor: [20, 41],
  popupAnchor: [1, -34]
});

baseMarker = L.marker([38.290399042463136, 21.79564239581478], { draggable: true }).addTo(map).setIcon(redIcon);
baseMarker.bindPopup('Address: 25th March, Patras Greece<br>Postcode: 265 04<br>Phone: +30 2610 529 090<br>Email: carelink@gmail.com').openPopup();

circle = L.circle([38.290399042463136, 21.79564239581478], {
  color: 'blue',
  fillColor: 'blue',
  fillOpacity: 0.5,
  radius: 100
}).addTo(map);

baseMarker.on('dragend', function (event) {
  var position = baseMarker.getLatLng();
  baseMarker.setLatLng(position);
  baseMarker.getPopup().setContent('BASE, new Position: ' + position.toString()).update();
  $("#Latitude").val(position.lat);
  $("#Longitude").val(position.lng).keyup();
  circle.setLatLng(position);

  // Calculate distance between baseMarker and userLocationMarker
  var distance = calculateDistance(userLocationMarker.getLatLng(), position);
  userLocationMarker.setPopupContent(`Your Location - Distance: ${distance.toFixed(2)} kilometers`).update();

  // Update the line
  updateLine();
});
}

function initializeUserLocationMarker(userLat, userLng) {
userLocationMarker = L.marker([userLat, userLng], { draggable: true }).addTo(map);

userLocationMarker.bindPopup('Your Location').openPopup();

userLocationMarker.on('dragend', function (event) {
  var position = userLocationMarker.getLatLng();
  userLocationMarker.setLatLng(position);
  userLocationMarker.getPopup().setContent('Your Location, new Position: ' + position.toString()).update();

  // Calculate distance between userLocationMarker and baseMarker
  var distance = calculateDistance(position, baseMarker.getLatLng());
  userLocationMarker.setPopupContent(`Your Location - Distance: ${distance.toFixed(2)} kilometers`).update();

  // Update the line
  updateLine();
});
}

function updateLine() {
  var distance = calculateDistance(baseMarker.getLatLng(), userLocationMarker.getLatLng());

  // Assuming the button has the id "yourButtonId"
  var loadItemsButton = document.getElementById('yourButtonId1');
  var unloadItemsButton = document.getElementById('yourButtonId2');

  if (distance < 0.1) {
    loadItemsButton.disabled = false; // Enable the button
    unloadItemsButton.disabled = false;
  } else {
    loadItemsButton.disabled = true; // Disable the button
    unloadItemsButton.disabled = true;
  }

  line.setLatLngs([baseMarker.getLatLng(), userLocationMarker.getLatLng()]);
}

function calculateDistance(pointA, pointB) {
  var earthRadius = 6371; // Earth's radius in kilometers
  var latDiff = (pointB.lat - pointA.lat) * (Math.PI / 180);
  var lngDiff = (pointB.lng - pointA.lng) * (Math.PI / 180);

  var a = Math.sin(latDiff / 2) * Math.sin(latDiff / 2) +
    Math.cos(pointA.lat * (Math.PI / 180)) * Math.cos(pointB.lat * (Math.PI / 180)) *
    Math.sin(lngDiff / 2) * Math.sin(lngDiff / 2);

  var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

  var distance = earthRadius * c; // Distance in kilometers
  return distance;
}

// Array to store marker clusters
var cityClusters = [];

function toggleLayer(layerId) {
  // Clear existing marker clusters
  clearClusters();

  // Create a new marker cluster for the selected layer
  var city = L.markerClusterGroup();

  if (layerId === 'layer1') {
    // Assuming 'data' contains the output of your SQL query
    createMarkersForRequests(city, data, 'WAITING');
  } 
  // Add the marker cluster to the map
  map.addLayer(city);
  cityClusters.push(city);
}



function clearClusters() {
  // Remove all existing marker clusters from the map
  cityClusters.forEach(function(cluster) {
    map.removeLayer(cluster);
  });
  cityClusters = []; // Clear the array
}

function createMarkersForRequests(city, data, state) {
  console.log("Data being processed:", data); // Log the entire data array
  for (var i = 0; i < data.length; i++) {
    if (data[i].state === state) { // Ensure correct access to the state attribute
      var new_location = new L.LatLng(data[i].latitude, data[i].longitude);
      var request_id = data[i].id_request;
      var username = data[i].request_civilian;
      var category = data[i].request_category;
      var product = data[i].request_product_name;
      var dateposted = data[i].request_date_posted;
      var timeposted = data[i].request_time_posted;
      var number = data[i].number;
      
      var marker = new L.Marker(new_location);

      var buttonHtml = '<button class="Acceptbut" onclick="handleMarkerButtonClick(' + request_id + ')">Accept</button>';

      var message = username + ' requested:<br>' + 'From ' + category + ' :' + product +
        '<br>Date-time: ' + dateposted + '/' + timeposted + '<br>Number: +30 ' + number + '<br>State: ' + state +
        '<br>' + buttonHtml;

      marker.bindPopup(message);
      city.addLayer(marker);

      // Set marker icon based on state
      marker.setIcon(L.divIcon({
        className: 'custom-div-icon',
        html: "<div class='WAITING-pin'></div>",
        iconSize: [30, 30],
        iconAnchor: [15, 15],
        popupAnchor: [0, 0],
      }));
    }
  }
}


function loadItems(formId) {
  var form = document.getElementById(formId);
  // Add your logic for showing the form and handling Load Items here
  form.style.display = 'block';
  }
  
  function unloadItems(formId) {
  var form = document.getElementById(formId);
  // Add your logic for showing the form and handling Unload Items here
  form.style.display = 'block';
  }
 
 

