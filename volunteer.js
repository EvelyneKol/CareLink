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
  
      updateLine();
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
    var request_id=data[i].id_request;
    var request_category = data[i].request_category;
    var state = data[i].state;
    var request_product_name = data[i].request_product_name;
    var persons = data[i].persons;

    var buttonHtml = '<button class="Acceptbut" onclick="handle_requests(' + request_id + ')">Accept</button>';

    const message = '<strong>' + civilian_first_name + ' ' + civilian_last_name + 
                ' requests: </strong><br>' + '<strong> ' + 
                request_category + '</strong>: ' + request_product_name + '<br><strong>For</strong>: ' +
                persons + ' persons' + '<br><strong>Date posted</strong>: ' + 
                request_date_posted + '<br><strong>Number: </strong> ' + '+30'+ civilian_number + 
                '<br><strong>State:</strong> ' + state + '<br>' + buttonHtml;
                 
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
      var offer_id=data[i].offer_id;

      
      var buttonHtml = '<button class="Acceptbut" onclick="handle_offers(' + offer_id + ')">Accept</button>';


     

    const message = '<strong>' + civilian_first_name + ' ' + civilian_last_name + 
                ' requests: </strong><br>' + '<strong> ' + 
                offer_category + '</strong>: ' + offer_product_name + '<br><strong>For</strong>: ' +
                offer_quantity + ' persons' + '<br><strong>Date posted</strong>: ' + 
                offer_date_posted + '<br><strong>Number: </strong> ' + '+30'+ civilian_number + 
                '<br><strong>State:</strong> ' + offer_status + '<br>' + buttonHtml;
                 
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
function my_requests(data) {
  layerMarkers.layer3 = [];

  // Loop through the data and create markers
  for (let i = 0; i < data.length; i++) {
    const location = new L.LatLng(data[i].latitude, data[i].longitude);
      var civilian_first_name = data[i].civilian_first_name ;
      var civilian_last_name = data[i].civilian_last_name ;
      var civilian_number = data[i].civilian_number ;
      var vehicle_name = data[i].vehicle_name ;
      var task_date = data[i].task_date ;
      var id_request = data[i].id_request;
      var request_date_posted = data[i].request_date_posted;
      var request_category = data[i].request_category ;
      var state=data[i].state;
      var request_product_name=data[i].request_product_name;
      var persons=data[i].persons;

      const message = '<strong>' + civilian_first_name + ' ' + civilian_last_name + 
      ' requests: </strong> <br>' + '<strong> ' + 
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
    layerMarkers.layer3.push(marker);

    requestMarkers.push(marker);
  }

  // Update the marker cluster group
  updateClusterGroup();
  
  // προσθήκη γραμμών που εννωνουν τους markers με το location χρήστη
  drawRequestLines();
} 


//_____________________________________________________________________________

// Function to draw lines connecting request markers to user location marker
function drawRequestLines() {
  if (!userLocationMarker) return; // Check if userLocationMarker exists
  requestMarkers.forEach(marker => {
    var line = L.polyline([marker.getLatLng(), userLocationMarker.getLatLng()], { color: 'red' }).addTo(map);
    requestLines.push(line);
  });
}

// Update lines when the user location marker is dragged
function updateRequestLines() {
  if (!userLocationMarker) return; // Check if userLocationMarker exists
  requestLines.forEach(line => map.removeLayer(line)); // Remove all lines from the map
  requestLines = []; // Clear the lines array
  // Draw new lines connecting the markers to the user location marker
  drawRequestLines();
}

// Call updateRequestLines when the user location marker is dragged
if (userLocationMarker) {
  userLocationMarker.on('dragend', function (event) {
    updateRequestLines();
  });
}

//_______________________________my_offers__________________________________
function my_offers(data) {
  layerMarkers.layer4 = [];

  // Loop through the data and create markers
  for (let i = 0; i < data.length; i++) {
    const location = new L.LatLng(data[i].latitude, data[i].longitude);
      var civilian_first_name = data[i].civilian_first_name ;
      var civilian_last_name = data[i].civilian_last_name ;
      var civilian_number = data[i].civilian_number ;
      var vehicle_name = data[i].vehicle_name ;
      var task_date = data[i].task_date ;
      var offer_id = data[i].offer_id;
      var offer_date_posted = data[i].offer_date_posted;
      var offer_category = data[i].offer_category ;
      var offer_status=data[i].offer_status;
      var offer_product_name=data[i].offer_product_name;
      var offer_quantity=data[i].offer_quantity;

      const message = '<strong>' + civilian_first_name + ' ' + civilian_last_name + 
      ' requests: </strong> <br>' + '<strong> ' + 
      offer_category + '</strong>: ' + offer_product_name + '<br><strong>For</strong>: ' +
      offer_quantity + ' persons' + '<br><strong>Date posted</strong>: ' + 
      offer_date_posted + '<br><strong>Number: </strong> ' + '+30'+ civilian_number +
      '<br><strong>Vehicle:</strong> ' + vehicle_name +
      '<br><strong>Date Accepted </strong> ' + task_date + 
      '<br><strong>State:</strong> ' + offer_status ;
                 
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
    layerMarkers.layer4.push(marker);
  }

  // Update the marker cluster group
  updateClusterGroup();
} 


//________________linesss____________________________________________
let layerLines = [];

// Function to draw lines between layers
function drawLinesforOffersandRequest() {
  // Clear existing lines
  layerLines.forEach(line => map.removeLayer(line));
  layerLines = [];

  if (activeLayers.layer3 ) {
    layerMarkers.layer3.forEach(requestMarker => {
      const vehicleName = requestMarker.getPopup().getContent().match(/<strong>Vehicle:<\/strong> (.+?)<br>/)[1];
      const vehicleLatLng = requestMarker.getLatLng();
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

  if (activeLayers.layer4) {
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



//__________________Function to toggle layers_________________________________
function toggleLayer(layer) {
  if (activeLayers[layer]) {
    // Remove the layer if it is active
    activeLayers[layer] = false;
  } else {
    // Add the layer if it is not active
    activeLayers[layer] = true;

    // Initialize markers for the layer if not already done
    if (layer === 'layer1' && layerMarkers.layer1.length === 0) {
      Waiting_requests_markers(volWaitingRequests);
    } else if (layer === 'layer2' && layerMarkers.layer2.length === 0) {
      offers_markers(volWaitingOffers);

    } else if (layer === 'layer3' && layerMarkers.layer3.length === 0) {
      my_requests(myRequests);
      
    } else if (layer === 'layer4' && layerMarkers.layer4.length === 0) {
       my_offers(myOffers);
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





  /* // Function to toggle layers
  function toggleLayer(layer, data) {
    // Remove previously added layers and lines
    if (map.hasLayer(waitingRequestsLayer)) {
      map.removeLayer(waitingRequestsLayer);
    }
    if (map.hasLayer(waitingOffersLayer)) {
      map.removeLayer(waitingOffersLayer);
    }
    if (map.hasLayer(myRequestsLayer)) {
      map.removeLayer(myRequestsLayer);
    }
    if (map.hasLayer(myOffersLayer)) {
      map.removeLayer(myOffersLayer);
    }
    requestMarkers = []; // Clear the markers array
    requestLines.forEach(line => map.removeLayer(line)); // Remove all lines from the map
    requestLines = []; // Clear the lines array


    OfferMarkers = [];
    OfferLines.forEach(line => map.removeLayer(line)); // Remove all lines from the map
    OfferLines = [];
  
    // Check which layer is selected and add the corresponding layer
    if (layer === 'layer1') {
      Waiting_requests_markers(data1);
    } else if (layer === 'layer2') {
      Waiting_offers_markers(data2); // Assuming data2 is another dataset
    } else if (layer === 'layer3') {
      On_way_requests_markers(data3); // Assuming data3 is another dataset
    } else if (layer === 'layer4') {
      On_way_Offers_markers(data4); // Assuming data4 is another dataset
    }
  }
  
 

// Function to draw lines connecting request markers to user location marker
function drawRequestLines() {
  if (!userLocationMarker) return; // Check if userLocationMarker exists

  requestMarkers.forEach(marker3 => {
    var line = L.polyline([marker3.getLatLng(), userLocationMarker.getLatLng()], { color: 'red' }).addTo(map);
    requestLines.push(line);
  });
}

// Update lines when the user location marker is dragged
function updateRequestLines() {
  if (!userLocationMarker) return; // Check if userLocationMarker exists

  requestLines.forEach(line => map.removeLayer(line)); // Remove all lines from the map
  requestLines = []; // Clear the lines array

  // Draw new lines connecting the markers to the user location marker
  drawRequestLines();
}

// Call updateRequestLines when the user location marker is dragged
if (userLocationMarker) {
  userLocationMarker.on('dragend', function (event) {
    updateRequestLines();
  });
}

function On_way_Offers_markers(data) {
    // Create a new marker cluster group for waiting offers
    myOffersLayer = L.markerClusterGroup();

    // Loop through the data and create markers
    for (var i = 0; i < data.length; i++) {
        var location4 = new L.LatLng(data[i].latitude, data[i].longitude);

        var offer_id = data[i].offer_id;
        var offer_category = data[i].offer_category;
        var offer_product_name = data[i].offer_product_name;
        var offer_quantity = data[i].offer_quantity;
        var offer_date_posted = data[i].offer_date_posted;
        var offer_time_posted = data[i].offer_time_posted;
        var offer_status = data[i].offer_status;

        var number = data[i].number;
        var first_name = data[i].first_name;
        var last_name = data[i].last_name;
        var task_date = data[i].task_date;
        var task_time = data[i].task_time;
        var task_volunteer = data[i].task_volunteer;
  
        //var delivery_button = '<button class="Delivery" onclick="handle_offers(' + offer_id + ')">Take</button>';
        //var delete_button = '<button class="Delete" onclick="delete_offer(' + offer_id + ')">Delete</button>';

        var message4 = 'Hello <strong>'+ task_volunteer + '</strong> you request is !'+ '<br><br><strong>'+first_name + ' ' + last_name + ' Requests:</strong><br>' + '<strong>From ' + offer_category + '</strong>: ' + offer_product_name + '<br><strong>For</strong>: ' 
            + offer_quantity + ' persons.' + '<br><strong>Date posted</strong>: ' + offer_date_posted + '<br><strong>Time posted</strong>: ' + offer_time_posted + '<br><strong>Number:+30</strong> ' 
            + number + '<br><strong>Date accepted</strong>: ' + task_date + '<br><strong>Time posted</strong>: ' + task_time + '<br><strong>State:</strong> ' + offer_status +
            '<br>';  // Include the button HTML in the message

        // Create a new marker with custom icon
        var marker4 = L.marker(location4, { 
            icon: L.icon({
                iconUrl: 'pin2.png', // Path to your custom icon image
                iconSize: [32, 32], // Size of the icon
                iconAnchor: [16, 32], // Anchor point of the icon, usually the center bottom
                popupAnchor: [0, -32] // Popup anchor relative to the icon
            })
        });
        
        // Bind popup content to the marker
        marker4.bindPopup(message4);
        myOffersLayer.addLayer(marker4); // Add marker to the marker cluster group
        OfferMarkers.push(marker4);
    }

    // Add the marker cluster group to the map
    map.addLayer(myOffersLayer);
    drawOffersLines();
}

function drawOffersLines() {
  if (!userLocationMarker) return; // Check if userLocationMarker exists

  OfferMarkers.forEach(marker4 => {
    var line = L.polyline([marker4.getLatLng(), userLocationMarker.getLatLng()], { color: 'red' }).addTo(map);
    OfferLines.push(line);
  });
}

// Update lines when the user location marker is dragged
function updateOfferLines() {
  if (!userLocationMarker) return; // Check if userLocationMarker exists

  OfferLines.forEach(line => map.removeLayer(line)); // Remove all lines from the map
  OfferLines = []; // Clear the lines array

  // Draw new lines connecting the markers to the user location marker
  drawOffersLines();
}

// Call updateRequestLines when the user location marker is dragged
if (userLocationMarker) {
  userLocationMarker.on('dragend', function (event) {
    updateOfferLines();
  });
} */