<?php
  $servername = "localhost";
  $username = "root";
  $password = "karagiannis";
  $dbname = "carelink";

  // Create connection
  $conn = new mysqli($servername, $username, $password, $dbname);

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

  // Your SQL query for requests
  $request = "SELECT request.*, civilian.civilian_number , civilian.civilian_location 
          FROM request 
          JOIN civilian ON request.request_civilian = civilian.civilian_username";

  $data1 = array();

  $sqlrequest = $conn->query($request);

  if ($sqlrequest) {
      while ($row = $sqlrequest->fetch_assoc()) {
          // Split the coordinates into latitude and longitude
          list($latitude, $longitude) = explode(",",$row["civilian_location"]);
          $number = $row["civilian_number"];

          $data1[] = array(
            "id_request" => $row["id_request"],
            "request_civilian" => $row["request_civilian"],
            "request_category" => $row["request_category"],
            "request_product_name" => $row["request_product_name"],
            "persons" => $row["persons"],
            "request_date_posted" => $row["request_date_posted"],
            "request_time_posted" => $row["request_time_posted"], 
            "state" => $row["state"],
            "number" => $number,
            "latitude" => $latitude,
            "longitude" => $longitude
        );
      }

      // Close the result set
      $sqlrequest->close();
  } else {
      die("Error executing the SQL query: " . $conn->error);
  }

  /* // Your SQL query for offers
  $offers = "SELECT offer.*, civilian.civilian_number , civilian.civilian_location
          FROM offer 
          JOIN civilian ON offer.offer_civilian = civilian.civilian_username";

  $data2 = array();
  $sqloffers = $conn->query($offers);

  if ($sqloffers) {
      while ($row = $sqloffers->fetch_assoc()) {
          // Split the coordinates into latitude and longitude
          list($latitude, $longitude) = explode(",", $row["civilian_location"]);
          $number = $row["civilian_number"];


          $data2[] = array(
              "offer_id" => $row["offer_id"],
              "offer_civilian" => $row["offer_civilian"],
              "offer_category" => $row["offer_category"],
              "offer_product_name" => $row["offer_product_name"],
              "offer_quantity" => $row["offer_quantity"],
              "offer_date_posted" => $row["offer_date_posted"],
              "offer_time_posted" => $row["offer_time_posted"],
              "offer_status" => $row["offer_status"],
              "number" => $number,
              "latitude" => $latitude,
              "longitude" => $longitude
          );
      }

      // Close the result set
      $sqloffers->close();
  } else {
      die("Error executing the SQL query: " . $conn->error);
  }


  // Your SQL query for requests
  $myrequest = "SELECT request.*,civilian.civilian_number,civilian.civilian_location,task.task_date,task.task_time
                    FROM
                    request
                    JOIN
                    civilian ON request.request_civilian = civilian.civilian_username
                    JOIN
                    task ON request.id_request = task.task_request_id
                    WHERE
                    request.id_request IN (
                        SELECT task_request_id
                        FROM task
                        WHERE task_volunteer = '$defaultUsername')";

  $data3 = array();

  $sqlmyrequest = $conn->query($myrequest);

  if ($sqlmyrequest) {
      while ($row = $sqlmyrequest->fetch_assoc()) {
          // Split the coordinates into latitude and longitude
          list($latitude, $longitude) = explode(",", $row["civilian_location"]);
          $number = $row["civilian_number"];

          $data3[] = array(
              "id_request" => $row["id_request"],
              "request_civilian" => $row["request_civilian"],
              "request_category" => $row["request_category"],
              "request_product_name" => $row["request_product_name"],
              "persons" => $row["persons"],
              "date_posted" => $row["date_posted"],
              "time_posted" => $row["time_posted"],
              "stated" => $row["state"],
              "number" => $number,
              "dateaccept" => $row["task_date"],
              "timeaccept" => $row["task_time"],
              "latitude" => $latitude,
              "longitude" => $longitude
          );
      }
      // Close the result set
      $sqlmyrequest->close();
  } else {
      die("Error executing the SQL query: " . $conn->error);
  }  */

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];

    // For the load form
    if (isset($_POST['Cateload']) && isset($_POST['Prodload']) && isset($_POST['Quantload'])) {
        $Category1 = $_POST['Cateload'];
        $Product1 = $_POST['Prodload'];
        $Quantity1 = (int)$_POST['Quantload'];

        $stmt1 = $conn->prepare("INSERT INTO vehicle (driver, products, quantity) VALUES (?, ?, ?)");
        $stmt1->bind_param("ssi", $username, $Product1, $Quantity1);

        $stmt2 = $conn->prepare("UPDATE categories SET quantity_on_stock = quantity_on_stock - ? AND quantity_on_truck = quantity_on_truck + ? WHERE category_name = ? AND products = ?");
        $stmt2->bind_param("iiss", $Quantity1, $Quantity1, $Category1, $Product1);

        if ($stmt1->execute() && $stmt2->execute()) {
            // Redirect to a different page after successful form submission
            header("Location: volunteer.php");
            exit();
        } else {
            echo "Error: " . $stmt1->error . " " . $stmt2->error;
        }

        $stmt1->close();
        $stmt2->close();
    }

    // For the unload form
    if (isset($_POST['Cateunload']) && isset($_POST['Produnload']) && isset($_POST['Quantunload'])) {
        $CategoryUnload = $_POST['Cateunload'];
        $ProductUnload = $_POST['Produnload'];
        $QuantityUnload = (int)$_POST['Quantunload'];

        $stmtUnload1 = $conn->prepare("UPDATE categories SET quantity_on_stock = quantity_on_stock + ? AND quantity_on_truck = quantity_on_truck - ? WHERE category_name = ? AND products = ?");
        $stmtUnload1->bind_param("iiss", $QuantityUnload, $QuantityUnload, $CategoryUnload, $ProductUnload );

        $stmtUnload2 = $conn->prepare("DELETE FROM vehicle WHERE products = ? AND quantity = ?");
        $stmtUnload2->bind_param("si", $ProductUnload, $QuantityUnload);

        if ($stmtUnload1->execute() && $stmtUnload2->execute()) {
            // Redirect to a different page after successful form submission
            header("Location: volunteer.php");
            exit();
        } else {
            echo "Error: " . $stmtUnload1->error . " " . $stmtUnload2->error;
        }

        $stmtUnload1->close();
        $stmtUnload2->close();
    }
}

  // Close the database connection
  $conn->close();
?>

<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="utf-8">
  <title>CareLink</title>
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
  <link rel="stylesheet" href="css/test3.css">

</head>

<body>
    <div class="header container-fluid">
        <p><img src="images/logo.png" alt="Logo" width="200"></p>
        <a href="home.html" class="a1"><i class="fa fa-sign-out" style="font-size:24px"></i>Log out</a>
        <div class="h3-header">
        <h3>Get back to action!</h3>
        </div>
    </div>

    <div class="Main container-fluid">
        <div class="Firstsection">
            <h2> Volunteer members</h2>
            <div class="container mt-5">
                <div class="row">
                    <div class="col-sm-4">
                        <h3><a class="a2" href="#Vehicle">My Vehicle</a><i class="fa fas fa-truck" style="font-size:23px"> </i></h3>
                        <p>Volunteers can <strong>tracking</strong> their <strong>vehicle</strong> and see what they tranfer at any
                        time.</p>
                    </div>
                    <div class="col-sm-4">
                        <h3><a class="a2" href="#Map">Maps </a><i class="fa fa-map" style="font-size:24px"></i></h3>
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
                            <div class="col-sm-6"></div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                            <label for="Cateload" class="form-label">Category</label>
                            <select id="Cateload" class="form-control p-2" name="Cateload">
                                <option value="Category1" selected>Category1</option>
                                <option value="dairy">dairy</option>
                                <option value="Category3">Category3</option>
                                <option value="Category4">Category4</option>
                                <option value="Category5">Category5</option>
                                <option value="Category6">Category6</option>
                            </select>
                            </div>
                            
                            <div class="col-sm-6">
                            <label for="Prodload" class="form-label">Product</label>
                            <select id="Prodload" class="form-control p-2" name="Prodload">
                                <option value="Product1" selected>Product1</option>
                                <option value="milk">milk</option>
                                <option value="Producty3">Product3</option>
                                <option value="Product4">Product4</option>
                                <option value="Product5">Product5</option>
                                <option value="Product6">Product6</option>
                            </select>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-sm-6">
                            <label for="Quantload" class="form-label">Quantity</label>
                            <input type="number" class="form-control p-2" id="Quantload" name="Quantload"
                                placeholder="Insert the quantity of the product " autocomplete="off" required>
                            </div>
                            <div class="col-sm-6">
                            <div class="row">
                                <div class="col-sm-4">
                                <button class="load" type="submit">Load</button>
                                </div>
                                <div class="col-sm-4">
                                <button class="reset" type="reset" onclick="resetForm()">Reset</button>
                                </div>
                                <div class="col-sm-4">
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
                            <label for="Cateunload" class="form-label">Category</label>
                            <select id="Cateunload" class="form-control p-2" name="Cateunload">
                                <option value="Category1" selected>Category1</option>
                                <option value="dairy">dairy</option>
                                <option value="Category3">Category3</option>
                                <option value="Category4">Category4</option>
                                <option value="Category5">Category5</option>
                                <option value="Category6">Category6</option>
                            </select>
                            </div>
                            <div class="col-sm-6">
                            <label for="Produnload" class="form-label">Product</label>
                            <select id="Produnload" class="form-control p-2" name="Produnload">
                                <option value="Product1" selected>Product1</option>
                                <option value="milk">milk</option>
                                <option value="Producty3">Product3</option>
                                <option value="Product4">Product4</option>
                                <option value="Product5">Product5</option>
                                <option value="Product6">Product6</option>
                            </select>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-sm-6">
                            <label for="Quantunload" class="form-label">Quantity</label>
                            <input type="number" class="form-control p-2" id="Quantunload" name="Quantunload"
                                placeholder="Insert the quantity of the product " autocomplete="off" required>
                            </div>
                            <div class="col-sm-6">
                            <div class="row">
                                <div class="col-sm-4">
                                <button class="unload" type="submit">Unload</button>
                                </div>
                                <div class="col-sm-4">
                                <button class="reset" type="reset" onclick="resetForm()">Reset</button>
                                </div>
                                <div class="col-sm-4">
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

        <div class="thirdsection">
            <h2 class="with-hr" id="B">Map</h2>
            <hr>
            <form class="filters">
                <input type="radio" id="layer1" name="mapLayer" onchange="toggleLayer('layer1')" checked>
                <label for="layer1">Requests</label>

                <input type="radio" id="layer2" name="mapLayer" onchange="toggleLayer('layer2')">
                <label for="layer2">Offers</label>

                <input type="radio" id="layer3" name="mapLayer" onchange="toggleLayer('layer3')">
                <label for="layer3">My-Requests</label>

                <input type="radio" id="layer4" name="mapLayer" onchange="toggleLayer('layer4')">
                <label for="layer4">My-Offers</label>
            </form>
            <div id='map'></div>
        </div>


        <div class="forthection">
          <h2 class="with-hr" id="C">My tasks</h2>
          <hr>
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
  <script>
    var data = <?php echo json_encode($data1); ?>;
  </script>
  <script src="volunteer1.js"></script>
  <script>
  function hideForm(formId) {
      var form = document.getElementById(formId);
      form.style.display = 'none';
    }
  </script>

<script>
    function showRequests(username) {
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

        // Call showRequests to fetch and display user requests
        showRequests(defaultUsername);
    });


    function handleMarkerButtonClick(requestId) {
        var username = "<?php echo $defaultUsername; ?>"; // Get the username from PHP
        var url = "addrequest_volunteer.php";

        // Create a FormData object and append the data you want to send
        var formData = new FormData();
        formData.append("requestId", requestId);
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