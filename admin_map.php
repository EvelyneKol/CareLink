<?php
// Σύνδεση στη βάση δεδομένων
include 'Connection.php';

session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'admin') {
    header('Location: sign_in.php');
    exit();
}
// Έλεγχος σύνδεσης
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
function baseLocation($conn) {
    //διαβασμα συντεταγμενων αποθηκης απο την βαση δεδομεων
    $baseLocation = "SELECT SUBSTRING_INDEX(base_location, ',', 1) AS latitude,
            SUBSTRING_INDEX(base_location, ',', -1) AS longitude
            FROM base";
    
        $baseLocationData = array();
    
        $sqlbaseLocation = $conn->query($baseLocation);
    
        if ($sqlbaseLocation) {
            while ($row = $sqlbaseLocation->fetch_assoc()) {
                $baseLocationData[] = array(
                    "latitude" => $row["latitude"],
                    "longitude" => $row["longitude"]
                );
            }
    
            // Encode $baseLocationData array to JSON
            $json_data = json_encode($baseLocationData);
    
            // Specify the path to store the JSON file
            $json_file = 'baseLocation.json';
    
            // Write JSON data to file
            if (file_put_contents($json_file, $json_data)) {
                return "JSON data successfully written to $json_file";
            } else {
                return "Unable to write JSON data to $json_file";
            }
    
            // Close the result set
            $sqlbaseLocation->close();
        } else {
            die("Error executing the SQL query: " . $conn->error);
        }
    }

//query για οχηματα
    $vehicles = "SELECT 
                    vehicle.vehicle_name,
                    SUBSTRING_INDEX(vehicle.vehicle_location, ',', 1) AS latitude,
                    SUBSTRING_INDEX(vehicle.vehicle_location, ',', -1) AS longitude,
                    GROUP_CONCAT(CONCAT(vehiclesOnAction.quantity, ' ', vehiclesOnAction.products) SEPARATOR '; ') AS products,
                    SUM(COALESCE(vehiclesOnAction.quantity, 0)) AS quantity,
                    COUNT(DISTINCT task.task_id) AS task_count
                FROM 
                    vehicle
                LEFT JOIN 
                    vehiclesOnAction ON vehicle.vehicle_name = vehiclesOnAction.v_name
                LEFT JOIN 
                    task ON vehiclesOnAction.driver = task.task_volunteer
                GROUP BY 
                    vehicle.vehicle_name";

    $data2 = array();
    $sqlVehicle = $conn->query($vehicles);
    
    if ($sqlVehicle) {
        // αρχικοποίηση πίνακα για αποθήκευση δεδομένων
        $data2 = array();
    
        while ($row = $sqlVehicle->fetch_assoc()) {        
            // αντιστοίχηση $data2 πίνακα με τα δεδομένα query
            $data2[] = array(
                "vehicle_name" => $row["vehicle_name"],
                "quantity" => $row["quantity"],
                "products" => $row["products"],
                "task_count" => $row["task_count"],
                "latitude" => $row["latitude"], 
                "longitude" => $row["longitude"]
            );
        }
       
        // αποκωδικοποίηση $data3 σε JSON 
        $json_data = json_encode($data2);
    
        // όνομα JSON file για αποθήκευση
        $json_file = 'vehicles.json';
    
        // Write JSON data to file
        if (file_put_contents($json_file, $json_data)) {
           
        } else {
            echo "Unable to write JSON data to $json_file";
        }
        
        // Close the result set
        $sqlVehicle->close();
    } else {
        die("Error executing the SQL query: " . $conn->error);
    }

    



// Αιτήματα που περιμένουν
$waitingRequest = "SELECT 
            civilian.civilian_first_name,
            civilian.civilian_last_name,
            civilian.civilian_number,
            SUBSTRING_INDEX(civilian.civilian_location, ',', 1) AS latitude,
            SUBSTRING_INDEX(civilian.civilian_location, ',', -1) AS longitude,
            request.request_date_posted,
            request.request_category,
            request.state,
            request.request_product_name,
            request.persons
            FROM 
            request
            JOIN 
            civilian ON request.request_civilian = civilian.civilian_username
            LEFT JOIN 
            task ON request.id_request = task.task_offer_id
            LEFT JOIN 
            vehiclesOnAction ON task.task_volunteer = vehiclesOnAction.driver
            LEFT JOIN 
            vehicle ON vehiclesOnAction.v_name = vehicle.vehicle_name
            WHERE  request.state = 'WAITING'";

 // αρχικοποίηση πίνακα για αποθήκευση δεδομένων
$waitingRequestdata = array();
$sqlwaitingRequest = $conn->query($waitingRequest);

if ($sqlwaitingRequest) {
     // αντιστοίχηση $data2 πίνακα με τα δεδομένα query
    while ($row = $sqlwaitingRequest->fetch_assoc()) {
        $waitingRequestdata[] = array(
            "civilian_first_name" => $row["civilian_first_name"],
            "civilian_last_name" => $row["civilian_last_name"],
            "civilian_number" => $row["civilian_number"],
            "request_date_posted" => $row["request_date_posted"],
            "request_category" => $row["request_category"],
            "state" => $row["state"],
            "request_product_name" => $row["request_product_name"], 
            "persons" => $row["persons"],
            "latitude" => $row["latitude"], 
            "longitude" => $row["longitude"]
        );
    }

// αποκωδικοποίηση $waitingRequestdata σε JSON
$json_data = json_encode($waitingRequestdata);

// όνομα JSON file για αποθήκευση
$json_file = 'waitingRequests.json';

// γράφει το JSON αρχείο με τα δεδομένα
file_put_contents($json_file, $json_data);

$sqlwaitingRequest->close();
} else {
return "Error executing the SQL query: " . $conn->error;
}

// query για select offers
$offers = "SELECT distinct
            civilian.civilian_first_name,
            civilian.civilian_last_name,
            civilian.civilian_number,
            SUBSTRING_INDEX(civilian.civilian_location, ',', 1) AS latitude,
            SUBSTRING_INDEX(civilian.civilian_location, ',', -1) AS longitude,
            offer.offer_date_posted,
            offer.offer_category,
            offer.offer_status,
            offer.offer_product_name,
            offer.offer_quantity,
            task.task_date,
            vehicle.vehicle_name
            FROM 
            offer
            JOIN 
            civilian ON offer.offer_civilian = civilian.civilian_username
            LEFT JOIN 
            task ON offer.offer_id = task.task_offer_id
            LEFT JOIN 
            vehiclesOnAction ON task.task_volunteer = vehiclesOnAction.driver
            LEFT JOIN 
            vehicle ON vehiclesOnAction.v_name = vehicle.vehicle_name
            WHERE 
            offer.offer_status NOT IN ('CANCELED', 'COMPLETED')";

 // αρχικοποίηση πίνακα για αποθήκευση δεδομένων
$data = array();
//αποθηκευση αποτελεσμάτων
$sqloffers = $conn->query($offers);

if ($sqloffers) {
    while ($row = $sqloffers->fetch_assoc()) {
         // αντιστοίχηση $data2 πίνακα με τα δεδομένα query
        $data[] = array(
            "civilian_first_name" => $row["civilian_first_name"],
            "civilian_last_name" => $row["civilian_last_name"],
            "civilian_number" => $row["civilian_number"],
            "offer_date_posted" => $row["offer_date_posted"],
            "offer_category" => $row["offer_category"],
            "offer_status" => $row["offer_status"],
            "offer_product_name" => $row["offer_product_name"],
            "offer_quantity" => $row["offer_quantity"], 
            "vehicle_name" => $row["vehicle_name"],
            "task_date" => $row["task_date"],
            "latitude" => $row["latitude"], 
            "longitude" => $row["longitude"]
        );
    }

    // αποκωδικοποίηση $data3 array σε JSON
    $json_data = json_encode($data);

    // όνομα JSON file για αποθήκευση
    $json_file = 'Offers.json';

    // γράφει το JSON αρχείο με τα δεδομένα
    if (file_put_contents($json_file, $json_data)) {
    
    } else {
        echo "Unable to write JSON data to $json_file";
    }
    
    // κλείσιμο του result set
    $sqloffers->close();
} else {
    die("Error executing the SQL query: " . $conn->error);
}



//on the way requests
$onTheWayRequests = "SELECT distinct
                civilian.civilian_first_name,
                civilian.civilian_last_name,
                civilian.civilian_number,
                SUBSTRING_INDEX(civilian.civilian_location, ',', 1) AS latitude,
                SUBSTRING_INDEX(civilian.civilian_location, ',', -1) AS longitude,
                request.request_date_posted,
                request.request_category,
                request.state,
                request.request_product_name,
                request.persons,
                task.task_date,
	            vehicle.vehicle_name
                FROM 
                request
                JOIN 
                civilian ON request.request_civilian = civilian.civilian_username
                LEFT JOIN 
                task ON request.id_request = task.task_request_id
                LEFT JOIN 
                vehiclesOnAction ON task.task_volunteer = vehiclesOnAction.driver
                LEFT JOIN 
                vehicle ON vehiclesOnAction.v_name = vehicle.vehicle_name
                WHERE  request.state = 'ON THE WAY'";

 // αρχικοποίηση πίνακα για αποθήκευση δεδομένων
$onTheWayRequestData = array();

$sqlOnTheWayRequest = $conn->query($onTheWayRequests);

if ($sqlOnTheWayRequest) {
    while ($row = $sqlOnTheWayRequest->fetch_assoc()) {
        // αντιστοίχηση $data2 πίνακα με τα δεδομένα query
        $onTheWayRequestData[] = array(
            "civilian_first_name" => $row["civilian_first_name"],
            "civilian_last_name" => $row["civilian_last_name"],
            "civilian_number" => $row["civilian_number"],
            "request_date_posted" => $row["request_date_posted"],
            "request_category" => $row["request_category"],
            "state" => $row["state"],
            "request_product_name" => $row["request_product_name"],
            "persons" => $row["persons"], 
            "vehicle_name" => $row["vehicle_name"],
            "task_date" => $row["task_date"],
            "latitude" => $row["latitude"], 
            "longitude" => $row["longitude"]
            
        );
    }
    
    // αποκωδικοποίηση $data1 πίνακα σε JSON
    $json_data = json_encode($onTheWayRequestData);

    // όνομα JSON file για αποθήκευση
    $json_file = 'OnWayRequests.json';

    // γράφει το JSON αρχείο με τα δεδομένα
    if (file_put_contents($json_file, $json_data)) {
    } else {
        return "Unable to write JSON data to $json_file";
    }
    // κλείσιμο σύνδεσης για αποτελέσματα
    $sqlOnTheWayRequest->close();
} else {
    die("Error executing the SQL query: " . $conn->error);
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
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />
    <script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@latest/dist/MarkerCluster.Default.css" />
    <script src="https://unpkg.com/leaflet.markercluster@latest/dist/leaflet.markercluster.js"></script>
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <!-- Leaflet Routing Machine CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css" />
    <!-- Leaflet Routing Machine JavaScript -->
    <script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.3.0/dist/MarkerCluster.Default.css" />
    <script src="https://unpkg.com/leaflet.markercluster@1.3.0/dist/leaflet.markercluster.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

</head>
<body>
   <!-- nav bar -->
    <div class="navbar">
        <img src="images/logo1.png" alt="Logo" >
        <ul class="nav">
            <li><a href="admin.php">Home</a></li>
            <p style="font-size: 18px;"> | </p >
            <li><a class="active" href="admin_map.html">Map</a></li>
            <p style="font-size: 18px;"> | </p >
            <li><a href="base.php">Database </a></li>
        </ul>
        <ul class="nav">
            <li><a href="logout.php"><i class="fa fa-sign-out" style="font-size:24px" ></i> Log out</a></li>
        </li>
        </ul>
    </div>


    <!-- χάρτης με φίλτρα σε μορφή checkbox -->
    <div class="Firstsection">
        <h2> Map </h2>
        <br>
        <form class="filters">
            <input type="checkbox" id="layer1" name="mapLayer" onchange="toggleLayer('layer1')">
            <label for="layer1">Vehicles Waiting</label>

            <input type="checkbox" id="layer2" name="mapLayer" onchange="toggleLayer('layer2')">
            <label for="layer2">Vehicles On The Way</label>

            <input type="checkbox" id="layer3" name="mapLayer" onchange="toggleLayer('layer3')">
            <label for="layer3">Offers</label>

            <input type="checkbox" id="layer4" name="mapLayer" onchange="toggleLayer('layer4')">
            <label for="layer4">Waiting Requests</label>

            <input type="checkbox" id="layer5" name="mapLayer" onchange="toggleLayer('layer5')">
            <label for="layer5">On their way Requests</label>

            <input type="checkbox" id="layer6" name="mapLayer" onchange="toggleLayer('layer6')" disabled>
            <label for="layer6">Lines</label>

        </form>
        <div id='map'></div>
    </div>
    
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
              <li>Email: carelink@gmail.com</li>
              <br>
              <li>Phone: +30 123456789</li>
            </ul>
    
          </div>
        </div>
    
    </div>

    <script src="admin_map.js"></script>

</body>
</html>