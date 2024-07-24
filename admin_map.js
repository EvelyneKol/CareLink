  var map = L.map('map').setView([38.2904558214517, 21.79578903224108], 14);
  L.tileLayer('https://api.maptiler.com/maps/basic/256/{z}/{x}/{y}.png?key=dVhthbXQs3EHCi0XzzkL', {
    attribution:
      '<a href="https://www.maptiler.com/copyright/" target="_blank">&copy; MapTiler</a> <a href="https://www.openstreetmap.org/copyright" target="_blank">&copy; OpenStreetMap contributors</a>'
  }).addTo(map);
  
  var layerGroups = {
    layer1: L.markerClusterGroup(),
    layer2: L.markerClusterGroup(),
    layer3: L.markerClusterGroup(),
    layer4: L.markerClusterGroup(),
    layer5: L.markerClusterGroup()
};


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
  var offers;
  var onTheWayRequests;
  var WaitingRequests;
  var Lines;
  var data1 = [];
  var data2 = [];
  var data3 = [];
  var data4 = [];
  var data5 = [];
  var data6 = [];


  // Fetch the JSON data from the file
  fetch('data1.json')
  .then(response => response.json())
  .then(data => {
      data1 = data;
  })
  .catch(error => console.error('Error fetching the JSON data:', error));
  
  fetch('data2.json')
  .then(response => response.json())
  .then(data => {
      data2 = data;
  })
  .catch(error => console.error('Error fetching the JSON data:', error));

  fetch('data3.json')
  .then(response => response.json())
  .then(data => {
      data3 = data;
  })
  .catch(error => console.error('Error fetching the JSON data:', error));

  fetch('data4.json')
  .then(response => response.json())
  .then(data => {
      data4 = data;
  })
  .catch(error => console.error('Error fetching the JSON data:', error));

  fetch('data5.json')
  .then(response => response.json())
  .then(data => {
      data4 = data;
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
      if (map.hasLayer(offers)) {
          map.removeLayer(offers);
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
          Waiting_vehicles_markers(data1);
      } else if (layer === 'layer2') {
          on_the_way_vehicles_markers(data2);
      } else if (layer === 'layer3') {
          offers_markers(data3); 
      } else if (layer === 'layer4') {
        Waiting_requests_markers(data4); 
      }
      else if (layer === 'layer5') {
        on_the_way_requests_markers(data5); 
      }
      else if (layer === 'layer6') {
        lines(data6); 
      }
  }

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
  
          var message = '<strong>' + first_name + ' ' + last_name + ' requested:</strong><br>' + '<strong>From ' + category + '</strong>: ' + product + '<br><strong>For</strong>: ' 
              + persons + ' persons' + '<br><strong>Date posted</strong>: ' + dateposted + '<br><strong>Time posted</strong>: ' + timeposted + '<br><strong>Number:+30</strong> ' 
              + number + '<br><strong>State:</strong> ' + state +'<br>';  // Include the button HTML in the message
  
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



   // Function to initialize markers for waiting requests
   function on_the_way_requests_markers(data) {
    // Create a new marker cluster group for waiting requests
    onTheWayRequests = L.markerClusterGroup();

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

        var message = '<strong>' + first_name + ' ' + last_name + ' requested:</strong><br>' + '<strong>From ' + category + '</strong>: ' + product + '<br><strong>For</strong>: ' 
            + persons + ' persons' + '<br><strong>Date posted</strong>: ' + dateposted + '<br><strong>Time posted</strong>: ' + timeposted + '<br><strong>Number:+30</strong> ' 
            + number + '<br><strong>State:</strong> ' + state +'<br>';  // Include the button HTML in the message

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
        onTheWayRequests.addLayer(marker);
    }

    // Add the marker cluster group to the map
    map.addLayer(onTheWayRequests);
}



