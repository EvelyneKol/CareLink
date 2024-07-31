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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
} 

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
  <link rel="icon" sizes="96x96" href="images/favicon.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="Civilian.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>

<body>

  <div class="navbar">
      <img src="images/logo1.png" alt="Logo">
      <ul class="nav">
          <li><a href="Civilian.php"><i class="fa fa-home" style="font-size:24px"></i> Home</a></li>
          <p> | </p>
          <li>
            <a href="civilian_requests.php">
              <i class="fa fa-bullhorn" style="font-size:24px"></i>  Requests
            </a>
          </li>
      </ul>
      <ul class="nav">
          <li><a href="logout.php"><i class="fa fa-sign-out" style="font-size:24px"></i> Log out</a></li>
      </ul>
  </div>

    <div class="Secondsection">
        <h2 id="txtUsername" style="display: none;"><?php echo $defaultUsername; ?></h2>
    </div>

    <div class="thirdsection">
        <h3>My offers</h3>
        <hr>
        <div id="userOffers"></div>
    </div>

    <div class="thirdsection">
        <h3>Completed offers</h3>
        <hr>
        <div id="pastOffers"></div>
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
        function showOffers(username) {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("userOffers").innerHTML = this.responseText;
                }
            };
            xmlhttp.open("GET", "load_civilian_offers.php?q=" + username, true);
            xmlhttp.send();
        }

        document.addEventListener("DOMContentLoaded", function () {
            // Get the default username value
            //var defaultUsername = document.getElementById("txtUsername");
            var defaultUsername = document.getElementById('txtUsername').innerText;
      
            // Call showOffers to fetch and display user requests
            showOffers(defaultUsername);
        });

        function showPastOffers(username) {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("pastOffers").innerHTML = this.responseText;
                }
            };
            xmlhttp.open("GET", "load_past_Offers_civilian.php?q=" + username, true);
            xmlhttp.send();
        }

        document.addEventListener("DOMContentLoaded", function () {
            // Get the default username value
            //var defaultUsername = document.getElementById("txtUsername");
            var defaultUsername = document.getElementById('txtUsername').innerText;
      
            // Call showOffers to fetch and display user requests
            showOffers(defaultUsername);
        });
    

    
    

        function deleteRequest(requestId) {
    // You can use AJAX to send a request to a PHP file that will delete the row from the database
    // Example using fetch API
        fetch('delete.php?id=' + requestId, {
               method: 'GET',
        })
        .then(data => {
            // Handle the response if needed
            console.log(data);
            // Optionally, you can remove the HTML element for the deleted request
            var cardElement = document.getElementById('card_' + requestId);
            if (cardElement) {
                cardElement.remove();
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
  }
  </script>
    
</body>

</html>
