var map = L.map('map').setView([38.2904558214517, 21.79578903224108], 14);
L.tileLayer('https://api.maptiler.com/maps/basic/256/{z}/{x}/{y}.png?key=dVhthbXQs3EHCi0XzzkL', {
attribution:
    '<a href="https://www.maptiler.com/copyright/" target="_blank">&copy; MapTiler</a> <a href="https://www.openstreetmap.org/copyright" target="_blank">&copy; OpenStreetMap contributors</a>'
}).addTo(map);

var userLocationMarker;
var baseMarker = L.marker([38.2904558214517, 21.79578903224108], { draggable: true });
var popup1 = baseMarker.bindPopup('Address: 25th March, Patras Greece<br>Postcode: 265 04<br>Phone: +30 2610 529 090<br>Email: carelink@gmail.com').openPopup();

var line = L.polyline([], { color: 'black' }).addTo(map); // Initialize an empty polyline


function initializeBaseMarker(userLat, userLng) {
var redIcon = L.icon({
    iconUrl: 'images/pin.png',
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
    
    // Calculate distance between baseMarker and userLocationMarker
    var distance = calculateDistance(userLocationMarker.getLatLng(), position);
    userLocationMarker.setPopupContent(`Your Location - Distance: ${distance.toFixed(2)} kilometers`).update();

    // Update the line
    updateLine();
    
});
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


// Check which layer is selected and add the corresponding layer
if (layer === 'layer1') {
    Waiting_requests_markers(data1); 
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


