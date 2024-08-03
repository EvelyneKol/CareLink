<?php
include 'Connection.php';

session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'volunteer') {
    header('Location: sign_in.php');
    exit();
}

// Check connection
if ($conn->connect_error) {
    die("Failed to connect to MySQL: " . $conn->connect_error);
}
// Check if the username is set in cookies
if(isset($_COOKIE['username'])){
    $defaultUsername = $_COOKIE['username'];
} else {
    $defaultUsername = "";
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

    //-----------------συνάρτηση για fetch waiting requests------------------------
    function fetchRequests($conn) {
        $waitingRequest = "SELECT DISTINCT
            civilian.civilian_first_name,
            civilian.civilian_last_name,
            civilian.civilian_number,
            SUBSTRING_INDEX(civilian.civilian_location, ',', 1) AS latitude,
            SUBSTRING_INDEX(civilian.civilian_location, ',', -1) AS longitude,
            request.request_date_posted,
            request.id_request,
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

        $waitingRequestdata = array();
        $sqlwaitingRequest = $conn->query($waitingRequest);

        if ($sqlwaitingRequest) {
            while ($row = $sqlwaitingRequest->fetch_assoc()) {
                $waitingRequestdata[] = array(
                    "civilian_first_name" => $row["civilian_first_name"],
                    "civilian_last_name" => $row["civilian_last_name"],
                    "civilian_number" => $row["civilian_number"],
                    "request_date_posted" => $row["request_date_posted"],
                    "id_request" => $row["id_request"],
                    "request_category" => $row["request_category"],
                    "state" => $row["state"],
                    "request_product_name" => $row["request_product_name"], 
                    "persons" => $row["persons"],
                    "latitude" => $row["latitude"], 
                    "longitude" => $row["longitude"]
                );
            }

        // Encode $data4 array to JSON
        $json_data = json_encode($waitingRequestdata);

        // Specify the path to store the JSON file
        $json_file = 'volWaitingRequests.json';

        // Write JSON data to file
        // Write JSON data to file
        if (file_put_contents($json_file, $json_data)) {
            
        } else {
            echo "Unable to write JSON data to $json_file";
        }

        $sqlwaitingRequest->close();
        } else {
            die("Error executing the SQL query: " . $conn->error);
        }

    }

    // Check if this request is to update the JSON file
    if (isset($_GET['volWaitingRequests_json'])) {
        echo fetchRequests($conn);
        exit();
    } else {
        fetchRequests($conn);
    }


    //----------------------συνάρτηση για waiting offer (προσφορές)-------------------
    function fetchOffers($conn) {
        $offers = "SELECT 
                    civilian.civilian_first_name,
                    civilian.civilian_last_name,
                    civilian.civilian_number,
                    SUBSTRING_INDEX(civilian.civilian_location, ',', 1) AS latitude,
                    SUBSTRING_INDEX(civilian.civilian_location, ',', -1) AS longitude,
                    offer.offer_date_posted,
                    offer.offer_category,
                    offer.offer_status,
                    offer.offer_product_name,
                    offer.offer_id,
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
                    offer.offer_status = 'WAITING'";

        $waitingOffersData = array();
        $sqloffers = $conn->query($offers);

        if ($sqloffers) {
            while ($row = $sqloffers->fetch_assoc()) {
                // Populate the $data array with data from the query
                $waitingOffersData[] = array(
                    "civilian_first_name" => $row["civilian_first_name"],
                    "civilian_last_name" => $row["civilian_last_name"],
                    "civilian_number" => $row["civilian_number"],
                    "offer_date_posted" => $row["offer_date_posted"],
                    "offer_category" => $row["offer_category"],
                    "offer_status" => $row["offer_status"],
                    "offer_product_name" => $row["offer_product_name"],
                    "offer_id" => $row["offer_id"],
                    "offer_quantity" => $row["offer_quantity"], 
                    "vehicle_name" => $row["vehicle_name"],
                    "task_date" => $row["task_date"],
                    "latitude" => $row["latitude"], 
                    "longitude" => $row["longitude"]
                );
            }

            // Encode $data3 array to JSON
            $json_data = json_encode($waitingOffersData);

            // Specify the path to store the JSON file
            $json_file = 'volWaitingOffers.json';

            // Write JSON data to file
            if (file_put_contents($json_file, $json_data)) {
            
            } else {
                echo "Unable to write JSON data to $json_file";
            }
            
            // Close the result set
            $sqloffers->close();
        } else {
            die("Error executing the SQL query: " . $conn->error);
        }


    }

    // Check if this offer is to update the JSON file
    if (isset($_GET['volWaitingOffers_json'])) {
        echo fetchOffers($conn);
        exit();
    } else {
        fetchOffers($conn);
    }



    //-----------------------------fetch my Requests data--------------------------------------
    function fetchMyRequests($conn, $defaultUsername){
        $myrequest = "SELECT DISTINCT
                    civilian.civilian_first_name,
                    civilian.civilian_last_name,
                    civilian.civilian_number,
                    SUBSTRING_INDEX(civilian.civilian_location, ',', 1) AS latitude,
                    SUBSTRING_INDEX(civilian.civilian_location, ',', -1) AS longitude,
                    vehicle.vehicle_name,
                    task.task_date,
                    request.request_date_posted,
                    request.id_request,
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
                    WHERE
                                request.id_request IN (
                                    SELECT task_request_id
                                    FROM task
                                    WHERE task_volunteer = '$defaultUsername') AND request.state = 'ON THE WAY'";

        $myRequestData = array();

        $sqlmyrequest = $conn->query($myrequest);

        if ($sqlmyrequest) {
            while ($row = $sqlmyrequest->fetch_assoc()) {

                $myRequestData[] = array(
                    "civilian_first_name" => $row["civilian_first_name"],
                    "civilian_last_name" => $row["civilian_last_name"],
                    "civilian_number" => $row["civilian_number"],
                    "vehicle_name" => $row["vehicle_name"],
                    "task_date" => $row["task_date"],
                    "id_request" => $row["id_request"], 
                    "request_date_posted" => $row["request_date_posted"],
                    "request_category" => $row["request_category"],
                    "state" =>  $row["state"],
                    "request_product_name" =>  $row["request_product_name"],
                    "persons" => $row["persons"],
                    "latitude" => $row["latitude"], 
                    "longitude" => $row["longitude"]
                );
            }
            // Store  in a session variable
            $_SESSION['myRequests'] = $myRequestData;
    
            // Encode $data1 array to JSON
            $json_data = json_encode($myRequestData);

            // Specify the path to store the JSON file
            $json_file = 'myRequests.json';

            // Write JSON data to file
            if (file_put_contents($json_file, $json_data)) {
                
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
    if (isset($_GET['myRequests_json'])) {
        echo fetchMyRequests($conn, $defaultUsername);
        exit();
    } else {
        fetchMyRequests($conn, $defaultUsername);
    }



//____________function to fetch volunteer's offerss_____________________________________
    function fetchMyOffers($conn,$defaultUsername){
        $myoffers = "SELECT DISTINCT
                    civilian.civilian_first_name,
                    civilian.civilian_last_name,
                    civilian.civilian_number,
                    SUBSTRING_INDEX(civilian.civilian_location, ',', 1) AS latitude,
                    SUBSTRING_INDEX(civilian.civilian_location, ',', -1) AS longitude,
                    offer.offer_date_posted,
                    offer.offer_category,
                    offer.offer_status,
                    offer.offer_product_name,
                    offer.offer_id,
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
                        offer.offer_id IN (
                            SELECT task_offer_id
                            FROM task
                            WHERE task_volunteer = '$defaultUsername') AND offer.offer_status='ON THE WAY'";

        $myOffersData = array();

        $sqlmyoffers = $conn->query($myoffers);

        if ($sqlmyoffers) {
            while ($row = $sqlmyoffers->fetch_assoc()) {
        
                $myOffersData[] = array(
                    "civilian_first_name" => $row["civilian_first_name"],
                    "civilian_last_name" => $row["civilian_last_name"],
                    "civilian_number" => $row["civilian_number"],
                    "offer_date_posted" => $row["offer_date_posted"],
                    "offer_category" => $row["offer_category"],
                    "offer_status" => $row["offer_status"],
                    "offer_product_name" => $row["offer_product_name"],
                    "offer_id" => $row["offer_id"],
                    "latitude" => $row["latitude"], 
                    "longitude" => $row["longitude"],
                    "offer_quantity" => $row["offer_quantity"],
                    "task_date" => $row["task_date"], 
                    "vehicle_name" => $row["vehicle_name"]                   
                );
            }
            // Store $data3 in a session variable
            $_SESSION['myOffers'] = $myOffersData;

            // Encode $data1 array to JSON
            $json_data = json_encode($myOffersData);

            // Specify the path to store the JSON file
            $json_file = 'myOffers.json';

            // Write JSON data to file
            if (file_put_contents($json_file, $json_data)) {
                
            } else {
                return "Unable to write JSON data to $json_file";
            }

            // Close the result set
            $sqlmyoffers->close();

        } else {
            die("Error executing the SQL query: " . $conn->error);
        }
    }
    // Check if this request is to update the JSON file
    if (isset($_GET['myOffers_json'])) {
        echo fetchMyOffers($conn,$defaultUsername);
        exit();
    } else {
        fetchMyOffers($conn,$defaultUsername);
    }

    function fetchTaskCount($conn, $defaultUsername) {
        $taskCountQuery = "SELECT COUNT(task_volunteer) AS task_count FROM task WHERE task_volunteer = '$defaultUsername'";
    
        $taskCountData = array();
    
        $sqltaskCount = $conn->query($taskCountQuery);
    
        if ($sqltaskCount) {
            while ($row = $sqltaskCount->fetch_assoc()) {
                $taskCountData[] = array(
                    "task_count" => $row["task_count"]
                );
            }
    
            // Encode $taskCountData array to JSON
            $json_data = json_encode($taskCountData);
    
            // Specify the path to store the JSON file
            $json_file = 'taskcount.json';
    
            // Write JSON data to file
            if (file_put_contents($json_file, $json_data)) {
                return "JSON data successfully written to $json_file";
            } else {
                return "Unable to write JSON data to $json_file";
            }
    
            // Close the result set
            $sqltaskCount->close();
        } else {
            die("Error executing the SQL query: " . $conn->error);
        }
    }
    
    // Check if this request is to update the JSON file
    if (isset($_GET['taskcount_json'])) {
        echo fetchTaskCount($conn, $defaultUsername);
        exit();
    } else {
        fetchTaskCount($conn, $defaultUsername);
    }
    

    
//________________________________queries for load and unload_________________________________________
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['Cateload']) && isset($_POST['Prodload']) && isset($_POST['Quantload']) && isset($_POST['loadAddress']) && isset($_POST['Vehicle_name'])) {
        $Category1 = $_POST['Cateload'];
        $Product1 = $_POST['Prodload'];
        $Quantity1 = (int)$_POST['Quantload'];
        $location1 = $_POST['loadAddress'];
        $v_name = $_POST['Vehicle_name'];

        $stmtCheck = $conn->prepare("SELECT quantity FROM vehiclesOnAction WHERE category = ? AND products = ? AND driver = ?");
        $stmtCheck->bind_param("sss", $Category1, $Product1, $defaultUsername);
        $stmtCheck->execute();
        $stmtCheck->store_result();

        if ($stmtCheck->num_rows > 0) {
            $stmtCheck->bind_result($currentQuantity);
            $stmtCheck->fetch();
            $newQuantity = $currentQuantity + $Quantity1;

            $stmtUpdate = $conn->prepare("UPDATE vehiclesOnAction SET quantity = ?, vehicle_location = ?, v_name = ? WHERE category = ? AND products = ? AND driver = ?");
            $stmtUpdate->bind_param("isssss", $newQuantity, $location1, $v_name, $Category1, $Product1, $defaultUsername);
            $stmtUpdate->execute();
            $stmtUpdate->close();
        } else {
            $stmtInsert = $conn->prepare("INSERT INTO vehiclesOnAction (v_name, driver, products, quantity, category, vehicle_location) VALUES (?, ?, ?, ?, ?, ?)");
            $stmtInsert->bind_param("sssiss", $v_name, $defaultUsername, $Product1, $Quantity1, $Category1, $location1);
            $stmtInsert->execute();
            $stmtInsert->close();
        }
        $stmtCheck->close();

        $stmt2 = $conn->prepare("UPDATE categories SET quantity_on_stock = quantity_on_stock - ?, quantity_on_truck = quantity_on_truck + ? WHERE category_name = ? AND products = ?");
        $stmt2->bind_param("iiss", $Quantity1, $Quantity1, $Category1, $Product1);
        $stmt2->execute();
        $stmt2->close();
    } 

    if (isset($_POST['CateUnload']) && isset($_POST['Produnload']) && isset($_POST['Quantunload']) && isset($_POST['Vehicle_nameUnload'])) {
        $CategoryUnload = $_POST['CateUnload'];
        $ProductUnload = $_POST['Produnload'];
        $QuantityUnload = (int)$_POST['Quantunload'];
        $v_name = $_POST['Vehicle_nameUnload'];
    
        $stmtCheck = $conn->prepare("SELECT quantity FROM vehiclesOnAction WHERE category = ? AND products = ? AND driver = ?");
        $stmtCheck->bind_param("sss", $CategoryUnload, $ProductUnload, $defaultUsername);
        $stmtCheck->execute();
        $stmtCheck->bind_result($existingQuantity);
        $stmtCheck->fetch();
        $stmtCheck->close();
    
        if ($existingQuantity >= $QuantityUnload) {
            $newQuantity = $existingQuantity - $QuantityUnload;
    
            if ($newQuantity != 0) {
                $stmtUpdate = $conn->prepare("UPDATE vehiclesOnAction SET quantity = ? WHERE category = ? AND products = ? AND driver = ?");
                $stmtUpdate->bind_param("isss", $newQuantity, $CategoryUnload, $ProductUnload, $defaultUsername);
                $stmtUpdate->execute();
                $stmtUpdate->close();
            } else {
                $stmtDelete = $conn->prepare("DELETE FROM vehiclesOnAction WHERE category = ? AND products = ? AND driver = ?");
                $stmtDelete->bind_param("sss", $CategoryUnload, $ProductUnload, $defaultUsername);
                $stmtDelete->execute();
                $stmtDelete->close();
            }
    
            $stmtUnload1 = $conn->prepare("UPDATE categories SET quantity_on_stock = quantity_on_stock + ?, quantity_on_truck = quantity_on_truck - ? WHERE category_name = ? AND products = ?");
            $stmtUnload1->bind_param("iiss", $QuantityUnload, $QuantityUnload, $CategoryUnload, $ProductUnload);
            $stmtUnload1->execute();
            $stmtUnload1->close();
        } else {
            echo "Error: Not enough quantity to unload.";
        }
    }
    
}

$sql = "SELECT DISTINCT category_name FROM categories";
$result = $conn->query($sql);

$sqlUnload = "SELECT DISTINCT category FROM vehiclesOnAction";
$resultUnload = $conn->query($sqlUnload);

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="utf-8">
  <title>Volunteer</title>
  <link rel="stylesheet" href="volunteer.css">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" type="image/jpg" sizes="96x96" href="images/favicon.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
  
  <style>
        .thirdsection .Acceptbut{
            background-color: rgb(3, 129, 178);
            border: none;
            border-radius: 15px;
            color: white;
            padding: 5px 10px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            margin: 4px 2px;
            cursor: pointer;
            }

        .thirdsection .Acceptbut:hover{
            background-color: rgba(3, 128, 178, 0.7);
            color: rgb(217, 217, 217);
            }
   </style>
</head>


<body>
    <div class="header container-fluid">
        <p><img src="images/logo1.png" alt="Logo" width="200"></p>
        <a href="home.html" class="a1"><i class="fa fa-sign-out" style="font-size:24px"></i>Log out</a>
        <div class="h3-header">
        <h3>Get back to action!</h3>
        </div>
    </div>

    <hr>

    <div class="Main container-fluid">
        <div class="Firstsection">
            <div class="container mt-5">
                <div class="row">
                    <div class="col-sm-4">
                        <h3><a class="a2" href="#myDetails">My Vehicle</a><i class="fa fas fa-truck" style="font-size:23px"> </i></h3>
                        <p>Volunteers can <strong>tracking</strong> their <strong>vehicle</strong> and see what they tranfer at any
                        time.</p>
                    </div>
                    <div class="col-sm-4">
                        <h3><a class="a2" href="#Map">Map </a><i class="fa fa-map" style="font-size:24px"></i></h3>
                        <p>A map with all the tasks available,
                        the <strong>tasks</strong> you have taken with their <strong>route</strong> and the
                        location of the <strong>store</strong> in available for you.</p>
                    </div>
                    <div class="col-sm-4">
                        <h3><a class="a2" href="#Task">My tasks</a> <i class="fa fa-tasks" style="font-size:24px"></i></h3>
                        <p>In this section you can see all the information about your task.
                        You can also update the task-status to <strong>"Done" </strong> or <strong>"Canceled"</strong> </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="Secondsection">
                <h2 id="A">My Vehicle</h2>
                <hr>
                <div class="managmentection">
                    <div class="row">
                        <div class="col4 col-sm-6">
                        <button id="yourButtonId1" class="button1" disabled  onclick="loadItems('loadForm')" >
                            <strong> Load items </strong></button>
                        </div>
                        <div class="col5 col-sm-6">
                        <button id="yourButtonId2" class="button1" disabled  onclick="unloadItems('UnloadForm')">
                            <strong> Unload items </strong></button>
                        </div>
                    </div>
                </div>

                <br>

                <div id="loadForm" style="display:none;">
                    <div class="row">
                        <div class="col-sm-3"></div>
                        <div class="col-sm-6">
                            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" onsubmit="resetForm()">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <label for="txtUsername" class="form-label">Username</label>
                                        <input type="text" class="form-control p-2" id="txtUsername" name="username"
                                        placeholder="Write your Username..." autocomplete="on" required value="<?php echo $defaultUsername; ?>" readonly>
                                    </div>

                                    <div class="col-sm-3">
                                        <label for="loadAddress">Vehicle-Location</label>
                                        <input type="text" class="form-control p-2" placeholder="Enter Your Address" id="loadAddress" name="loadAddress" autocomplete="on"
                                        spellcheck="false" required readonly>
                                    </div>
                                    <div class="col-sm-3">
                                        <label for="Vehicle_name">Vehicle-Name</label>
                                        <input type="text" class="form-control p-2" placeholder="Enter Your Vehicle name" id="Vehicle_name" name="Vehicle_name" spellcheck="false" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                            <label for="Cateload" class="form-label">Category</label>
                                            <select id="Cateload" class="form-control p-2" name="Cateload">
                                            <option value="">Select a category</option>
                                                <?php
                                                // Check if there are results
                                                if ($result->num_rows > 0) {
                                                    // Output data of each row
                                                    while($row = $result->fetch_assoc()) {
                                                        echo '<option value="' . htmlspecialchars($row["category_name"]) . '">' . htmlspecialchars($row["category_name"]) . '</option>';
                                                    }
                                                } else {
                                                    echo '<option value="">No categories available</option>';
                                                }
                                                ?>
                                            </select>
                                    </div>
                                        
                                    <div class="col-sm-6">
                                        <label for="Prodload" class="form-label">Product</label>
                                        <select id="Prodload" class="form-control p-2" name="Prodload">
                                        </select>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                <div class="col-sm-6">
                                    <label for="Quantload" class="form-label">Quantity</label>
                                    <input type="number" class="form-control p-2" id="Quantload" name="Quantload"
                                        placeholder="Insert the quantity of the product" autocomplete="off" required min="0">
                                </div>
                                    <div class="col-sm-6">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <button class="load" type="submit">Load</button>
                                        </div>
                                        <div class="col-sm-6">
                                            <button class="hidebutton" type="button" onclick="hideForm('loadForm')">Hide Form</button>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-sm-3"></div>
                    </div>
                </div>

                <br>

                <div id="UnloadForm" style="display:none;">
                <div class="row">
                    <div class="col-sm-3"></div>
                    <div class="col-sm-6">
                        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" onsubmit="resetForm()">
                            <div class="row">
                                <div class="col-sm-6">
                                    <label for="txtUsernameUnload" class="form-label">Username</label>
                                    <input type="text" class="form-control p-2" id="txtUsernameUnload" name="username"
                                        placeholder="Write your Username..." autocomplete="on" required value="<?php echo $defaultUsername; ?>" readonly>
                                </div>
                                <div class="col-sm-6">
                                    <label for="Vehicle_nameUnload">Vehicle-Name</label>
                                    <input type="text" class="form-control p-2" placeholder="Enter Your Vehicle name" id="Vehicle_nameUnload" name="Vehicle_nameUnload" spellcheck="false" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <label for="CateUnload" class="form-label">Category</label>
                                    <select id="CateUnload" class="form-control p-2" name="CateUnload">
                                        <option value="">Select a category</option>
                                        <?php
                                        // Check if there are results
                                        if ($resultUnload->num_rows > 0) {
                                            // Output data of each row
                                            while($row = $resultUnload->fetch_assoc()) {
                                                echo '<option value="' . htmlspecialchars($row["category"]) . '">' . htmlspecialchars($row["category"]) . '</option>';
                                            }
                                        } else {
                                            echo '<option value="">No categories available</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-sm-6">
                                    <label for="Produnload" class="form-label">Product</label>
                                    <select id="Produnload" class="form-control p-2" name="Produnload">
                                    </select>
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-sm-6">
                                    <label for="Quantunload" class="form-label">Quantity</label>
                                    <input type="number" class="form-control p-2" id="Quantunload" name="Quantunload"
                                        placeholder="Insert the quantity of the product " autocomplete="off" required min="0">
                                </div>
                                <div class="col-sm-6">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <button class="unload" type="submit">Unload</button>
                                        </div>
                                        <div class="col-sm-6">
                                            <button class="hidebutton" type="button" onclick="hideForm('UnloadForm')">Hide Form</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-sm-3"></div>
                </div>
            </div>


            <details id="myDetails">
                <summary>Status of my Truck</summary>
                <div id="vehiclestatus"></div>
            </details>
            
        </div>

        <div class="thirdsection" id="Map">
            <h2 class="with-hr" id="B" style="text-align: center;">Map</h2>
            <hr>
            <form class="filters">
                <input type="checkbox" id="layer1" name="mapLayer" onchange="toggleLayer('layer1')">
                <label for="layer1">Requests</label>

                <input type="checkbox" id="layer2" name="mapLayer" onchange="toggleLayer('layer2')">
                <label for="layer2">Offers</label>

                <input type="checkbox" id="layer3" name="mapLayer" onchange="toggleLayer('layer3')">
                <label for="layer3">My-Requests</label>

                <input type="checkbox" id="layer4" name="mapLayer" onchange="toggleLayer('layer4')">
                <label for="layer4">My-Offers</label>
               
            </form>
            <div id='map'></div>
        </div>


        <div class="forthection">
          <h2 class="with-hr" id="Task" style="text-align: center;">My tasks</h2>
          <hr>
          <div class="row">
                <div class="col-sm-6">
                <h2 class="with-hr" style="text-align: center;">You have requests for:</h2>
                    <div id="myRequests"></div>
                    <br>
                </div>
                <div class="col-sm-6">
                <h2 class="with-hr" style="text-align: center;">You have Offers from:</h2>
                    <div id="myOffers"></div>
                    <br>
                </div>
            </div>
        </div>
    </div>

    <div class="Footer container-fluid">
        <div class="row">
            <div class="col-sm-3">
                <h2>Social Media</h2>
                <nav class="nav02">
                <a href="https://www.linkedin.com/in/george-karagiannis-00a683222/" class="fa fa-linkedin"></a>
                <a href="https://www.facebook.com/george.karagiannis.9406" class="fa fa-facebook"></a>
                <a href="https://www.instagram.com/_karagiannis_g/" class="fa fa-instagram"></a>
                </nav>
            </div>
            <div class="col-sm-6">
                <h2>Licences</h2>
                <br>
                <p>© George Karagiannis/Ceid/Upatras/Year 2023-2024</p>
                <p>© Evelina Kolagki/Ceid/Upatras/Year 2023-2024</p>
            </div>
            <div class="column col-sm-3">
                <h2>Contact info</h2>
                <br>
                <ul class="list-unstyled">
                <li>Email: <span class="email">carelink@gmail.com</span></li>
                <br>
                <li>Phone: +30 123456789</li>
                </ul>
            </div>
        </div>
    </div>

  <script src="volunteer.js"></script>
   
  <script>
    function hideForm(formId) {
            var form = document.getElementById(formId);
            form.style.display = 'none';
            }

    function LoadVehicle(username) {
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                document.getElementById("vehiclestatus").innerHTML = this.responseText;
            }
        };
        xmlhttp.open("GET", "load_vehicle.php?q=" + username, true);
        xmlhttp.send();
    }

    document.addEventListener("DOMContentLoaded", function () {
        // Get the default username value
        var defaultUsername = document.getElementById("txtUsername").value;

        // Call LoadVehicle to fetch and display user requests
        LoadVehicle(defaultUsername);
    });

    $(document).ready(function() {
            $('#Cateload').change(function() {
                var category_name = $(this).val();
                $.ajax({
                    type: 'POST',
                    url: 'fetch_products.php',
                    data: {category_name: category_name},
                    dataType: 'json',
                    success: function(data) {
                        $('#Prodload').empty();
                        $('#Prodload').append('<option value="">Select Product</option>');
                        $.each(data, function(index, value) {
                            $('#Prodload').append('<option value="'+ value +'">'+ value +'</option>');
                        });
                    }
                });
            });
        });
  
    $(document).ready(function() {
        $('#CateUnload').change(function() {
            var category = $(this).val();
            $.ajax({
                type: 'POST',
                url: 'fetch_unload_products.php',
                data: {category: category},
                dataType: 'json',
                success: function(data) {
                    $('#Produnload').empty();
                    $('#Produnload').append('<option value="">Select Product</option>');
                    $.each(data, function(index, value) {
                        $('#Produnload').append('<option value="'+ value +'">'+ value +'</option>');
                    });
                }
            });
        });
    });

    // JavaScript functions
    function handle_requests(id_request) {
        var username = document.getElementById("txtUsername").value;
        var url = "addrequest_volunteer.php";

        // Create a FormData object and append the data you want to send
        var formData = new FormData();
        formData.append("id_request", id_request);
        formData.append("username", username);

        // Create the XMLHttpRequest object
        var xhr = new XMLHttpRequest();

        // Setup the AJAX request
        xhr.open("POST", url, true);

        // Set up the onload and onerror functions
        xhr.onload = function () {
            if (xhr.status == 200) {
                // Handle the success response
                console.log(xhr.responseText);
                // After handling the request, update the JSON file
                updateRequests();
                updateMyRequests();
                updatetasks(); 
                location.reload();
            } else {
                // Handle the error response
                console.error("Error: " + xhr.statusText);
            }
        };

        xhr.onerror = function () {
            // Handle the network error
            console.error("Network error");
        };

        // Send the AJAX request with the form data
        xhr.send(formData);
    }


    function updateRequests() {
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "<?php echo $_SERVER['PHP_SELF']; ?>?volWaitingRequests_json=true", true);
        xhr.onload = function () {
            if (xhr.status == 200) {
                console.log("JSON file updated successfully");
            } else {
                console.error("Failed to update JSON file: " + xhr.statusText);
            }
        };
        xhr.onerror = function () {
            console.error("Network error while updating JSON file");
        };
        xhr.send();
    }

    function updateMyRequests() {
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "<?php echo $_SERVER['PHP_SELF']; ?>?myRequests_json=true", true);
        xhr.onload = function () {
            if (xhr.status == 200) {
                console.log("JSON file updated successfully");
            } else {
                console.error("Failed to update JSON file: " + xhr.statusText);
            }
        };
        xhr.onerror = function () {
            console.error("Network error while updating JSON file");
        };
        xhr.send();
    }

        

    function handle_offers(offerId) {
        var username = document.getElementById("txtUsername").value;
        var url = "addoffer_volunteer.php";

        // Create a FormData object and append the data you want to send
        var formData = new FormData();
        formData.append("offerId", offerId);
        formData.append("username", username);

        // Create the XMLHttpRequest object
        var xhr = new XMLHttpRequest();

        // Setup the AJAX request
        xhr.open("POST", url, true);

        // Set up the onload and onerror functions
        xhr.onload = function () {
            if (xhr.status == 200) {
                // Handle the success response
                console.log(xhr.responseText);
                // After handling the request, update the JSON file
                updateOffers();
                updateMyOffers();
                updatetasks();
                location.reload();
            } else {
                // Handle the error response
                console.error("Error: " + xhr.statusText);
            }
        };
        xhr.onerror = function () {
            // Handle the network error
            console.error("Network error");
        };

        // Send the AJAX request with the form data
        xhr.send(formData);
    }

    function updateOffers() {
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "<?php echo $_SERVER['PHP_SELF']; ?>?volWaitingOffers=true", true);
        xhr.onload = function () {
            if (xhr.status == 200) {
                console.log("JSON file updated successfully");
            } else {
                console.error("Failed to update JSON file: " + xhr.statusText);
            }
        };
        xhr.onerror = function () {
            console.error("Network error while updating JSON file");
        };
        xhr.send();
    }

    function updateMyOffers() {
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "<?php echo $_SERVER['PHP_SELF']; ?>?myOffers=true", true);
        xhr.onload = function () {
            if (xhr.status == 200) {
                console.log("JSON file updated successfully");
            } else {
                console.error("Failed to update JSON file: " + xhr.statusText);
            }
        };
        xhr.onerror = function () {
            console.error("Network error while updating JSON file");
        };
        xhr.send();
    }

    function updatetasks() {
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "<?php echo $_SERVER['PHP_SELF']; ?>?taskcount_json=true", true);
        xhr.onload = function () {
            if (xhr.status == 200) {
                console.log("JSON file updated successfully");
            } else {
                console.error("Failed to update JSON file: " + xhr.statusText);
            }
        };
        xhr.onerror = function () {
            console.error("Network error while updating JSON file");
        };
        xhr.send();
    }

    function showMyRequests(username) {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("myRequests").innerHTML = this.responseText;
                }
            };
            xmlhttp.open("GET", "load_myRequests.php?q=" + username, true);
            xmlhttp.send();
        }

        document.addEventListener("DOMContentLoaded", function () {
            // Get the default username value
            var defaultUsername = document.getElementById("txtUsername").value;

            // Call showMyRequests to fetch and display user requests
            showMyRequests(defaultUsername);
        });

    function deliver_requests(requestId, category, product, quantity) {
            var username = document.getElementById("txtUsername").value;  // Get the username from PHP and escape it
            var url = "deliver_request_volunteer.php";

            // Create a FormData object and append the data you want to send
            var formData = new FormData();
            formData.append("requestId", requestId);
            formData.append("category", category);
            formData.append("product", product);
            formData.append("quantity", quantity);
            formData.append("username", username);

            // Create the XMLHttpRequest object
            var xhr = new XMLHttpRequest();

            // Setup the AJAX request
            xhr.open("POST", url, true);

            // Set up the onload and onerror functions
            xhr.onload = function () {
                if (xhr.status == 200) {
                    // Handle the success response
                    console.log(xhr.responseText);
                    // Update the UI or perform other actions if needed
                    updateRequests();
                    updateMyRequests();
                    updatetasks();

                    location.reload();
                
                } else {
                    // Handle the error response
                    console.error("Error: " + xhr.statusText);
                }
            };

            xhr.onerror = function () {
                // Handle the network error
                console.error("Network error");
            };

            // Send the AJAX request with the form data
            xhr.send(formData);
        }


  function delete_request(requestId) {
        var url = "delete_request_volunteer.php";

        // Create a FormData object and append the data you want to send
        var formData = new FormData();
        formData.append("requestId", requestId);

        // Create the XMLHttpRequest object
        var xhr = new XMLHttpRequest();

        // Setup the AJAX request
        xhr.open("POST", url, true);

        // Set up the onload and onerror functions
        xhr.onload = function () {
            if (xhr.status == 200) {
                // Handle the success response
                console.log(xhr.responseText);
                updateRequests();
                updateMyRequests();
                updatetasks(); 
                location.reload();
            } else {
                // Handle the error response
                console.error("Error: " + xhr.statusText);
            }
        };
        xhr.onerror = function () {
            // Handle the network error
            console.error("Network error");
        };
        // Send the AJAX request with the form data
        xhr.send(formData);
    }


    function showMyOffers(username) {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("myOffers").innerHTML = this.responseText;
                }
            };
            xmlhttp.open("GET", "load_myOffers.php?q=" + username, true);
            xmlhttp.send();
        }

        document.addEventListener("DOMContentLoaded", function () {
            // Get the default username value
            var defaultUsername = document.getElementById("txtUsername").value;

            // Call showMyOffers to fetch and display user offers
            showMyOffers(defaultUsername);
        });

       
    function accept_offer(offerId, category, product, quantity,latitude,longitude) {
            var username = document.getElementById("txtUsername").value;  // Get the username from PHP and escape it
            var url = "accept_offer_volunteer.php";

            // Create a FormData object and append the data you want to send
            var formData = new FormData();
                formData.append("offerId", offerId);
                formData.append("category", category);
                formData.append("product", product);
                formData.append("quantity", quantity);
                formData.append("latitude", latitude);
                formData.append("longitude", longitude);
                formData.append("username", username);

            // Create the XMLHttpRequest object
            var xhr = new XMLHttpRequest();

            // Setup the AJAX request
            xhr.open("POST", url, true);

            // Set up the onload and onerror functions
            xhr.onload = function () {
                if (xhr.status == 200) {
                    // Handle the success response
                    console.log(xhr.responseText);
                    // Update the UI or perform other actions if needed
                    updateOffers();
                    updateMyOffers();
                    updatetasks();

                    location.reload();

                } else {
                    // Handle the error response
                    console.error("Error: " + xhr.statusText);
                    alert("Error occurred: " + xhr.statusText);
                }
            };

            xhr.onerror = function () {
                // Handle the network error
                console.error("Network error");
                alert("Network error. Please try again later.");
            };

            // Send the AJAX request with the form data
            xhr.send(formData);
        }

    function delete_offer(offerId) {
        var url = "delete_offer_volunteer.php";

        // Create a FormData object and append the data you want to send
        var formData = new FormData();
        formData.append("offerId", offerId);

        // Create the XMLHttpRequest object
        var xhr = new XMLHttpRequest();

        // Setup the AJAX request
        xhr.open("POST", url, true);

        // Set up the onload and onerror functions
        xhr.onload = function () {
            if (xhr.status == 200) {
                // Handle the success response
                console.log(xhr.responseText);

                updateOffers();
                updateMyOffers();
                updatetasks();
                // You can update the UI or perform other actions if needed
                location.reload();
            } else {
                // Handle the error response
                console.error("Error: " + xhr.statusText);
            }
        };
        xhr.onerror = function () {
            // Handle the network error
            console.error("Network error");
        };
        // Send the AJAX request with the form data
        xhr.send(formData);
    }

    </script>
</body>
</html>