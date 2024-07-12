<?php
// Σύνδεση στη βάση δεδομένων
$servername = "localhost";
$username = "evelina";
$password = "Evel1084599!";
$dbname = "carelink";

$conn = new mysqli($servername, $username, $password, $dbname);

// Έλεγχος σύνδεσης
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Επιλογή των συντεταγμένων της βάσης από τη βάση δεδομένων
$base = "SELECT base_location FROM base WHERE base_id = 1"; // Υποθέτουμε ότι η βάση έχει μόνο ένα στοιχείο με αναγνωριστικό 1
$location = $conn->query($base);

if ($location->num_rows > 0) {
    $row = $location->fetch_assoc();
    $base_location = $row["base_location"];
} else {
    echo "Base location not found";
}


$sql = "SELECT vehicle_location FROM vehicle";
$result = $conn->query($sql);

$locations = array();
if ($result->num_rows > 0) {
    // Fetch locations from the database
    while ($row = $result->fetch_assoc()) {
        $locations[] = $row['vehicle_location'];
    }
}

// Κλείσιμο σύνδεσης με τη βάση δεδομένων
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>CareLink Map</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="admin_map.css">
    <link rel="icon" type="image/jpg" sizes="96x96" href="images/favicon.png">
    <link rel="stylesheet" href="leaflet-routing-machine.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.2.0/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />
    <script src="https://unpkg.com/leaflet@1.2.0/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
</head>


<body>
    <div class="header">
        <h2><img src="images/logo.png" alt="Logo" width="200"></h2>
        <div class="h3-header">
            <h3>Back to tracking!</h3> 
          </div>
    </div>

    <div class="navbar">
        <ul class="nav">
            <li><a href="admin.php">Home</a></li>
            <li><a class="active" href="admin_map.html">Map</a></li>
            <li><a href="base.php">Database </a></li>
        </ul>
        <ul class="nav">            
            <li><a href="#"><i class="fa fa-sign-out" style="font-size:24px" ></i> Log out</a></li>
        </ul>
    </div>


    <div class="Firstsection">
        <h2> Maps </h2>
        <div class="filter">
          <input type="checkbox" id="layer1" class="filter-btn" onchange="toggleLayer('layer1')" checked>
          <label for="layer1">Tasks undertaken</label>
      
          <input type="checkbox" id="layer2" class="filter-btn" onchange="toggleLayer('layer2')" checked>
          <label for="layer2">On the way tasks</label>

          <input type="checkbox" id="layer2" class="filter-btn" onchange="toggleLayer('layer2')" checked>
          <label for="layer2">Offers</label>
          
          <input type="checkbox" id="layer2" class="filter-btn" onchange="toggleLayer('layer2')" checked>
          <label for="layer2">Vehicles</label>

          <input type="checkbox" id="layer2" class="filter-btn" onchange="toggleLayer('layer2')" checked>
          <label for="layer2">Vehicles with active tasks</label>

          <input type="checkbox" id="layer2" class="filter-btn" onchange="toggleLayer('layer2')" checked>
          <label for="layer2">Lines</label>

      </div>
      
   </div>
    

    <div id='map' class="map-container"></div>

    <div class="Footer container-fluid">
        <div class="row">
          <div class="column col-sm-3">
            <h2>Social Media</h2>
            <nav class="nav02">
              <a href="https://www.linkedin.com/in/george-karagiannis-00a683222/" class="fa fa-linkedin"></a>
              <a href="https://www.facebook.com/george.karagiannis.9406" class="fa fa-facebook"></a>
              <a href="https://www.instagram.com/_karagiannis_g/" class="fa fa-instagram "></a>
            </nav>
          </div>
          <div class="column col-sm-6">
            <h2>Licences</h2>
            <br>
            <p>© George Karagiannis/Ceid/Upatras/Year 2023-2024</p>
            <p>© Evelina Kolagki/Ceid/Upatras/Year 2023-2024</p>
    
          </div>
          <div class="column col-sm-3">
            <h2>Contact info</h2>
            <br>
            <ul>
              <li>Email: Karagiannis.giorg@gmail.com</li>
              <br>
              <li>Phone: +30 123456789</li>
            </ul>
    
          </div>
        </div>
    
    </div>

    <script>
    // Initialize the map
    let map = L.map('map').setView([38.2904558214517, 21.79578903224108], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
    }).addTo(map);

    // Define the icon
    let customIcon = L.icon({
        iconUrl: 'images/base.png',
        iconSize: [32, 32], // Size of the icon
        iconAnchor: [16, 32], // Point of the icon which will correspond to marker's location
        popupAnchor: [0, -32] // Point from which the popup should open relative to the iconAnchor
    });

    // Initialize the base marker as draggable with initial coordinates from PHP
    let base = L.marker([<?php echo $base_location; ?>], {
        draggable: true,
        icon: customIcon // Set the custom icon
    }).addTo(map);

    // Add a popup message to the base marker
    base.bindPopup("Address: 25th March, Patras Greece<br>Postcode: 265 04<br>Phone: +30 2610 529 090<br>Email: carelink@gmail.com'").openPopup();

    // Function to handle dragend event of the marker
    base.on('dragend', function(event){
        let marker = event.target;
        let position = marker.getLatLng(); // Get the new coordinates
        let lat = position.lat;
        let lng = position.lng;

        // Send an AJAX request to update the base location in the database
        $.ajax({
            url: 'update_base_location.php', // Αντικαταστήστε αυτό με τη σωστή διαδρομή προς τον PHP επεξεργαστή
            method: 'POST',
            data: {
                lat: lat,
                lng: lng
            },
            success: function(response) {
                console.log(response);
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    });
    // Define an icon for the truck
    var vehicle = L.icon({
            iconUrl: 'images/Truck_map.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
            shadowSize: [41, 41]
        });

    // Loop through the fetched locations and create markers dynamically
    <?php
    foreach ($locations as $location) {
        // Split the location string to get latitude and longitude
        list($lat, $lng) = explode(',', $location);
    ?>
        // Create marker for each location with red icon
        L.marker([<?php echo $lat; ?>, <?php echo $lng; ?>], { icon: vehicle }).addTo(map);
    <?php
    }
    ?>

var waitingRequestsLayer;
var waitingOffersLayer;

function toggleLayer(layerId) {
    // Get the checkbox element by its ID
    var checkbox = document.getElementById(layerId);

    // Check if the checkbox is checked
    if (checkbox.checked) {
        // If checked, add the layer to the map
        switch (layerId) {
            case 'layer1':
                // Add layer 1 to the map
                break;
            case 'layer2':
                // Add layer 2 to the map
                break;
            // Add more cases as needed for other layers
        }
    } else {
        // If not checked, remove the layer from the map
        switch (layerId) {
            case 'layer1':
                // Remove layer 1 from the map
                break;
            case 'layer2':
                // Remove layer 2 from the map
                break;
            // Add more cases as needed for other layers
        }
    }
}



</script>
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>


  
</body>

</html>