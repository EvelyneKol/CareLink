<?php
// Σύνδεση στη βάση δεδομένων
$servername = "localhost";
$username = "evelina";
$password = "Evel1084599!";
$dbname = "carelink";

$conn = new mysqli($servername, $username, $password, $dbname);

session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'admin') {
    header('Location: sign_in.php');
    exit();
}
// Έλεγχος σύνδεσης
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//οχήματα που περιμένουν 
function waitingVehicles($conn) {
    // The query to select vehicle names from vehicle table that do not exist in vehiclesOnAction table
    $request = "SELECT v.vehicle_name,
                       SUBSTRING_INDEX(v.vehicle_location, ',', 1) AS latitude,
                       SUBSTRING_INDEX(v.vehicle_location, ',', -1) AS longitude
                FROM vehicle v
                LEFT JOIN vehiclesOnAction va ON v.vehicle_name = va.v_name
                WHERE va.v_name IS NULL";

    $data1 = array();
    $sqlrequest = $conn->query($request);

    if ($sqlrequest) {
        while ($row = $sqlrequest->fetch_assoc()) {
            $data1[] = array(
                "vehicle_name" => $row["vehicle_name"],
                "latitude" => $row["latitude"],
                "longitude" => $row["longitude"]
            );
        }

        // Encode $data1 array to JSON
        $json_data = json_encode($data1);

        // Specify the path to store the JSON file
        $json_file = 'waiting_vehicles.json';

        // Write JSON data to file
        if (file_put_contents($json_file, $json_data)) {
            return "JSON data successfully written to $json_file";
        } else {
            return "Unable to write JSON data to $json_file";
        }

        $sqlrequest->close();
    } else {
        return "Error executing the SQL query: " . $conn->error;
    }
}


// ελεγξε αν τα request ανενεωθηκαν στο JSON file
if (isset($_GET['update_json1'])) {
    echo waitingVehicles($conn);
    exit();
} else {
    waitingVehicles($conn);
}



// Αιτήματα
function fetchWaitingRequests($conn) {
    $request = "SELECT request.*, civilian.civilian_number, civilian.civilian_first_name, civilian.civilian_last_name,
                    SUBSTRING_INDEX(civilian.civilian_location, ',', 1) AS latitude,
                    SUBSTRING_INDEX(civilian.civilian_location, ',', -1) AS longitude
                    FROM request 
                    JOIN civilian ON request.request_civilian = civilian.civilian_username 
                    WHERE request.state='WAITING'";

        $data1 = array();
        $sqlrequest = $conn->query($request);

        if ($sqlrequest) {
            while ($row = $sqlrequest->fetch_assoc()) {
                $data1[] = array(
                    "id_request" => $row["id_request"],
                    "request_civilian" => $row["request_civilian"],
                    "request_category" => $row["request_category"],
                    "request_product_name" => $row["request_product_name"],
                    "persons" => $row["persons"],
                    "request_date_posted" => $row["request_date_posted"],
                    "request_time_posted" => $row["request_time_posted"], 
                    "state" => $row["state"],
                    "number" => $row["civilian_number"],
                    "first_name" => $row["civilian_first_name"],
                    "last_name" => $row["civilian_last_name"],
                    "latitude" => $row["latitude"], 
                    "longitude" => $row["longitude"]
                );
            }

        // Encode $data4 array to JSON
        $json_data = json_encode($data1);

        // Specify the path to store the JSON file
        $json_file = 'waitingRequests.json';

        // Write JSON data to file
        if (file_put_contents($json_file, $json_data)) {
            return "JSON data successfully written to $json_file";
        } else {
            return "Unable to write JSON data to $json_file";
        }

        $sqlrequest->close();
    } else {
        return "Error executing the SQL query: " . $conn->error;
    }
}

// ελεγξε αν τα request ανενεωθηκαν στο JSON file
if (isset($_GET['update_json5'])) {
    echo fetchWaitingRequests($conn);
    exit();
} else {
    fetchWaitingRequests($conn);
}

$offers = "SELECT 
        civilian.civilian_username,
        civilian.civilian_number,
        SUBSTRING_INDEX(civilian.civilian_location, ',', 1) AS latitude,
        SUBSTRING_INDEX(civilian.civilian_location, ',', -1) AS longitude,
        offer.offer_date_posted,
        offer.offer_category,
        offer.offer_status,
        offer.offer_product_name,
        offer.offer_quantity,
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
        offer.offer_status NOT IN ('CANCELED', 'COMPLETED');
        ";

$data3 = array();
$sqlmyrequest = $conn->query($offers);

if ($sqlmyrequest) {
    // Initialize an empty array to hold the data
    $data3 = array();

    while ($row = $sqlmyrequest->fetch_assoc()) {
        $number = $row["civilian_number"];
    
        // Populate the $data3 array with data from the query
        $data3[] = array(
            "civilian_username" => $row["civilian_username"],
            "civilian_number" => $number,
            "offer_date_posted" => $row["offer_date_posted"],
            "offer_category" => $row["offer_category"],
            "offer_status" => $row["offer_status"],
            "offer_product_name" => $row["offer_product_name"],
            "offer_quantity" => $row["offer_quantity"], 
            "vehicle_name" => $row["vehicle_name"],
            "latitude" => $row["latitude"], 
            "longitude" => $row["longitude"]
        );
    }
   
    // Encode $data3 array to JSON
    $json_data = json_encode($data3);

    // Specify the path to store the JSON file
    $json_file = 'offers_response.json';

    // Write JSON data to file
    if (file_put_contents($json_file, $json_data)) {
       
    } else {
        echo "Unable to write JSON data to $json_file";
    }
    
    // Close the result set
    $sqlmyrequest->close();
} else {
    die("Error executing the SQL query: " . $conn->error);
}



function fetchOnWayRequests($conn){
    $myrequest = "SELECT request.*,civilian.civilian_number, civilian.civilian_first_name, civilian.civilian_last_name,
                    SUBSTRING_INDEX(civilian.civilian_location, ',', 1) AS latitude,
                    SUBSTRING_INDEX(civilian.civilian_location, ',', -1) AS longitude,
                    task.*
                    FROM
                    request
                    JOIN
                    civilian ON request.request_civilian = civilian.civilian_username
                    JOIN
                    task ON request.id_request = task.task_request_id
                    WHERE
                    request.id_request IN (
                        SELECT task_request_id
                        FROM task) AND request.state = 'ON THE WAY'";

    $data3 = array();

    $sqlmyrequest = $conn->query($myrequest);

    if ($sqlmyrequest) {
        while ($row = $sqlmyrequest->fetch_assoc()) {
            $number = $row["civilian_number"];
            $first_name = $row["civilian_first_name"];
            $last_name = $row["civilian_last_name"];

            $data3[] = array(
                "id_request" => $row["id_request"],
                "request_civilian" => $row["request_civilian"],
                "request_category" => $row["request_category"],
                "request_product_name" => $row["request_product_name"],
                "persons" => $row["persons"],
                "request_date_posted" => $row["request_date_posted"],
                "request_time_posted" => $row["request_time_posted"], 
                "state" => $row["state"],
                "number" => $number,
                "first_name" => $first_name,
                "last_name" => $last_name,
                "latitude" => $row["latitude"], 
                "longitude" => $row["longitude"], 
                "task_date" => $row["task_date"],
                "task_time" => $row["task_time"], 
                "task_volunteer" => $row["task_volunteer"]
                
            );
        }
       
        // Encode $data1 array to JSON
        $json_data = json_encode($data3);

        // Specify the path to store the JSON file
        $json_file = 'OnWayRequests.json';

        // Write JSON data to file
        if (file_put_contents($json_file, $json_data)) {
            return "JSON data successfully written to $json_file";
        } else {
            return "Unable to write JSON data to $json_file";
        }
        // Close the result set
        $sqlmyrequest->close();
    } else {
        die("Error executing the SQL query: " . $conn->error);
    }
}
// Check if this request is to update the JSON file
if (isset($_GET['update_json3'])) {
    echo fetchOnWayRequests($conn);
    exit();
} else {
    fetchOnWayRequests($conn);
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
    <link rel="stylesheet" href="css/admin_map.css">
    <link rel="icon" type="image/jpg" sizes="96x96" href="images/favicon.png">
    <link rel="stylesheet" href="leaflet-routing-machine.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.2.0/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />
    <script src="https://unpkg.com/leaflet@1.2.0/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

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
        <h2> Map </h2>
        <br>
        <form class="filters">
                <input type="radio" id="layer1" name="mapLayer" onchange="toggleLayer('layer1')">
                <label for="layer1">Vehicles Waiting</label>

                <input type="radio" id="layer2" name="mapLayer" onchange="toggleLayer('layer2')">
                <label for="layer2">Vehicles On The Way</label>

                <input type="radio" id="layer3" name="mapLayer" onchange="toggleLayer('layer3')">
                <label for="layer3">Offers</label>

                <input type="radio" id="layer4" name="mapLayer" onchange="toggleLayer('layer4')">
                <label for="layer4">Waiting Requests</label>

                <input type="radio" id="layer5" name="mapLayer" onchange="toggleLayer('layer5')">
                <label for="layer5">On their way Requests</label>

                <input type="radio" id="layer6" name="mapLayer" onchange="toggleLayer('layer6')">
                <label for="layer5">Lines</label>
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
              <li>Email: Karagiannis.giorg@gmail.com</li>
              <br>
              <li>Phone: +30 123456789</li>
            </ul>
    
          </div>
        </div>
    
    </div>

    <script src="admin_map.js"></script>

</body>
</html>