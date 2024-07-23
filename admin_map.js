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
    });
  }
  
  // Define global variables for layers
  var waitingRequestsLayer;
  var waitingOffersLayer;
  var myRequestsLayer;
  var myOffersLayer;
  var data1 = [];

  // Fetch the JSON data from the file
  fetch('data.json')
  .then(response => response.json())
  .then(data => {
      data1 = data;
  })
  .catch(error => console.error('Error fetching the JSON data:', error));
  

  // Function to toggle layers
  function toggleLayer(layer, data) {
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
          Waiting_offers_markers(data2); // Assuming data2 is another dataset
      } else if (layer === 'layer3') {
          On_way_requests_markers(data3); // Assuming data3 is another dataset
      } else if (layer === 'layer4') {
          On_way_Offers_markers(data4); // Assuming data4 is another dataset
      }
  }
  
  // Function to initialize markers for waiting requests
  function Waiting_requests_markers(data) {
      // Create a new marker cluster group for waiting requests
      waitingRequestsLayer = L.markerClusterGroup();
  
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
          waitingRequestsLayer.addLayer(marker);
      }
  
      // Add the marker cluster group to the map
      map.addLayer(waitingRequestsLayer);
  }