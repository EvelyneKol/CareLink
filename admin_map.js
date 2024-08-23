//αρχικοποίηση χάρτη, συντεταγμένων, zoom και διαμόρφωσης
var map = L.map('map').setView([38.24663362118412, 21.734787451795558], 12);

L.tileLayer('https://api.maptiler.com/maps/basic/256/{z}/{x}/{y}.png?key=dVhthbXQs3EHCi0XzzkL', {
    attribution: '<a href="https://www.maptiler.com/copyright/" target="_blank">&copy; MapTiler</a> \
    <a href="https://www.openstreetmap.org/copyright" target="_blank">&copy; OpenStreetMap contributors</a>'
}).addTo(map);

// Χρήση AJAX για ανάκτηση συντεταγμένων από την βάση μέσω του αρχείου get_location.php για αρχικοποίηση του marker
$.ajax({
    url: 'get_location.php',
    type: 'GET',
    dataType: 'json',  // Τύπος δεδομένων που αναμένεται να επιστραφεί (JSON)
    success: function(response) {
       // Έλεγχος αν υπάρχει σφάλμα στην απόκριση
        if (response.error) {
            console.error(response.error);
            return;
        }

        // Μετατροπή των συντεταγμένων σε αριθμητικές τιμές
        var Lat = parseFloat(response.latitude);
        var Lng = parseFloat(response.longitude);

        // Κλήση της συνάρτησης για να δημιουργηθεί ο αρχικός marker
        initializeBaseMarker(Lat, Lng);
    },
    error: function(xhr, status, error) {
        // Εμφάνιση μηνύματος σφάλματος αν αποτύχει η AJAX κλήση
        console.error(error);
    }
});

// Συνάρτηση για αρχικοποίηση του base marker με τις δεδομένες συντεταγμένες
function initializeBaseMarker(Lat, Lng) {
  // Δημιουργία προσαρμοσμένου εικονιδίου για το marker (διαστάσεις εικονιδίου και popup)
  var baseIcon = L.icon({
      iconUrl: 'images/base.png',
      iconSize: [41, 41],
      iconAnchor: [20, 41],
      popupAnchor: [1, -34]
  });


  // Δημιουργία του marker με δυνατότητα μεταφοράς (draggable)
  baseMarker = L.marker([Lat, Lng], { draggable: true }).addTo(map).setIcon(baseIcon);
  // Προσθήκη popup με πληροφορίες για την τοποθεσία
  baseMarker.bindPopup('Base <br>Phone: +30 2610 529 090<br>Email: carelink@gmail.com').openPopup();

  // Αποθήκευση της αρχικής θέσης του marker
  originalBasePosition = baseMarker.getLatLng();

  // Διαχείριση του event μεταφοράς του marker
  baseMarker.on('dragend', function (event) {
      var newPosition = baseMarker.getLatLng();

      // Επιβεβαίωση από τον χρήστη για την αλλαγή θέσης
      if (confirm("Are you sure you want to move the base marker to this new location?")) {
          // Ενημέρωση της θέσης του marker
          baseMarker.setLatLng(newPosition);
          baseMarker.getPopup().setContent('BASE, new Position: ' + newPosition.toString()).update();
          $("#Latitude").val(newPosition.lat);
          $("#Longitude").val(newPosition.lng).keyup();

          // AJAX κλήση για ενημέρωση της βάσης δεδομένων με τις νέες συντεταγμένες
          $.ajax({
              url: 'update_location.php', //url αρχέιου όπου θα γίνει η ενημέρωση των συντεταγμένων
              type: 'POST',
              data: { //νέες συντεταγμενες 
                  latitude: newPosition.lat,
                  longitude: newPosition.lng
              },
              success: function(response) {
                  console.log(response);
              },
              error: function(xhr, status, error) {
                  console.error(error);
              }
          });

          originalBasePosition = newPosition; // Ενημέρωση της αρχικής θέσης με τη νέα θέση
      } else {
          baseMarker.setLatLng(originalBasePosition); // Αν ο χρήστης ακυρώσει, επαναφορά του marker στην αρχική θέση
      }
  });
}



// ορισμός global μεταβλητών για τα layers
var vehicles = [];
var Offers = [];
var WaitingRequests = [];
var OnWayRequests = [];


// Ανάκτηση δεδομένων από JSON αρχείο για τα οχήματα
fetch('vehicles.json')
.then(response => response.json())
.then(data => {
  vehicles = data;
})
.catch(error => console.error('Error fetching the JSON data:', error));

// Ανάκτηση δεδομένων από JSON αρχείο για τις προσφορές
fetch('Offers.json')
.then(response => response.json())
.then(data => {
  Offers = data;
})
.catch(error => console.error('Error fetching the JSON data:', error));

// Ανάκτηση δεδομένων από JSON αρχείο για τις αναμονές
fetch('waitingRequests.json')
.then(response => response.json())
.then(data => {
  waitingRequests = data;
})
.catch(error => console.error('Error fetching the JSON data:', error));

// Ανάκτηση δεδομένων από JSON αρχείο για τα ενεργά αιτήματα
fetch('OnWayRequests.json')
.then(response => response.json())
.then(data => {
  OnWayRequests = data;
})
.catch(error => console.error('Error fetching the JSON data:', error));

// Ενεργά επίπεδα φίλτρων του χάρτη
const activeLayers = {};

// δημιουργία global marker cluster group
const allMarkersClusterGroup = L.markerClusterGroup();
map.addLayer(allMarkersClusterGroup);

// Αποθήκευση όλων των markers ανά επίπεδο (layer)
const layerMarkers = {
  layer1: [], // εν αναμονή οχήματα
  layer2: [], // Οχήματα καθ' οδόν
  layer3: [],
  layer4: [],
  layer5: [],
  layer6: []
};

// Συνάρτηση για αρχικοποίηση markers για τα οχήματα σε αναμονή
function Waiting_vehicles_markers(data) {
  // Εκκαθάριση υπαρχόντων markers για το συγκεκριμένο επίπεδο
  layerMarkers.layer1 = [];

  // Δημιουργία markers για κάθε όχημα στα δεδομένα
  for (let i = 0; i < data.length; i++) {
    const location = new L.LatLng(data[i].latitude, data[i].longitude);
        var vehicle_name = data[i].vehicle_name;
        var quantity = data[i].quantity;
        var products = data[i].products;
        var task_count = data[i].task_count;
        var state= 'Waiting';
        
    // Μήνυμα popup με τις πληροφορίες του οχήματος
    const message = '<strong>Vehicle:</strong> ' + vehicle_name + '<br>' +
                    '<strong>State:</strong> ' + state + '<br>' +
                    '<strong>Products:</strong> ' +products +'<br>'+'<strong>Quantity:</strong> ' +
                     + quantity +'<br>'+ '<strong>No of Active Tasks: </strong> ' + task_count;
    
    // Δημιουργία marker με προσαρμοσμένο εικονίδιο για τα φορτηγά
    const marker = L.marker(location, {
      icon: L.icon({
        iconUrl: 'truck.png',
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -32]
      })
    });

    // Σύνδεση του popup με τον marker
    marker.bindPopup(message);
    if(task_count==0){
    // Προσθήκη του marker στο array του επιπέδου
    layerMarkers.layer1.push(marker);
  }
}
  // Ενημέρωση του marker cluster group
  updateClusterGroup();
}

// Συνάρτηση για αρχικοποίηση markers για τα οχήματα καθ' οδόν
function on_the_way_vehicles_markers(data) {
  // καθαρισμός
  layerMarkers.layer2 = [];
  
  // Loop στα data και δημιουργία markers
  for (let i = 0; i < data.length; i++) {
    const location = new L.LatLng(data[i].latitude, data[i].longitude);
        var vehicle_name = data[i].vehicle_name;
        var quantity = data[i].quantity;
        var products = data[i].products;
        var task_count = data[i].task_count;
        var state= 'Active';

    // Μήνυμα popup με τις πληροφορίες του οχήματος
    const message = '<strong>Vehicle:</strong> ' + vehicle_name + '<br>' +
                    '<strong>State:</strong> ' + state + '<br>' +
                    '<strong>Products:</strong> ' +products +'<br>'+'<strong>Quantity:</strong> ' +
                    + quantity +'<br>'+ '<strong>No of Active Tasks: </strong> ' + task_count;

    // Δημιουργία marker με προσαρμοσμένο εικονίδιο
    const marker = L.marker(location, {
      icon: L.icon({
        iconUrl: 'truck.png',
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -32]
      })
    });

    // Σύνδεση του popup με τον marker
    marker.bindPopup(message);

    if(task_count>0){
    // προσθήκη marker sto layerMarkers array
    layerMarkers.layer2.push(marker);
    }
  }
  // Update the marker cluster group
  updateClusterGroup();
}

// Function για αρχικοποίηση markers για αιτήματα σε αναμονή
function Waiting_requests_markers(data) {
  // Καθαρίζει τα υπάρχοντα markers για το επίπεδο
  layerMarkers.layer4 = [];

  // Διατρέχει τα δεδομένα και δημιουργεί markers
  for (let i = 0; i < data.length; i++) {
    const location = new L.LatLng(data[i].latitude, data[i].longitude);

    // αποθήκευση των λεπτομεριών του αιτήματος σε μεταβλητές
          var civilian_first_name = data[i].civilian_first_name;
          var civilian_last_name = data[i].civilian_last_name;
          var civilian_number = data[i].civilian_number;
          var request_date_posted = data[i].request_date_posted;
          var request_category = data[i].request_category;
          var state = data[i].state;
          var request_product_name = data[i].request_product_name;
          var persons = data[i].persons;

    // popup του marker
    const message = '<strong>' + civilian_first_name + ' ' + civilian_last_name + 
                ' requests: </strong><br>' + '<strong> ' + 
                request_category + '</strong>: ' + request_product_name + '<br><strong>For</strong>: ' +
                persons + ' persons' + '<br><strong>Date posted</strong>: ' + 
                request_date_posted + '<br><strong>Number: </strong> ' + '+30'+ civilian_number + 
                '<br><strong>State:</strong> ' + state ;
                 
    // δημιουργία νέου marker 
    const marker = L.marker(location, {
      icon: L.icon({
        iconUrl: 'pin1.png',
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -32]
      })
    });

    // σύνδεση popup με marker
    marker.bindPopup(message);

    // προσθήκη layerMarkers array
    layerMarkers.layer4.push(marker);
  }
  updateClusterGroup();
}

// συνάρτηση για την αρχικοποίηση markers για αιτήματα on the way
function on_the_way_requests_markers(data) {
  // Clear existing markers for the layer
  layerMarkers.layer5 = [];

  // Διατρέχει τα δεδομένα και δημιουργεί markers
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
                 
    const marker = L.marker(location, {
      icon: L.icon({
        iconUrl: 'pin1.png',
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -32]
      })
    });

    marker.bindPopup(message);

    layerMarkers.layer5.push(marker);
  }
  updateClusterGroup();
}

// συνάρτηση για αρχικοποίηση markers για τα offers
function offers_markers(data) {
  layerMarkers.layer3 = [];

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

      let message; //προσβάσιμες μεταβλητες μόνο απο τον βρόχο
      let marker;

    // Δημιουργία του μηνύματος και του marker ανάλογα με το αν υπάρχει όχημα
    if (vehicle_name == null) {
       message = '<strong>' + civilian_first_name + ' ' + civilian_last_name + ' Offers: </strong><br>' + '<strong>From ' + 
                  offer_category + '</strong>: ' + offer_product_name + '<br><strong>For</strong>: ' +
                  offer_quantity + ' persons' + '<br><strong>Date posted</strong>: ' + 
                  offer_date_posted + '<br><strong>Number:+30 </strong> ' + civilian_number + '<br><strong>State:</strong> ' + offer_status ;
                
      marker = L.marker(location, {
        icon: L.icon({
          iconUrl: 'pin2.png', //pin2 marker
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

      marker = L.marker(location, {
        icon: L.icon({
          iconUrl: 'pin2.png', //pin2 marker
          iconSize: [32, 32],
          iconAnchor: [16, 32],
          popupAnchor: [0, -32]
        })
      });
    }

    marker.bindPopup(message);

    layerMarkers.layer3.push(marker);
  }

  updateClusterGroup();
}


let layerLines = [];

// συνάρτηση για ευθέιες γραμμές
function drawLinesforOffersandRequest() {
  // καθαρισμός παλιών γραμμών
  layerLines.forEach(line => map.removeLayer(line));
  layerLines = [];

  // Σχεδίαση γραμμών ανάμεσα στα ενεργά επίπεδα
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

// συνάρτηση για την διαγραφή γραμμών μεταξύ layers
function clearLinesBetweenLayers() {
  layerLines.forEach(line => map.removeLayer(line));
  layerLines = [];
}

// Λειτουργία για την ενεργοποίηση/απενεργοποίηση layer
function toggleLayer(layer) {
  if (activeLayers[layer]) {
    // Απενεργοποίηση του επιπέδου αν είναι ενεργό
    activeLayers[layer] = false;
    if (layer === 'layer6' || layer === 'layer2' || layer === 'layer3' || layer === 'layer5') {
      clearLinesBetweenLayers();
    }
  } else {
    // Ενεργοποίηση του επιπέδου αν είναι ανενεργό
    activeLayers[layer] = true;

    // Αρχικοποίηση markers για το κάθε επίπεδο (φίλτρο)
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

    // Σχεδίαση γραμμών αν το layer 6 ενεργοποιηθεί
    if (layer === 'layer6') {
      drawLinesforOffersandRequest();
    }
  }

  // Ενημέρωση του marker cluster group
  updateClusterGroup();

  // Ενεργοποίηση/απενεργοποίηση του κουμπιού toggle για το layer6 με βάση την κατάσταση των layer2 και layer3
  document.getElementById('layer6').disabled = !(activeLayers['layer2']);
}

// Function για την ενημέρωση του marker cluster group με βάση τα ενεργά επίπεδα
function updateClusterGroup() {
  // Καθαρίζει όλους τους markers από το cluster group
  allMarkersClusterGroup.clearLayers();

  // Προσθήκη markers από τα ενεργά επίπεδα
  for (const layer in activeLayers) {
    if (activeLayers[layer]) {
      allMarkersClusterGroup.addLayers(layerMarkers[layer]);
    }
  }
}
