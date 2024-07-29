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

  var map = L.map('map').setView([38.2904558214517, 21.79578903224108], 14);
  L.tileLayer('https://api.maptiler.com/maps/basic/256/{z}/{x}/{y}.png?key=dVhthbXQs3EHCi0XzzkL', {
    attribution: '<a href="https://www.maptiler.com/copyright/" target="_blank">&copy; MapTiler</a> <a href="https://www.openstreetmap.org/copyright" target="_blank">&copy; OpenStreetMap contributors</a>'
  }).addTo(map);
  
  var userLocationMarker;
  var baseMarker = L.marker([38.2904558214517, 21.79578903224108]);
  var popup1 = baseMarker.bindPopup('Address: 25th March, Patras Greece<br>Postcode: 265 04<br>Phone: +30 2610 529 090<br>Email: carelink@gmail.com').openPopup();
  
  var line = L.polyline([], { color: 'black' }).addTo(map); // Initialize an empty polyline

  var RequestMarkers = [];
  var RequestLines = [];

  var OfferMarkers = [];
  var OfferLines = [];
  
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
      document.getElementById("loadAddress").value = geolocation;
  
    });
  } else {
    console.log('Geolocation is not supported by your browser.');
  }
  
  function initializeBaseMarker() {
    var baseIcon = L.icon({
      iconUrl: 'images/base.png',
      iconSize: [41, 41],
      iconAnchor: [20, 41],
      popupAnchor: [1, -34]
    });
  
    baseMarker = L.marker([38.290399042463136, 21.79564239581478]).addTo(map).setIcon(baseIcon);
    baseMarker.bindPopup('Address: 25th March, Patras Greece<br>Postcode: 265 04<br>Phone: +30 2610 529 090<br>Email: carelink@gmail.com').openPopup();

    var circle = L.circle([38.290399042463136, 21.79564239581478], {
      color: 'blue',
      fillColor: 'blue',
      fillOpacity: 0.5,
      radius: 100
    }).addTo(map);
  
  }
  
  function initializeUserLocationMarker(userLat, userLng) {
    var myTruck = L.icon({
      iconUrl: 'images/truck.png',
      iconSize: [41, 41],
      iconAnchor: [20, 41],
      popupAnchor: [1, -34]
    });

  
    userLocationMarker = L.marker([userLat, userLng], { draggable: true }).addTo(map).setIcon(myTruck);
    userLocationMarker.bindPopup('Your Location').openPopup();
  
    userLocationMarker.on('dragend', function (event) {
      var position = userLocationMarker.getLatLng();
      userLocationMarker.setLatLng(position);
      userLocationMarker.getPopup().setContent('Your Location, new Position: ' + position.toString()).update();
  
      // Calculate distance between userLocationMarker and baseMarker
      var distance = calculateDistance(position, baseMarker.getLatLng());
      userLocationMarker.setPopupContent(`Your Location - Distance: ${distance.toFixed(2)} kilometers`).update();
  
      updateLine(); // Add this line to update offer lines
      updateRequestLines();
      updateOfferLines();
      checkDistancesAndEnableButtons();
      
    });
 
  }
  
  
  function updateLine() {
    var distance = calculateDistance(baseMarker.getLatLng(), userLocationMarker.getLatLng());
  
    var loadItemsButton = document.getElementById('yourButtonId1');
    var unloadItemsButton = document.getElementById('yourButtonId2');
  
    if (distance < 0.1) {
      loadItemsButton.disabled = false;
      unloadItemsButton.disabled = false;
    } else {
      loadItemsButton.disabled = true;
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

  var volWaitingRequests = [];
  var volWaitingOffers = [];
  var myRequests = [];
  var myOffers = [];
  var myOffers = 0; 

 
  //__________________________Fetch the JSON data from the file__________________________
  fetch('volWaitingRequests.json')
    .then(response => response.json())
    .then(data => {
      volWaitingRequests = data;
    })
    .catch(error => console.error('Error fetching the JSON data:', error));
  
  fetch('volWaitingOffers.json')
    .then(response => response.json())
    .then(data => {
      volWaitingOffers = data;
    })
    .catch(error => console.error('Error fetching the JSON data:', error));
  
  fetch('myRequests.json')
    .then(response => response.json())
    .then(data => {
      myRequests = data;
    })
    .catch(error => console.error('Error fetching the JSON data:', error));
  
  fetch('myOffers.json')
    .then(response => response.json())
    .then(data => {
      myOffers = data;
    })
    .catch(error => console.error('Error fetching the JSON data:', error));

  fetch('taskcount.json')
    .then(response => response.json())
    .then(data => {
        taskCount = parseInt(data[0].task_count, 10);
    })
    .catch(error => console.error('Error fetching the taskcount JSON data:', error));
 
//__________________active layers__________________________________________
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
  layer5: []
};


//___________________________________volWaitingRequests___________________________________
function Waiting_requests_markers(data) {
  // Clear existing markers for the layer
  layerMarkers.layer1 = [];

  // Loop through the data and create markers
  for (let i = 0; i < data.length; i++) {
      const location = new L.LatLng(data[i].latitude, data[i].longitude);

      var civilian_first_name = data[i].civilian_first_name;
      var civilian_last_name = data[i].civilian_last_name;
      var civilian_number = data[i].civilian_number;
      var request_date_posted = data[i].request_date_posted;
      var id_request = parseInt(data[i].id_request);
      var request_category = data[i].request_category;
      var state = data[i].state;
      var request_product_name = data[i].request_product_name;
      var persons = data[i].persons;
      var requestbutton ;

      if (taskCount < 4) {
        requestbutton = '<button class="Acceptbut" onclick="handle_requests(' + id_request + ')">Accept</button>';
      } else {
        requestbutton = '<strong style="display: block; text-align: center; color: red;">You have reached the maximum number of Tasks</strong>';
      } 

       '<button class="Acceptbut" onclick="handle_requests(' + id_request + ')">Accept</button>';

      const message = '<strong>' + civilian_first_name + ' ' + civilian_last_name +
          ' requests: </strong><br>' + '<strong> ' +
          request_category + '</strong>: ' + request_product_name + '<br><strong>For</strong>: ' +
          persons + ' persons' + '<br><strong>Date posted</strong>: ' +
          request_date_posted + '<br><strong>Number: </strong> ' + '+30' + civilian_number +
          '<br><strong>State:</strong> ' + state + '<br>' + requestbutton;

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
      layerMarkers.layer1.push(marker);
  }
  // Update the marker cluster group
  updateClusterGroup();
}



//____________________________offersss_______________________________
function offers_markers(data) {
  layerMarkers.layer2 = [];

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
      var offer_id=parseInt(data[i].offer_id);

      var offerbutton;

      if (taskCount < 4) {
        offerbutton = '<button class="Acceptbut" onclick="handle_offers(' + offer_id + ')">Accept</button>';
      } else {
        offerbutton = '<strong style="display: block; text-align: center; color: red;">You have reached the maximum number of Tasks</strong>';
      } 

    const message = '<strong>' + civilian_first_name + ' ' + civilian_last_name + 
                ' requests: </strong><br>' + '<strong> ' + 
                offer_category + '</strong>: ' + offer_product_name + '<br><strong>For</strong>: ' +
                offer_quantity + ' persons' + '<br><strong>Date posted</strong>: ' + 
                offer_date_posted + '<br><strong>Number: </strong> ' + '+30'+ civilian_number + 
                '<br><strong>State:</strong> ' + offer_status + '<br>' + offerbutton;
                 
    // Create a new marker with a custom icon
    const marker = L.marker(location, {
      icon: L.icon({
        iconUrl: 'pin2.png',
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -32]
      })
    });


    // Bind popup content to the marker
    marker.bindPopup(message);

    // Add marker to the layerMarkers array
    layerMarkers.layer2.push(marker);
  }

  // Update the marker cluster group
  updateClusterGroup();
} 

//___________________________________myRequests___________________________________
let RequestCircles = []; // Array to store the circles

function my_requests(data) {
  layerMarkers.layer3 = [];
  RequestCircles = []; // Clear any existing circles

  // Loop through the data and create markers
  for (let i = 0; i < data.length; i++) {
    const location = new L.LatLng(data[i].latitude, data[i].longitude);
    var civilian_first_name = data[i].civilian_first_name;
    var civilian_last_name = data[i].civilian_last_name;
    var civilian_number = data[i].civilian_number;
    var vehicle_name = data[i].vehicle_name;
    var task_date = data[i].task_date;
    var id_request = data[i].id_request;
    var request_date_posted = data[i].request_date_posted;
    var request_category = data[i].request_category;
    var state = data[i].state;
    var request_product_name = data[i].request_product_name;
    var persons = data[i].persons;

    const message = '<strong>' + civilian_first_name + ' ' + civilian_last_name +
      ' requests: </strong> <br>' + '<strong> ' +
      request_category + '</strong>: ' + request_product_name + '<br><strong>For</strong>: ' +
      persons + ' persons' + '<br><strong>Date posted</strong>: ' +
      request_date_posted + '<br><strong>Number: </strong> ' + '+30' + civilian_number +
      '<br><strong>Vehicle:</strong> ' + vehicle_name +
      '<br><strong>Date Accepted </strong> ' + task_date +
      '<br><strong>State:</strong> ' + state;

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

    // Create a circle around the marker
    const circle = L.circle(location, {
      color: 'blue',
      fillColor: 'blue',
      fillOpacity: 0.5,
      radius: 50 // Radius in meters
    });
    

    // Add the marker and circle to the map
    marker.addTo(map);
    circle.addTo(map);

    // Store the marker and circle
    layerMarkers.layer3.push(marker);
    RequestMarkers.push(marker); // Add to RequestMarkers array
    RequestCircles.push(circle); // Add to RequestCircles array
  }

  // Update the marker cluster group
  updateClusterGroup();
// Check distances and enable buttons

}

function checkDistancesAndEnableButtons() {
  if (!userLocationMarker) return; // Check if userLocationMarker exists

  RequestMarkers.forEach(marker => {
    var distance = calculateDistance(marker.getLatLng(), userLocationMarker.getLatLng());
    // Check if distance is less than 500 meters
    if (distance < 500) {
      // Find the associated button and enable it
      const id_request = marker.options.id_request; // Ensure id_request is stored in marker options
      document.querySelectorAll(`button.DeliverReq`).forEach(button => {
        if (button.getAttribute('onclick').includes(id_request)) {
          button.disabled = false;
        }
      });
    } else {
      // Disable the button if the distance is greater than 500 meters
      const id_request = marker.options.id_request; // Ensure id_request is stored in marker options
      document.querySelectorAll(`button.DeliverReq`).forEach(button => {
        if (button.getAttribute('onclick').includes(id_request)) {
          button.disabled = true;
        }
      });
    }
  });
}

function drawRequestLines() {
  if (!userLocationMarker || !activeLayers.layer3) return; // Check if userLocationMarker exists and layer3 is active

  RequestMarkers.forEach(marker => {
    var line = L.polyline([marker.getLatLng(), userLocationMarker.getLatLng()], { color: 'red' }).addTo(map);
    RequestLines.push(line);
  });
}

function updateRequestLines() {
  if (!userLocationMarker || !activeLayers.layer3) return; // Check if userLocationMarker exists and layer3 is active

  RequestLines.forEach(line => map.removeLayer(line)); // Remove all lines from the map
  RequestLines = []; // Clear the lines array

  // Draw new lines connecting the markers to the user location marker
  drawRequestLines();
}


//_______________________________my_offers__________________________________

let OfferCircles = [];
function my_offers(data) {
  layerMarkers.layer4 = [];
  OfferCircles = []; // Clear any existing circles

  // Loop through the data and create markers
  for (let i = 0; i < data.length; i++) {
    const location = new L.LatLng(data[i].latitude, data[i].longitude);
    var civilian_first_name = data[i].civilian_first_name;
    var civilian_last_name = data[i].civilian_last_name;
    var civilian_number = data[i].civilian_number;
    var vehicle_name = data[i].vehicle_name;
    var task_date = data[i].task_date;
    var offer_id = data[i].offer_id;
    var offer_date_posted = data[i].offer_date_posted;
    var offer_category = data[i].offer_category;
    var offer_status = data[i].offer_status;
    var offer_product_name = data[i].offer_product_name;
    var offer_quantity = data[i].offer_quantity;

    const message = '<strong>' + civilian_first_name + ' ' + civilian_last_name +
      ' requests: </strong> <br>' + '<strong> ' +
      offer_category + '</strong>: ' + offer_product_name + '<br><strong>For</strong>: ' +
      offer_quantity + ' persons' + '<br><strong>Date posted</strong>: ' +
      offer_date_posted + '<br><strong>Number: </strong> ' + '+30' + civilian_number +
      '<br><strong>Vehicle:</strong> ' + vehicle_name +
      '<br><strong>Date Accepted </strong> ' + task_date +
      '<br><strong>State:</strong> ' + offer_status;

    // Create a new marker with a custom icon
    const marker = L.marker(location, {
      icon: L.icon({
        iconUrl: 'pin2.png',
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -32]
      })
    });

    // Bind popup content to the marker
    marker.bindPopup(message);

    // Create a circle around the marker
    const circle = L.circle(location, {
      color: 'blue',
      fillColor: 'blue',
      fillOpacity: 0.5,
      radius: 50 // Radius in meters
    });

    marker.addTo(map);
    circle.addTo(map);

    // Add marker to the layerMarkers array
    layerMarkers.layer4.push(marker);
    OfferMarkers.push(marker); // Add to OfferMarkers array
    OfferCircles.push(circle); // Add to RequestCircles array
  }

  // Update the marker cluster group
  updateClusterGroup();
}


function drawOfferLines() {
  if (!userLocationMarker || !activeLayers.layer4) return; // Check if userLocationMarker exists and layer4 is active

  OfferMarkers.forEach(marker => {
    var line = L.polyline([marker.getLatLng(), userLocationMarker.getLatLng()], { color: 'green' }).addTo(map);
    OfferLines.push(line);
  });
}

function updateOfferLines() {
  if (!userLocationMarker || !activeLayers.layer4) return; // Check if userLocationMarker exists and layer4 is active

  OfferLines.forEach(line => map.removeLayer(line)); // Remove all lines from the map
  OfferLines = []; // Clear the lines array

  // Draw new lines connecting the markers to the user location marker
  
  drawOfferLines();
}


//_________________Function to toggle layers_________________________________
function toggleLayer(layer) {
  if (activeLayers[layer]) {
    // Remove the layer if it is active
    activeLayers[layer] = false;

    if (layer === 'layer3') {
      RequestLines.forEach(line => map.removeLayer(line)); // Remove all request lines from the map
      RequestLines = []; // Clear the request lines array
      RequestCircles.forEach(circle => map.removeLayer(circle)); // Remove all circles from the map
      RequestCircles = []; // Clear the circles array
    }

    // Remove lines if layer4 is deactivated
    if (layer === 'layer4') {
      OfferLines.forEach(line => map.removeLayer(line)); // Remove all offer lines from the map
      OfferLines = []; // Clear the offer lines array
      OfferCircles.forEach(circle => map.removeLayer(circle)); // Remove all circles from the map
      OfferCircles = []; // Clear the circles array
    }

  } else {
    // Add the layer if it is not active
    activeLayers[layer] = true;

    // Initialize markers for the layer if not already done
    if (layer === 'layer1') {
      Waiting_requests_markers(volWaitingRequests);
    } else if (layer === 'layer2') {
      offers_markers(volWaitingOffers);
    } else if (layer === 'layer3') { // Always call my_requests when enabling layer3
      my_requests(myRequests);
      drawRequestLines();

    } else if (layer === 'layer4') { // Always call my_offers when enabling layer4
      my_offers(myOffers);
      drawOfferLines();
    }
  }
  // Update the marker cluster group
  updateClusterGroup();
}


// Function to update the marker cluster group based on active layers
function updateClusterGroup() {
  // Clear all markers from the cluster group
  allMarkersClusterGroup.clearLayers();

  // Προσθήση markers στα ενεργά layers
  for (const layer in activeLayers) {
    if (activeLayers[layer]) {
      allMarkersClusterGroup.addLayers(layerMarkers[layer]);
    }
  }
}