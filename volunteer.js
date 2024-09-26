// Συνάρτηση για την εμφάνιση της φόρμας load
function loadItems(formId) {
  var form = document.getElementById(formId);
  form.style.display = 'block';
  }

// Συνάρτηση για την εμφάνιση της φόρμας unload
function unloadItems(formId) {
  var form = document.getElementById(formId);
  form.style.display = 'block';
  }

//Δημιουργεία χάρτη
var map = L.map('map');
L.tileLayer('https://api.maptiler.com/maps/basic/256/{z}/{x}/{y}.png?key=dVhthbXQs3EHCi0XzzkL', {
  attribution: '<a href="https://www.maptiler.com/copyright/" target="_blank">&copy; MapTiler</a> <a href="https://www.openstreetmap.org/copyright" target="_blank">&copy; OpenStreetMap contributors</a>'
}).addTo(map);

var userLocationMarker;
var baseMarker;
var line = L.polyline([], { color: 'black' }).addTo(map);
var RequestMarkers = [];
var RequestLines = [];

var OfferMarkers = [];
var OfferLines = [];


// παίρνουμες  τισ συντεταγμενες και δημιουργουμε μεταβλητές
$.ajax({
    url: 'get_location.php',// URL που περιέχει τις συντεταγμενεσ τησ βάσης 
    type: 'GET',
    dataType: 'json',// Μορφή των δεδομένων που αναμένονται από τον διακομιστή
    success: function(response) {
        if (response.error) {
            console.error(response.error);
            return;
        }

         // Ανάκτηση και μετατροπή των συντεταγμένων από την απόκριση
        var Lat = parseFloat(response.latitude);
        var Lng = parseFloat(response.longitude);

        // Κλήση συνάρτησης για την αρχικοποίηση του marker της βάσης
        initializeBaseMarker(Lat, Lng);
        // Ενημέρωση της γραμμής
        updateLine();
    },
    error: function(error) { // Συνάρτηση που καλείται όταν προκύπτει σφάλμα κατά την αίτηση
        console.error(error);
    }
});

document.addEventListener('DOMContentLoaded', function() {
  if ('geolocation' in navigator) {
   // Λήψη της τρέχουσας θέσης του χρήστη
  navigator.geolocation.getCurrentPosition(function (position) {
      var userLat = position.coords.latitude;
      var userLng = position.coords.longitude;
      var geolocation = userLat + ", " + userLng;

      map.setView([userLat, userLng], 13);

      initializeUserLocationMarker(userLat, userLng);

       // Υπολογισμός της απόστασης μεταξύ της θέσης του χρήστη και της αρχικής θέσης του baseMarker
      var initialDistance = calculateDistance(userLocationMarker.getLatLng(), baseMarker.getLatLng());
       // Εμφάνιση της απόστασης
      userLocationMarker.bindPopup(`Your Location - Distance: ${initialDistance.toFixed(2)} kilometers`).openPopup();
      document.getElementById("loadAddress").value = geolocation;
  });
} else {
  console.log('Geolocation is not supported by your browser.');
}
});



function initializeBaseMarker(Lat, Lng) {
  // Δημιουργία του εικονιδίου για τον δείκτη βάσης
  var baseIcon = L.icon({
      iconUrl: 'images/base.png',
      iconSize: [41, 41],
      iconAnchor: [20, 41],
      popupAnchor: [1, -34]
  });

  // Δημιουργία του δείκτη βάσης με τις συντεταγμένες και το εικονίδιο που καθορίστηκε
  baseMarker = L.marker([Lat, Lng]).addTo(map).setIcon(baseIcon);
  baseMarker.bindPopup('Base <br>Phone: +30 2610 529 090<br>Email: carelink@gmail.com').openPopup();

  // Δημιουργία ενός κύκλου γύρω από τη θέση του δείκτη βάσης
  var circle = L.circle([Lat, Lng], {
      color: 'blue',
      fillColor: 'blue',
      fillOpacity: 0.5,
      radius: 50
  }).addTo(map);

}

function initializeUserLocationMarker(userLat, userLng) {
  // Δημιουργία εικονιδίου για τον δείκτη της θέσης του χρήστη
  var myTruck = L.icon({
    iconUrl: 'truck.png',
    iconSize: [41, 41],
    iconAnchor: [20, 41],
    popupAnchor: [1, -34]
  });

  // Δημιουργία του δείκτη θέσης του χρήστη με τις συντεταγμένες και το εικονίδιο που καθορίστηκε
  userLocationMarker = L.marker([userLat, userLng], { draggable: true }).addTo(map).setIcon(myTruck);
  userLocationMarker.bindPopup('Your Location').openPopup();

  userLocationMarker.on('dragend', function (event) {
    // Ανάκτηση της νέας θέσης του δείκτη μετά τη μετακίνηση
    var position = userLocationMarker.getLatLng();
    userLocationMarker.setLatLng(position);
    userLocationMarker.getPopup().setContent('Your Location, new Position: ' + position.toString()).update();

    // Υπολογισμός της απόστασης μεταξύ του δείκτη θέσης του χρήστη και του βασικού δείκτη
    var distance = calculateDistance(position, baseMarker.getLatLng());
    userLocationMarker.setPopupContent(`Your Location - Distance: ${distance.toFixed(2)} kilometers`).update();

    // Ενημέρωση      
    updateLine();
    updateRequestLines();
    updateOfferLines();
    checkDistancesforRequests();
    checkDistancesforOffers();
  });

}

  
function updateLine() {
    // Υπολογισμός της απόστασης μεταξύ του δείκτη βάσης και του δείκτη θέσης του χρήστη
  var distance = calculateDistance(baseMarker.getLatLng(), userLocationMarker.getLatLng());

  // Ανάκτηση των στοιχείων των κουμπιών
  var loadItemsButton = document.getElementById('yourButtonId1');
  var unloadItemsButton = document.getElementById('yourButtonId2');

  // Ενεργοποίηση ή απενεργοποίηση των κουμπιών με βάση την απόσταση
  if (distance < 50) {
    loadItemsButton.disabled = false;
    unloadItemsButton.disabled = false;
  } else {
    loadItemsButton.disabled = true;
    unloadItemsButton.disabled = true;
  }

  // Ενημέρωση της γραμμής που συνδέει τον δείκτη βάσης με τον δείκτη θέσης του χρήστη
  line.setLatLngs([baseMarker.getLatLng(), userLocationMarker.getLatLng()]);
}

function calculateDistance(pointA, pointB) {
  return pointA.distanceTo(pointB);
}

var volWaitingRequests = [];
var volWaitingOffers = [];
var myRequests = [];
var myOffers = [];


// Ανάκτηση δεδομένων απο τα αρχεία json
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

//Ενεργά επίπεδα (layers)
// Δημιουργία ενός αντικειμένου για την αποθήκευση ενεργών επιπέδων (layers)
var activeLayers = {};

// Δημιουργία ενός global marker cluster group για την ομαδοποίηση των σημείων
var allMarkersClusterGroup = L.markerClusterGroup();
map.addLayer(allMarkersClusterGroup);

// Δημιουργία ενός αντικειμένου για την αποθήκευση σημείων ανά επίπεδο
var layerMarkers = {
  layer1: [],
  layer2: [],
  layer3: [],
  layer4: [],
  layer5: []
};

// Markers για τα Request & offers
function Waiting_requests_markers(data) {
  // καθαρισμός layer
  layerMarkers.layer1 = [];

  // Loop στα data για δημιουργια markers
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

      if (taskCount < 4) { //αναλαμβάνει μέχρι 4 τασκ 
        requestbutton = '<button class="Acceptbut" onclick="handle_requests(' + id_request + ')">Accept</button>';
      } else {
        requestbutton = '<strong style="display: block; text-align: center; color: red;">You have reached the maximum number of Tasks</strong>';
      } 

       //μήνυμα ποπ απ 
      const message = '<strong>' + civilian_first_name + ' ' + civilian_last_name +
          ' requests: </strong><br>' + '<strong> ' +
          request_category + '</strong>: ' + request_product_name + '<br><strong>For</strong>: ' +
          persons + ' persons' + '<br><strong>Date posted</strong>: ' +
          request_date_posted + '<br><strong>Number: </strong> ' + '+30' + civilian_number +
          '<br><strong>State:</strong> ' + state + '<br>' + requestbutton;

      // Δημιουργία marker 
      const marker = L.marker(location, {
          icon: L.icon({
              iconUrl: 'pin1.png',
              iconSize: [32, 32],
              iconAnchor: [16, 32],
              popupAnchor: [0, -32]
          })
      });

      // ένωση μαρκερ με pop up 
      marker.bindPopup(message);

      // προσθήκη marker στο layer 1
      layerMarkers.layer1.push(marker);
  }
  // ανανέωση του group των markers 
  updateClusterGroup();
}


//____________________________offers_______________________________
function offers_markers(data) {
  layerMarkers.layer2 = [];

  // Loop στα data για δημιουργια markers
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

      //ελεγχος για να μην μπορεί ένας εθελοντήσ να εχει πανω απο 4 tasks
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
                 
    // marker προσφοράς
    const marker = L.marker(location, {
      icon: L.icon({
        iconUrl: 'pin2.png',
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -32]
      })
    });

    
    // Bind popup με marker
    marker.bindPopup(message);

    // δέικτησ με φίλτρο
    layerMarkers.layer2.push(marker);
  }

  // Update marker cluster group
  updateClusterGroup();
} 

//___________________________________myRequests___________________________________
let RequestCircles = []; // Array για αποθήκευση του κύκλου γύρω απο το αίτημα

function my_requests(data) {
  layerMarkers.layer3 = [];
  RequestCircles = []; 

  // Loop στα data και δημιουργία markers
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

    const marker = L.marker(location, {
      icon: L.icon({
        iconUrl: 'pin1.png',
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -32]
      }),
      id_request: id_request
    });

    // Bind popup με marker αιτήματος
    marker.bindPopup(message);

    // δημιουργία κύκλου
    const circle = L.circle(location, {
      color: 'blue',
      fillColor: 'blue',
      fillOpacity: 0.5,
      radius: 50 // μέτρα κύκλου
    });

    // προσθήκη στον χάρτη
    marker.addTo(map);
    circle.addTo(map);

    // αποθήκευση του marker και του circle
    layerMarkers.layer3.push(marker);
    RequestMarkers.push(marker); 
    RequestCircles.push(circle); 
  }

  // ενημέρωση του marker cluster group
  updateClusterGroup();

}

function checkDistancesforRequests() {
  if (!userLocationMarker) return; //έλεγχος της τοποθεσίας χρήστη

  RequestMarkers.forEach(marker => {
    var distance = calculateDistance(marker.getLatLng(), userLocationMarker.getLatLng());
    // έλεγχος αν η αποσταση ειναι <50 μέτρα
    if (distance < 50) {
      const id_request = marker.options.id_request; // Επιβεβαίωση ότι το id_request είναι συνδεδεμένο με τον marker
    
    // Επιλογή του κουμπιού που έχει το σωστό id_request
     const button = document.querySelector('button.DeliverReq[data-id_request="${id_request}"]');

      if (button) {
        // Ενεργοποίηση του κουμπιού
        button.disabled = false;
      }
    } else {
      const id_request = marker.options.id_request; // Επιβεβαίωση ότι το id_request είναι συνδεδεμένο με τον marker
    
      // Επιλογή του κουμπιού που έχει το σωστό id_request
      const button = document.querySelector('button.DeliverReq[data-id_request="${id_request}"]');
                                            
      if (button) {
        // Ενεργοποίηση του κουμπιού
        button.disabled = true;
      }
    }
  });
}

function drawRequestLines() {
  if (!userLocationMarker || !activeLayers.layer3) return; // ελεχγος αν υπαρχει το userLocationMarker και το layer3 ειναι πατημενο

  RequestMarkers.forEach(marker => {
    var line = L.polyline([marker.getLatLng(), userLocationMarker.getLatLng()], { color: 'red' }).addTo(map);
    RequestLines.push(line);
  });
}

function updateRequestLines() {
  if (!userLocationMarker || !activeLayers.layer3) return; //  ελεχγος αν υπαρχει το userLocationMarker και το layer3 ειναι πατημενο

  RequestLines.forEach(line => map.removeLayer(line)); // αφαίρεση γραμμων
  RequestLines = []; // άδειος πίνακας γραμμων

  // κλήση συνάρτησης για εμφάνιση γραμμων
  drawRequestLines();
}


//_______________________________my_offers__________________________________
var OfferCircles = []; //μεταβλητη για τον κύκλο του offer 
function my_offers(data) {
  layerMarkers.layer4 = [];
  OfferCircles = []; 
 

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

    const marker = L.marker(location, {
      icon: L.icon({
        iconUrl: 'pin2.png',
        iconSize: [32, 32],
        iconAnchor: [16, 32],
        popupAnchor: [0, -32]
      })
    });

    marker.bindPopup(message);

    // κύκλος προσφορών
    const circle = L.circle(location, {
      color: 'blue',
      fillColor: 'blue',
      fillOpacity: 0.5,
      radius: 50 // κύκλος σε μετρα
    });

    marker.addTo(map);
    circle.addTo(map);

    // προσθήκη marker ατο layerMarkers array
    layerMarkers.layer4.push(marker);
    OfferMarkers.push(marker); // προσθηκη στο OfferMarkers array
    OfferCircles.push(circle); // προσθηκη στο RequestCircles array
  }

  updateClusterGroup(); //ανανέωση group markers 
}

function checkDistancesforOffers() {
  if (!userLocationMarker) return; //έλεγχος της τοποθεσίας χρήστη

  OfferMarkers.forEach(marker => {
    var distance = calculateDistance(marker.getLatLng(), userLocationMarker.getLatLng());
    
    // έλεγχος αν η αποσταση ειναι <50 μέτρα
    if (distance < 50) {
      //enable το κουμπί για φόρτωση       
      document.querySelectorAll('button.AcceptOffer').forEach(button => {
        button.disabled = false;
      });
    } else {
      // Disable το κουμπί αν η αποσταση ειναι μεγαλυτερη απο 50 μετρα
      document.querySelectorAll('button.AcceptOffer').forEach(button => {
        button.disabled = true;
      });
    }
  });
} 

function drawOfferLines() {
  if (!userLocationMarker || !activeLayers.layer4) return; 

  OfferMarkers.forEach(marker => {
    var line = L.polyline([marker.getLatLng(), userLocationMarker.getLatLng()], { color: 'green' }).addTo(map);
    OfferLines.push(line);
  });
}

function updateOfferLines() {
  if (!userLocationMarker || !activeLayers.layer4) return; 

  OfferLines.forEach(line => map.removeLayer(line)); // αφαιρεση γραμμων απο τον χαρτη
  OfferLines = []; // κενος πίνακας γραμμων 

  // σχεδιασμός γραμμων 
  drawOfferLines();
}

//_________________ Συνάρτηση για toggle layers_________________________________
function toggleLayer(layer) {
  if (activeLayers[layer]) {// Ελέγχει αν το συγκεκριμένο layer είναι ήδη ενεργοποιημένο
    //άρα πατήθηκε και θέλω να κάνω toggle την κατάσταση του, δηλαδή να το απενεργοποιήσω 

    // απανεργοποίηση layer, αν το layer είναι ενεργό
    activeLayers[layer] = false;

    if (layer === 'layer3') {
      RequestLines.forEach(line => map.removeLayer(line)); // αφαίρεση όλων των request lines απο τον χάρτη
      RequestLines = []; // "καθαρισμός" πίνακα
      RequestCircles.forEach(circle => map.removeLayer(circle)); // αφαίρεση όλων των κυκλων απο τον χάρτη
      RequestCircles = []; // "καθαρισμός" πίνακα
    }

    // αφαίρεση γραμμων αν το layer4 είναι απενεργοπποιημενο  
    if (layer === 'layer4') {
      OfferLines.forEach(line => map.removeLayer(line)); //  αφαίρεση όλων των offer lines απο τον χάρτη
      OfferLines = []; // "καθαρισμός" πίνακα
      OfferCircles.forEach(circle => map.removeLayer(circle)); //  αφαίρεση όλων των κυκλων απο τον χάρτη
      OfferCircles = []; // "καθαρισμός" πίνακα
    }

  } else {
    // το φίλτρο είναι απενεργοποιημένο και θέλω να ενεργοποιηθεί το layer 
    activeLayers[layer] = true;  

    // φιλτρα
    if (layer === 'layer1') {
      Waiting_requests_markers(volWaitingRequests);

    } else if (layer === 'layer2') {
      offers_markers(volWaitingOffers);

    } else if (layer === 'layer3') { 
      my_requests(myRequests);
      drawRequestLines();

    } else if (layer === 'layer4') { 
      my_offers(myOffers);
      drawOfferLines();

    }
  }
  // ενημερωση marker cluster group
  updateClusterGroup();
}


// συνάρτηση για ενημερωση marker cluster group σύμφωνα με το φίλτρο που εχει επιλεγεί 
function updateClusterGroup() {
  // καθαρισμος όλων των markers απο το cluster group
  allMarkersClusterGroup.clearLayers();

  // Προσθήση markers στα ενεργά layers
  for (const layer in activeLayers) {
    if (activeLayers[layer]) {
      allMarkersClusterGroup.addLayers(layerMarkers[layer]);
    }
  }
}