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
  var offers_response;
  var onTheWayRequests;
  var WaitingRequests;
  var Lines;
  var waiting_vehicles = [];
  var onTheWayVihicles = [];
  var offers_response = [];
  var WaitingRequests = [];
  var OnWayRequests = [];
  var data6 = [];


  // Fetch the JSON data from the file
  fetch('waiting_vehicles.json')
  .then(response => response.json())
  .then(data => {
    waiting_vehicles = data;
  })
  .catch(error => console.error('Error fetching the JSON data:', error));
  
  fetch('onTheWayVihicles.json')
  .then(response => response.json())
  .then(data => {
      data2 = data;
  })
  .catch(error => console.error('Error fetching the JSON data:', error));

  fetch('offers_response.json')
  .then(response => response.json())
  .then(data => {
    offers_response = data;
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


  // Function to toggle layers
  function toggleLayer(layer, data) {
      // Remove previously added layers
      if (map.hasLayer(vehiclesWaiting)) {
          map.removeLayer(vehiclesWaiting);
      }
      if (map.hasLayer(vehiclesOnAction)) {
          map.removeLayer(vehiclesOnAction);
      }
      if (map.hasLayer(offers_response)) {
          map.removeLayer(offers_response);
      }
      if (map.hasLayer(WaitingRequests)) {
        map.removeLayer(WaitingRequests);
      }
      if (map.hasLayer(onTheWayRequests)) {
        map.removeLayer(onTheWayRequests);
      }
      if (map.hasLayer(Lines)) {
        map.removeLayer(Lines);
    }
    
      // ανάλογα το κουμπί που πατήθηκε, εμφανίζονται οι κατάλληλοι markers
      if (layer === 'layer1') {
          Waiting_vehicles_markers(waiting_vehicles);
      } else if (layer === 'layer2') {
          on_the_way_vehicles_markers(data2);
      } else if (layer === 'layer3') {
          offers_markers(offers_response); 
      } else if (layer === 'layer4') {
        Waiting_requests_markers(waitingRequests); 
      }
      else if (layer === 'layer5') {
        on_the_way_requests_markers(OnWayRequests); 
      }
      else if (layer === 'layer6') {
        lines(data6); 
      }
  }


/*==================================================================================================================*/


    // Function to initialize markers for waiting requests
    function Waiting_vehicles_markers(data) {
      // Create a new marker cluster group for waiting requests
      vehiclesWaiting = L.markerClusterGroup();
  
      // Loop through the data and create markers
      for (var i = 0; i < data.length; i++) {
          var location = new L.LatLng(data[i].latitude, data[i].longitude);

        var vehicle_name = data[i].vehicle_name;

        var message = '<strong>Vehicle:</strong> ' + vehicle_name;
  
          // Create a new marker with custom icon
          var marker = L.marker(location, {
              icon: L.icon({
                  iconUrl: 'trucker.png', // Path to your custom icon image
                  iconSize: [32, 32], // Size of the icon
                  iconAnchor: [16, 32], // Anchor point of the icon, usually the center bottom
                  popupAnchor: [0, -32] // Popup anchor relative to the icon
              })
          });
  
          // Bind popup content to the marker
          marker.bindPopup(message);
          // Add marker to the marker cluster group
          vehiclesWaiting.addLayer(marker);
      }
  
      // Add the marker cluster group to the map
      map.addLayer(vehiclesWaiting);
  }


/* ================================================================================================================== */


  // Function to initialize markers for waiting requests
  function Waiting_requests_markers(data) {
      // Create a new marker cluster group for waiting requests
      WaitingRequests = L.markerClusterGroup();
  
          // Loop through the data and create markers
        for (var i = 0; i < data.length; i++) {
          var location = new L.LatLng(data[i].latitude, data[i].longitude);
      
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
      
          var message = '<strong>' + first_name + ' ' + last_name + ' requested:</strong><br>' + '<strong>From ' + category + '</strong>: ' + product + '<br><strong>For</strong>: ' +
            persons + ' persons' + '<br><strong>Date posted</strong>: ' + dateposted + '<br><strong>Time posted</strong>: ' + timeposted + '<br><strong>Number:+30</strong> ' +
            number + '<br><strong>State:</strong> ' + state + '<br>' + buttonHtml;  // Include the button HTML in the message
      
          // Create a new marker with custom icon
          var marker = L.marker(location, {
            icon: L.icon({
              iconUrl: 'pin1.png', // Path to your custom icon image
              iconSize: [32, 32], // Size of the icon
              iconAnchor: [16, 32], // Anchor point of the icon, usually the center bottom
              popupAnchor: [0, -32] // Popup anchor relative to the icon
            })
          });
  
          // Bind popup content to the marker
          marker.bindPopup(message);
          // Add marker to the marker cluster group
          WaitingRequests.addLayer(marker);
      }
  
      // Add the marker cluster group to the map
      map.addLayer(WaitingRequests);
  }


/* ================================================================================================================== */


function on_the_way_requests_markers(data) {
  // Create a new marker cluster group for waiting offers
  onTheWayRequests = L.markerClusterGroup();


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

      //var delivery_button = '<button class="Delivery" onclick="deliver_requests(' + request_id + ')">Deliver</button>';
      //var delete_button = '<button class="Delete" onclick="delete_request(' + request_id + ')">Delete</button>';

      var message3 = 'Hello <strong>'+ task_volunteer + '</strong> you request is !'+ '<br><br><strong>'+first_name + ' ' + last_name + ' Requests:</strong><br>' + '<strong>From ' + request_category + '</strong>: ' + request_product_name + '<br><strong>For</strong>: ' 
          + persons + ' persons.' + '<br><strong>Date posted</strong>: ' + request_date_posted + '<br><strong>Time posted</strong>: ' + request_time_posted + '<br><strong>Number:+30</strong> ' 
          + number + '<br><strong>Date accepted</strong>: ' + task_date + '<br><strong>Time posted</strong>: ' + task_time + '<br><strong>State:</strong> ' + state +
          '<br>';  // Include the button HTML in the message

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
      onTheWayRequests.addLayer(marker3); // Add marker to the marker cluster grou
  }

  // Add the marker cluster group to the map
  map.addLayer(onTheWayRequests);

}

/*============================προσφορές===================================== */
// Function to initialize markers for waiting requests
function offers_markers(data) {
  // Create a new marker cluster group for waiting requests
  offers_response = L.markerClusterGroup();

  // Loop through the data and create markers
  for (var i = 0; i < data.length; i++) {
      var location = new L.LatLng(data[i].latitude, data[i].longitude);
      
      var civilian_username = data[i].civilian_username;
      var civilian_number = data[i].civilian_number;
      var offer_date_posted = data[i].offer_date_posted;
      var offer_category = data[i].offer_category;
      var offer_status = data[i].offer_status;
      var offer_product_name = data[i].offer_product_name;
      var offer_quantity = data[i].offer_quantity;
      var vehicle_name = data[i].vehicle_name;

      var message = '<strong>'+civilian_username + ' ' + civilian_number + 
      ' Οffers:</strong><br>' + '<strong>From ' + offer_date_posted + '</strong>: ' + 
      offer_product_name + '<br><strong>For</strong>: ' 
      + offer_quantity + ' persons.' + '<br><strong>Date posted</strong>: ' + offer_date_posted + 
      '<br><strong>Time posted</strong>: ' + vehicle_name + '<br><strong>Number:+30</strong> ' 
      + offer_category + '<br><strong>State:</strong> ' + offer_status; 

      // Create a new marker with custom icon
      var marker = L.marker(location, {
          icon: L.icon({
              iconUrl: 'pin1.png', // Path to your custom icon image
              iconSize: [32, 32], // Size of the icon
              iconAnchor: [16, 32], // Anchor point of the icon, usually the center bottom
              popupAnchor: [0, -32] // Popup anchor relative to the icon
          })
      });

      // Bind popup content to the marker
      marker.bindPopup(message);
      // Add marker to the marker cluster group
      offers_response.addLayer(marker);
  }

  // Add the marker cluster group to the map
  map.addLayer(offers_response);
}


