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
  attribution:
    '<a href="https://www.maptiler.com/copyright/" target="_blank">&copy; MapTiler</a> <a href="https://www.openstreetmap.org/copyright" target="_blank">&copy; OpenStreetMap contributors</a>'
}).addTo(map);

var userLocationMarker;
var baseMarker = L.marker([38.2904558214517, 21.79578903224108], { draggable: true });
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
    document.getElementById("address1").value = geolocation;


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

      updateLine();
      // Update the line
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

var waitingRequestsLayer;
var waitingOffersLayer;
var myRequestsLayer;
var myOffersLayer;


// Function to toggle layers
function toggleLayer(layer) {
  // Remove previously added layers
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

  // Check which layer is selected and add the corresponding layer
  if (layer === 'layer1') {
      Waiting_requests_markers(data1);
  } else if (layer === 'layer2') {
      Waiting_offers_markers(data2);
  } else if (layer === 'layer3') {
      On_way_requests_markers(data3);
  } else if (layer === 'layer4') {
      On_way_Offers_markers(data4);
} 
}

// Function to initialize markers
function Waiting_requests_markers(data) {
  // Create a new marker cluster group for waiting requests
  waitingRequestsLayer = L.markerClusterGroup();

    // Loop through the data and create markers
    for (var i = 0; i < data.length; i++) {
        var location1 = new L.LatLng(data[i].latitude, data[i].longitude);

        var request_id = data[i].id_request;
        var category = data[i].request_category;
        var product = data[i].request_product_name;
        var persons = data[i].persons;
        var dateposted = data[i].request_date_posted;
        var timeposted = data[i].request_time_posted;
        var state = data[i].state;
        var number = data[i].number;
        var first_name = data[i].first_name;
        var last_name = data[i].last_name;

        var buttonHtml = '<button class="Acceptbut" onclick="handle_requests(' + request_id + ')">Accept</button>';

        var message1 = '<strong>' + first_name + ' ' + last_name + ' requested:</strong><br>' + '<strong>From ' + category + '</strong>: ' + product + '<br><strong>For</strong>: ' 
            + persons + ' persons' + '<br><strong>Date posted</strong>: ' + dateposted + '<br><strong>Time posted</strong>: ' + timeposted + '<br><strong>Number:+30</strong> ' 
            + number + '<br><strong>State:</strong> ' + state +'<br>' + buttonHtml;  // Include the button HTML in the message

        // Create a new marker with custom icon
        var marker1 = L.marker(location1, {
            icon: L.icon({
                iconUrl: 'pin1.png', // Path to your custom icon image
                iconSize: [32, 32], // Size of the icon
                iconAnchor: [16, 32], // Anchor point of the icon, usually the center bottom
                popupAnchor: [0, -32] // Popup anchor relative to the icon
            })
        });

        // Bind popup content to the marker
        marker1.bindPopup(message1);

        // Add marker to the marker cluster group
        waitingRequestsLayer.addLayer(marker1);
    }

  // Add the marker cluster group to the map
  map.addLayer(waitingRequestsLayer);

}
// Call the initializeMarkers function to initialize markers
Waiting_requests_markers(data1);


// Function to initialize waiting offers markers
function Waiting_offers_markers(data) {
  // Create a new marker cluster group for waiting offers
  waitingOffersLayer = L.markerClusterGroup();

    // Loop through the data and create markers
    for (var i = 0; i < data.length; i++) {
        var location2 = new L.LatLng(data[i].latitude, data[i].longitude);

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

        var buttonHtml = '<button class="Acceptbut" onclick="handle_offers(' + offer_id + ')">Accept</button>';

        var message2 = '<strong>'+first_name + ' ' + last_name + ' Οffers:</strong><br>' + '<strong>From ' + offer_category + '</strong>: ' + offer_product_name + '<br><strong>For</strong>: ' 
            + offer_quantity + ' persons.' + '<br><strong>Date posted</strong>: ' + offer_date_posted + '<br><strong>Time posted</strong>: ' + offer_time_posted + '<br><strong>Number:+30</strong> ' 
            + number + '<br><strong>State:</strong> ' + offer_status +
            '<br>' + buttonHtml;  // Include the button HTML in the message

        // Create a new marker with custom icon
        var marker2 = L.marker(location2, {
            icon: L.icon({
                iconUrl: 'pin2.png', // Path to your custom icon image
                iconSize: [32, 32], // Size of the icon
                iconAnchor: [16, 32], // Anchor point of the icon, usually the center bottom
                popupAnchor: [0, -32] // Popup anchor relative to the icon
            })
        });

        // Bind popup content to the marker
        marker2.bindPopup(message2);
        // Add marker to the marker cluster group
        waitingOffersLayer.addLayer(marker2);
    }

  // Add the marker cluster group to the map
    map.addLayer(waitingOffersLayer);
} 

function On_way_requests_markers(data) {
    // Create a new marker cluster group for waiting offers
    myRequestsLayer = L.markerClusterGroup();
 

    // Loop through the data and create markers
    for (var i = 0; i < data.length; i++) {
        var location3 = new L.LatLng(data[i].latitude, data[i].longitude);

        var request_id = data[i].id_request;
        var request_category = data[i].request_category;
        var request_product_name = data[i].request_product_name;
        var persons = data[i].persons;
        var request_date_posted = data[i].request_date_posted;
        var request_time_posted = data[i].request_time_posted;
        var state = data[i].state;
        var number = data[i].number;
        var task_date = data[i].task_date;
        var task_time = data[i].task_time;
        var task_volunteer = data[i].task_volunteer;
        var first_name = data[i].first_name;
        var last_name = data[i].last_name;

        var delivery_button = '<button class="Delivery" onclick="deliver_requests(' + request_id + ')">Deliver</button>';
        var delete_button = '<button class="Delete" onclick="delete_request(' + request_id + ')">Delete</button>';
        var message3 = 'Hello <strong>'+ task_volunteer + '</strong> you request is !'+ '<br><br><strong>'+first_name + ' ' + last_name + ' Requests:</strong><br>' + '<strong>From ' + request_category + '</strong>: ' + request_product_name + '<br><strong>For</strong>: ' 
            + persons + ' persons.' + '<br><strong>Date posted</strong>: ' + request_date_posted + '<br><strong>Time posted</strong>: ' + request_time_posted + '<br><strong>Number:+30</strong> ' 
            + number + '<br><strong>Date accepted</strong>: ' + task_date + '<br><strong>Time posted</strong>: ' + task_time + '<br><strong>State:</strong> ' + state +
            '<br>' + delivery_button + delete_button;  // Include the button HTML in the message

        // Create a new marker with custom icon
        var marker3 = L.marker(location3, { 
            icon: L.icon({
                iconUrl: 'pin1.png', // Path to your custom icon image
                iconSize: [32, 32], // Size of the icon
                iconAnchor: [16, 32], // Anchor point of the icon, usually the center bottom
                popupAnchor: [0, -32] // Popup anchor relative to the icon
            })
        });
        
        // Bind popup content to the marker
        marker3.bindPopup(message3);
        myRequestsLayer.addLayer(marker3); // Add marker to the marker cluster group
    }

    // Add the marker cluster group to the map
    map.addLayer(myRequestsLayer);
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
  
        var delivery_button = '<button class="Delivery" onclick="handle_offers(' + offer_id + ')">Take</button>';
        var delete_button = '<button class="Delete" onclick="delete_offer(' + offer_id + ')">Delete</button>';

        var message4 = 'Hello <strong>'+ task_volunteer + '</strong> you request is !'+ '<br><br><strong>'+first_name + ' ' + last_name + ' Requests:</strong><br>' + '<strong>From ' + offer_category + '</strong>: ' + offer_product_name + '<br><strong>For</strong>: ' 
            + offer_quantity + ' persons.' + '<br><strong>Date posted</strong>: ' + offer_date_posted + '<br><strong>Time posted</strong>: ' + offer_time_posted + '<br><strong>Number:+30</strong> ' 
            + number + '<br><strong>Date accepted</strong>: ' + task_date + '<br><strong>Time posted</strong>: ' + task_time + '<br><strong>State:</strong> ' + offer_status +
            '<br>' + delivery_button + delete_button;  // Include the button HTML in the message

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
    }

    // Add the marker cluster group to the map
    map.addLayer(myOffersLayer);
}