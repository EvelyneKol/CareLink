<?php
include 'Connection.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'civilian') {
    header('Location: sign_in.php');
    exit(); }

// Check if the username is set in cookies
if(isset($_COOKIE['username'])){
  $defaultUsername = $_COOKIE['username'];
} else {
  $defaultUsername = "";
}

/* if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $shortageId = $_POST['shortage_id'];
  $category = $_POST['category'];
  $productName = $_POST['product_name'];
  $quantity = $_POST['quantity'];

  // Get the current date and time
  $datePosted = date('Y-m-d');
  $timePosted = date('H:i:s');

  // Assuming you have the username of the civilian making the offer
  $offerCivilian = 'some_user'; // Replace with actual civilian username

  $sql = "INSERT INTO offer (offer_civilian, offer_category, offer_product_name, offer_quantity, offer_date_posted, offer_time_posted, offer_status) VALUES (?, ?, ?, ?, ?, ?, 'WAITING')";

  $stmt = $conn->prepare($sql);
  $stmt->bind_param("sssiss", $offerCivilian, $category, $productName, $quantity, $datePosted, $timePosted);

  if ($stmt->execute()) {
      echo "Offer added successfully.";
  } else {
      echo "Error: " . $stmt->error;
  }
} */

// Fetch shortage records from the database
$shortageQuery = "SELECT * FROM shortage ORDER BY shortage_datetime DESC";
$shortageResult = $conn->query($shortageQuery);


// Close the database connection outside the if block
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
  <link rel="stylesheet" href="Civilian.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>

<div class="navbar">
      <img src="images/logo1.png" alt="Logo">
      <ul class="nav">
          <li><a href="logout.php"><i class="fa fa-sign-out" style="font-size:24px"></i> Log out</a></li>
      </ul>
  </div>

  <div class="Buttons">
    <div class="row">
      <div class="col4 col-sm-6">
        <button class="Request"><a href="civilian_requests.php">Make a Request</a></button>
      </div>
      <div class="col5 col-sm-6">
        <button class="Offer"><a href="sign_in.html">Offer</a></button>
      </div>
    </div>
  </div>

  <div class="Main container-fluid">
    <div class="Firstsection">
      <h2> Welcome back </h2>
      <h2 id="txtUsername"><?php echo $defaultUsername; ?></h2>
      <div class="container mt-5">
        <div class="row">
          <div class="col">
            <h3><a class="a">My Requests </a><img src="images/request.png" alt="heart"></h3>
            <p>All members of 
              <strong>CareLink</strong> can see a list with the <strong>Requests</strong> they have made as well as their
              <strong>status</strong>.</p>
          </div>
          <div class="col">
            <h3><a class="a">My Offers </a><img src="images/heart.png" alt="heart"></h3>
            <p>All member of CareLink can <strong>offer</strong> items to people in need.
              By declaring availability to team's announcements for <strong>shortages</strong> the volunteers
              will be responsible to tranfers the products.</p>
          </div>

          <div class="col">
            <h3><a class="a" href="#A">Shortages</a> <i class="fa fa-bullhorn" style="font-size:24px"></i></h3>
            <p>All member of our society can <strong>offer</strong> items to people in need.
              By declaring availability to team's announcements about <strong>shortages</strong> the volunteers
              undertake to transport them.</p>
          </div>

        </div>
      </div>
    </div>


    <div class="Secondsection">
      <h3 >Shortages</h3>
      <hr>
      <div class="reminder-notes">
        <?php
        if ($shortageResult->num_rows > 0) {
            echo '<ul>';
            while($row = $shortageResult->fetch_assoc()) {
                echo '<li>';       
                echo '<h2>Category: ' . htmlspecialchars($row["shortage_category"]) . '</h2>';
                echo '<p>Product Name: ' . htmlspecialchars($row["shortage_product_name"]) . '</p>';
                echo '<p>Quantity: ' . htmlspecialchars($row["shortage_quantity"]) . '</p>';
                echo '<p>Date & Time: ' . htmlspecialchars($row["shortage_datetime"]) . '</p>';  
                //echo '<button class="DeleteReq" onclick="delete_request(' . htmlspecialchars($row["id_shortage"]) . ')">Delete</button>';            
                echo '<button class="delete"  onclick="addOffer(\'' . htmlspecialchars($row["id_shortage"]) . '\', \'' . htmlspecialchars($row["shortage_category"]) . '\', \'' . htmlspecialchars($row["shortage_product_name"]) . '\', \'' . htmlspecialchars($row["shortage_quantity"]) . '\')">Make an Offer</button>';
                echo '</li>';
            }
            echo '</ul>';
        } else {
            echo '<p>No reminders available.</p>';
        }
        ?>
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
          <li>Email: <span class="email">Karagiannis.giorg@gmail.com</span></li>
          <br>
          <li>Phone: +30 123456789</li>
        </ul>
      </div>
    </div>
  </div>

  <script>
      function addOffer(shortageId, category, product, quantity) {
            //var username = document.getElementById("txtUsername").value;  // Get the username from PHP and escape it

            // JavaScript to get the username from the h2 element
            var usernameElement = document.getElementById("txtUsername");
            var username = usernameElement.textContent.trim();  // Use textContent to get the text, and trim() to remove any extra spaces


            var url = "add_offer_civilian.php";

            // Create a FormData object and append the data you want to send
            var formData = new FormData();
            formData.append("shortageId", shortageId);
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
