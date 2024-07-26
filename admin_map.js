var map = L.map('map').setView([38.2904558214517, 21.79578903224108], 14);
L.tileLayer('https://api.maptiler.com/maps/basic/256/{z}/{x}/{y}.png?key=dVhthbXQs3EHCi0XzzkL', {
  attribution:
    '<a href="https://www.maptiler.com/copyright/" target="_blank">&copy; MapTiler</a> <a href="https://www.openstreetmap.org/copyright" target="_blank">&copy; OpenStreetMap contributors</a>'
}).addTo(map);

var baseMarker = L.marker([38.2904558214517, 21.79578903224108], { draggable: true });
var popup1 = baseMarker.bindPopup('Address: 25th March, Patras Greece<br>Postcode: 265 04<br>Phone: +30 2610 529 090<br>Email: carelink@gmail.com').openPopup();

if ('geolocation' in navigator) {
  navigator.geolocation.getCurrentPosition(function (position) {
    var userLat = position.coords.latitude;
    var userLng = position.coords.longitude;
    var geolocation = userLat + ", " + userLng;

    map.setView([userLat, userLng], 13);

    initializeBaseMarker(userLat, userLng);
    initializeUserLocationMarker(userLat, userLng);

    updateLine();

    // Calculate distance between user's location and baseMarker's initial position
    var initialDistance = calculateDistance(userLocationMarker.getLatLng(), baseMarker.getLatLng());
    userLocationMarker.bindPopup(`Your Location - Distance: ${initialDistance.toFixed(2)} kilometers`).openPopup();
    // Update the value of the address input field
    document.getElementById("address1").value = geolocation;
    document.getElementById("address1").value = geolocation;


  });
} else {
  console.log('Geolocation is not supported by your browser.');
}
function initializeBaseMarker(userLat, userLng) {
  var redIcon = L.icon({
    iconUrl: 'images/base.png',
    iconSize: [41, 41],
    iconAnchor: [20, 41],
    popupAnchor: [1, -34]
  });

  baseMarker = L.marker([38.290399042463136, 21.79564239581478], { draggable: true }).addTo(map).setIcon(redIcon);
  baseMarker.bindPopup('Address: 25th March, Patras Greece<br>Postcode: 265 04<br>Phone: +30 2610 529 090<br>Email: carelink@gmail.com').openPopup();

  baseMarker.on('dragend', function (event) {
    var position = baseMarker.getLatLng();
    baseMarker.setLatLng(position);
    baseMarker.getPopup().setContent('BASE, new Position: ' + position.toString()).update();
    $("#Latitude").val(position.lat);
    $("#Longitude").val(position.lng).keyup();
  });
}

// Define global variables for layers
var vehiclesWaiting;
var vehiclesOnAction;
var offers_layer;
var onTheWayRequestslayer;
var WaitingRequestslayer;
var Lines;
var vehicles = [];
var Offers = [];
var WaitingRequests = [];
var OnWayRequests = [];
var data6 = [];


// Fetch the JSON data from the file
fetch('vehicles.json')
.then(response => response.json())
.then(data => {
  vehicles = data;
})
.catch(error => console.error('Error fetching the JSON data:', error));

fetch('Offers.json')
.then(response => response.json())
.then(data => {
  Offers = data;
})
.catch(error => console.error('Error fetching the JSON data:', error));

fetch('waitingRequests.json')
.then(response => response.json())
.then(data => {
  waitingRequests = data;
})
.catch(error => console.error('Error fetching the JSON data:', error));

fetch('OnWayRequests.json')
.then(response => response.json())
.then(data => {
  OnWayRequests = data;
})
.catch(error => console.error('Error fetching the JSON data:', error));

fetch('data6.json')
.then(response => response.json())
.then(data => {
    data4 = data;
})
.catch(error => console.error('Error fetching the JSON data:', error));

// Store active layers
const activeLayers = {};

// Create a global marker cluster group
const allMarkersClusterGroup = L.markerClusterGroup();
map.addLayer(allMarkersClusterGroup);

// Store all markers by layer
const layerMarkers = {
  layer1: [],
  layer2: [],
  layer3: [],
  layer4: [],
  layer5: [],
  layer6: []
};

// Function to initialize markers for waiting vehicles
function Waiting_vehicles_markers(data) {
  // Clear existing markers for the layer
  layerMarkers.layer1 = [];

  // Loop through the data and create markers
  for (let i = 0; i < data.length; i++) {
    const location = new L.LatLng(data[i].latitude, data[i].longitude);
        var vehicle_name = data[i].vehicle_name;
        var quantity = data[i].quantity;
        var products = data[i].products;
        var task_count = data[i].task_count;
        var state= 'Waiting';
        
    const message = '<strong>Vehicle:</strong> ' + vehicle_name + '<br>' +
                    '<strong>State:</strong> ' + state + '<br>' +
                    '<strong>Products:</strong> ' +products +'<br>'+'<strong>Quantity:</strong> ' +
                     + quantity +'<br>'+ '<strong>No of Active Tasks: </strong> ' + task_count;
    // Create a new marker with a custom icon
    const marker = L.marker(location, {
      icon: L.icon({
        iconUrl: 'truck.png',
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -32]
      })
    });

    // Bind popup content to the marker
    marker.bindPopup(message);
    if(task_count==0){
    // Add marker to the layerMarkers array
    layerMarkers.layer1.push(marker);
  }
}
  // Update the marker cluster group
  updateClusterGroup();
}

// Function to initialize markers for on the way vehicles
function on_the_way_vehicles_markers(data) {
    // Clear existing markers for the layer
  layerMarkers.layer2 = [];
  
  // Loop through the data and create markers
  for (let i = 0; i < data.length; i++) {
    const location = new L.LatLng(data[i].latitude, data[i].longitude);
        var vehicle_name = data[i].vehicle_name;
        var quantity = data[i].quantity;
        var products = data[i].products;
        var task_count = data[i].task_count;
        var state= 'Active';

    const message = '<strong>Vehicle:</strong> ' + vehicle_name + '<br>' +
                    '<strong>State:</strong> ' + state + '<br>' +
                    '<strong>Products:</strong> ' +products +'<br>'+'<strong>Quantity:</strong> ' +
                    + quantity +'<br>'+ '<strong>No of Active Tasks: </strong> ' + task_count;

    // Create a new marker with a custom icon
    const marker = L.marker(location, {
      icon: L.icon({
        iconUrl: 'truck.png',
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -32]
      })
    });

    // Bind popup content to the marker
    marker.bindPopup(message);

    if(task_count>0){
    // Add marker to the layerMarkers array
    layerMarkers.layer2.push(marker);
    }
  }
  // Update the marker cluster group
  updateClusterGroup();
}


// Function to initialize markers for waiting requests
function Waiting_requests_markers(data) {
  // Clear existing markers for the layer
  layerMarkers.layer4 = [];

  // Loop through the data and create markers
  for (let i = 0; i < data.length; i++) {
    const location = new L.LatLng(data[i].latitude, data[i].longitude);

          var civilian_first_name = data[i].civilian_first_name;
          var civilian_last_name = data[i].civilian_last_name;
          var civilian_number = data[i].civilian_number;
          var request_date_posted = data[i].request_date_posted;
          var request_category = data[i].request_category;
          var state = data[i].state;
          var request_product_name = data[i].request_product_name;
          var persons = data[i].persons;

    const message = '<strong>' + civilian_first_name + ' ' + civilian_last_name + 
                ' requests: </strong><br>' + '<strong> ' + 
                request_category + '</strong>: ' + request_product_name + '<br><strong>For</strong>: ' +
                persons + ' persons' + '<br><strong>Date posted</strong>: ' + 
                request_date_posted + '<br><strong>Number: </strong> ' + '+30'+ civilian_number + 
                '<br><strong>State:</strong> ' + state ;
                 
    // Create a new marker with a custom icon
    const marker = L.marker(location, {
      icon: L.icon({
        iconUrl: 'pin1.png',
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -32]
      })
    });

    // Bind popup content to the marker
    marker.bindPopup(message);

    // Add marker to the layerMarkers array
    layerMarkers.layer4.push(marker);
  }
  // Update the marker cluster group
  updateClusterGroup();
}

// Function to initialize markers for on the way requests
function on_the_way_requests_markers(data) {
  // Clear existing markers for the layer
  layerMarkers.layer5 = [];

  // Loop through the data and create markers
  for (let i = 0; i < data.length; i++) {
    const location = new L.LatLng(data[i].latitude, data[i].longitude);
    var civilian_first_name = data[i].civilian_first_name;
          var civilian_last_name = data[i].civilian_last_name;
          var civilian_number = data[i].civilian_number;
          var request_date_posted = data[i].request_date_posted;
          var request_category = data[i].request_category;
          var state = data[i].state;
          var request_product_name = data[i].request_product_name;
          var persons = data[i].persons;
          var task_date = data[i].task_date;
          var vehicle_name = data[i].vehicle_name;

    const message = '<strong>' + civilian_first_name + ' ' + civilian_last_name + 
                ' requests: </strong><br>' + '<strong> ' + 
                request_category + '</strong>: ' + request_product_name + '<br><strong>For</strong>: ' +
                persons + ' persons' + '<br><strong>Date posted</strong>: ' + 
                request_date_posted + '<br><strong>Number: </strong> ' + '+30'+ civilian_number +
                '<br><strong>Vehicle:</strong> ' + vehicle_name +
                '<br><strong>Date Accepted </strong> ' + task_date + 
                '<br><strong>State:</strong> ' + state ;
                 
    // Create a new marker with a custom icon
    const marker = L.marker(location, {
      icon: L.icon({
        iconUrl: 'pin1.png',
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -32]
      })
    });

    // Bind popup content to the marker
    marker.bindPopup(message);

    // Add marker to the layerMarkers array
    layerMarkers.layer5.push(marker);
  }
  // Update the marker cluster group
  updateClusterGroup();
}

// Function to initialize markers for offers
function offers_markers(data) {
  layerMarkers.layer3 = [];

  // Loop through the data and create markers
  for (let i = 0; i < data.length; i++) {
    const location = new L.LatLng(data[i].latitude, data[i].longitude);
      var civilian_first_name = data[i].civilian_first_name ;
      var civilian_last_name = data[i].civilian_last_name ;
      var civilian_number = data[i].civilian_number ;
      var offer_date_posted = data[i].offer_date_posted ;
      var offer_category = data[i].offer_category ;
      var offer_status = data[i].offer_status;
      var offer_product_name = data[i].offer_product_name;
      var offer_quantity = data[i].offer_quantity ;
      var task_date = data[i].task_date ;
      var vehicle_name = data[i].vehicle_name;

      /* In JavaScript, the let keyword is used to declare variables that are block-scoped, meaning the variable is only accessible within the block 
      (e.g., within a function, loop, or conditional statement) where it is defined. This is in contrast to the var keyword, 
      which declares variables that are function-scoped or globally-scoped if declared outside a function. */

      let message;
      let marker;

    if (vehicle_name == null) {
       message = '<strong>' + civilian_first_name + ' ' + civilian_last_name + ' Offers: </strong><br>' + '<strong>From ' + 
                  offer_category + '</strong>: ' + offer_product_name + '<br><strong>For</strong>: ' +
                  offer_quantity + ' persons' + '<br><strong>Date posted</strong>: ' + 
                  offer_date_posted + '<br><strong>Number:+30 </strong> ' + civilian_number + '<br><strong>State:</strong> ' + offer_status ;
                  // Create a new marker with a custom icon
      marker = L.marker(location, {
        icon: L.icon({
          iconUrl: 'pin1.png',
          iconSize: [32, 32],
          iconAnchor: [16, 32],
          popupAnchor: [0, -32]
        })
      });
    } else {
      message = '<strong>' + civilian_first_name + ' ' + civilian_last_name + ' Offers: </strong><br>' + '<strong>From ' + 
                  offer_category + '</strong>: ' + offer_product_name + '<br><strong>For</strong>: ' +
                  offer_quantity + ' persons' + '<br><strong>Date posted</strong>: ' + 
                  offer_date_posted + '<br><strong>Number:+30 </strong> ' + civilian_number +
                  '<br><strong>Vehicle:</strong> ' + vehicle_name +
                  '<br><strong>Date Accepted </strong> ' + task_date + 
                  '<br><strong>State:</strong> ' + offer_status ;

                  // Create a new marker with a custom icon
      marker = L.marker(location, {
        icon: L.icon({
          iconUrl: 'pin2.png',
          iconSize: [32, 32],
          iconAnchor: [16, 32],
          popupAnchor: [0, -32]
        })
      });
    }

    // Bind popup content to the marker
    marker.bindPopup(message);

    // Add marker to the layerMarkers array
    layerMarkers.layer3.push(marker);
  }

  // Update the marker cluster group
  updateClusterGroup();
}


let layerLines = [];

// Function to draw lines between layers
function drawLinesforOffersandRequest() {
  // Clear existing lines
  layerLines.forEach(line => map.removeLayer(line));
  layerLines = [];

  if (activeLayers.layer2 && activeLayers.layer3 && activeLayers.layer5) {
    layerMarkers.layer2.forEach(vehicleMarker => {
      const vehicleName = vehicleMarker.getPopup().getContent().match(/<strong>Vehicle:<\/strong> (.+?)<br>/)[1];
      const vehicleLatLng = vehicleMarker.getLatLng();
      layerMarkers.layer3.forEach(offerMarker => {
        const offerVehicleName = offerMarker.getPopup().getContent().match(/<strong>Vehicle:<\/strong> (.+?)<br>/)[1];
        if (vehicleName === offerVehicleName) {
          const offerLatLng = offerMarker.getLatLng();
          layerMarkers.layer5.forEach(requestMarker => {
            const requestVehicleName = requestMarker.getPopup().getContent().match(/<strong>Vehicle:<\/strong> (.+?)<br>/)[1];
            if (vehicleName === requestVehicleName) {
              const requestLatLng = requestMarker.getLatLng();
              const polyline = L.polyline([vehicleLatLng, offerLatLng], {color: 'green'}).addTo(map);
              const polyline1 = L.polyline([vehicleLatLng, requestLatLng], {color: 'red'}).addTo(map);
              layerLines.push(polyline);
              layerLines.push(polyline1);
            }
          });
        }
      });
    });
  }
}

// Function to draw lines between layers
function drawLinesforOffersorRequest() {
  // Clear existing lines
  layerLines.forEach(line => map.removeLayer(line));
  layerLines = [];

  if (activeLayers.layer2 && activeLayers.layer3) {
    layerMarkers.layer2.forEach(vehicleMarker => {
      const vehicleName = vehicleMarker.getPopup().getContent().match(/<strong>Vehicle:<\/strong> (.+?)<br>/)[1];
      const vehicleLatLng = vehicleMarker.getLatLng();
      layerMarkers.layer3.forEach(offerMarker => {
        const offerVehicleName = offerMarker.getPopup().getContent().match(/<strong>Vehicle:<\/strong> (.+?)<br>/)[1];
        if (vehicleName === offerVehicleName) {
          const offerLatLng = offerMarker.getLatLng();
          const polyline = L.polyline([vehicleLatLng, offerLatLng], {color: 'green'}).addTo(map);
          layerLines.push(polyline);
        }
      });
    });
  }

  if (activeLayers.layer2 && activeLayers.layer5) {
    layerMarkers.layer2.forEach(vehicleMarker => {
      const vehicleName = vehicleMarker.getPopup().getContent().match(/<strong>Vehicle:<\/strong> (.+?)<br>/)[1];
      const vehicleLatLng = vehicleMarker.getLatLng();
      layerMarkers.layer5.forEach(requestMarker => {
        const requestVehicleName = requestMarker.getPopup().getContent().match(/<strong>Vehicle:<\/strong> (.+?)<br>/)[1];
        if (vehicleName === requestVehicleName) {
          const requestLatLng = requestMarker.getLatLng();
          const polyline = L.polyline([vehicleLatLng, requestLatLng], {color: 'red'}).addTo(map);
          layerLines.push(polyline);
        }
      });
    });
  } 

  
}

// Function to clear lines between layers
function clearLinesBetweenLayers() {
  layerLines.forEach(line => map.removeLayer(line));
  layerLines = [];
}

// Function to toggle layers
function toggleLayer(layer) {
  if (activeLayers[layer]) {
    // Remove the layer if it is active
    activeLayers[layer] = false;
    if (layer === 'layer6') {
      clearLinesBetweenLayers();
    }
  } else {
    // Add the layer if it is not active
    activeLayers[layer] = true;

    // Initialize markers for the layer if not already done
    if (layer === 'layer1' && layerMarkers.layer1.length === 0) {
      Waiting_vehicles_markers(vehicles);
    } else if (layer === 'layer2' && layerMarkers.layer2.length === 0) {
      on_the_way_vehicles_markers(vehicles);
    } else if (layer === 'layer3' && layerMarkers.layer3.length === 0) {
      offers_markers(Offers);
    } else if (layer === 'layer4' && layerMarkers.layer4.length === 0) {
      Waiting_requests_markers(waitingRequests);
    } else if (layer === 'layer5' && layerMarkers.layer5.length === 0) {
      on_the_way_requests_markers(OnWayRequests);
    }

    // Draw lines if layer 6 is being enabled
    if (layer === 'layer6') {
      drawLinesforOffersandRequest();
      drawLinesforOffersorRequest();
    }
  }

  // Update the marker cluster group
  updateClusterGroup();

  // Enable/disable the layer6 toggle button based on the state of layer2 and layer3
  document.getElementById('layer6').disabled = !(activeLayers['layer2']);
}

// Function to update the marker cluster group based on active layers
function updateClusterGroup() {
  // Clear all markers from the cluster group
  allMarkersClusterGroup.clearLayers();

  // Add markers from active layers
  for (const layer in activeLayers) {
    if (activeLayers[layer]) {
      allMarkersClusterGroup.addLayers(layerMarkers[layer]);
    }
  }
}
