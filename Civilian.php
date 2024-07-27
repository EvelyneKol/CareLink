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
  $username = $_POST['username'];
  $people = (int)$_POST['People']; 
  $Category = $_POST['Category'];
  $product = $_POST['product'];
  $date = date("Y-m-d"); // Change the format to match the database column type
  date_default_timezone_set("Europe/Athens");
  $time = date("H:i:s"); // Format as "hour:minute:second

  // Debugging statement
  echo "Category: " . $Category;

  // Insert requests into 'requests' table
  $stmt = $conn->prepare("INSERT INTO request (request_civilian, request_category, request_product_name, persons, request_date_posted, request_time_posted) VALUES (?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("sssiss", $username, $Category, $product, $people, $date, $time);

  // Execute the statement
  if ($stmt->execute()) {
      // Redirect to a different page after successful form submission
      header("Location: Civilian.php");
      exit(); // Make sure to exit to prevent further execution of the script
  } else {
      // Handle the error
      echo "Error: " . $stmt->error;
  }

  // Close the statement
  $stmt->close();
}

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
  <link rel="stylesheet" href="css/Civilian.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>
  <div class="header container-fluid">
    <p><img src="images/logo.png" alt="Logo" width="200"></p>
    <a href="home.html" class="a1"><i class="fa fa-sign-out" style="font-size:24px"></i>Log out</a>
  </div>

  <div class="Main container-fluid">
    <div class="Firstsection">
      <h2> Civilian members</h2>
      <div class="container mt-5">
        <div class="row">
          <div class="col">
            <h3><a class="a" href="#A">Requests </a><img src="images/request.png" alt="heart"></h3>
            <p>A map with all the tasks available,
              the <strong>tasks</strong> you have taken with their <strong>route</strong> and the
              location of the <strong>store</strong> in available for you.</p>
          </div>
          <div class="col">
            <h3><a class="a" href="#B">Offer </a><img src="images/heart.png" alt="heart"></h3>
            <p>All member of our society can <strong>offer</strong> items to people in need.
              By declaring availability to team's announcements about <strong>shortages</strong> the volunteers
              undertake to transport them.</p>
          </div>
        </div>
      </div>
    </div>

    <div class="Secondsection">
      <h3 id="A">Make a request!</h3>
      <hr>
      <p>Hey there! In this section, you have the chance to submit a form with all the
        products that you need.
        Quick reminder: stick to one product per category each time.
        But guess what? You're not limited by quantity,
        so go ahead and submit as many forms as your heart desires!</p>
      <div class="row">
        <div class="col-sm-3"></div>
        <div class="req col-sm-6">
        <form id="requestForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" onsubmit="resetForm()">
            <div class="row">
              <div class="col-sm-6">
                <label for="txtUsername" class="form-label">Username</label>
                <input type="text" class="form-control p-2" id="txtUsername" name="username"
                placeholder="Write your Username..." autocomplete="on" required value="<?php echo $defaultUsername; ?>" readonly>
              </div>
              <div class="col-sm-6">
                <label for="people" class="form-label">People</label>
                <input type="text" class="form-control p-2" id="people" name="People"
                  placeholder="Number of people" pattern="[0-9]{1,2}" autocomplete="on" required>
              </div>
            </div>
            <br>
            <div>
              <label for="Category" class="form-label">Category</label>
              <select id="Category" class="form-control p-2" name="Category">
                <option value="Sanitation" selected>Toilet paper</option>
                <option value="Sanitation">Feminine supplies</option>
                <option value="Sanitation">Paper</option>
                <option value="Food">Oil</option>
                <option value="Food">Rice</option>
                <option value="Dairy">Dairy</option>
              </select>
            </div>
            <br>
            <div>
              <label for="product" class="form-label">Product</label>
              <input type="text" class="form-control p-2" id="product" name="product"
                placeholder="Insert your product..." autocomplete="on" required>
            </div>
            <button class="submit" type="submit">Submit</button>
            <button class="reset" type="reset" onclick="resetForm()">Reset</button>
          </form>
        </div>
        <div class="col-sm-3"></div>
      </div>
    </div>

    <div class="thirdsection">
        <h3>My requests</h3>
        <hr>
        <!-- Add a div to display the user requests -->
        <div id="userRequests"></div>
    </div>


    <div class="forthsection">
        <h3>Offers</h3>
        <hr>
        <!-- Add a div to display the user requests -->
        <div></div>
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
        function showRequests(username) {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("userRequests").innerHTML = this.responseText;
                }
            };
            xmlhttp.open("GET", "load_requests.php?q=" + username, true);
            xmlhttp.send();
        }

        document.addEventListener("DOMContentLoaded", function () {
            // Get the default username value
            var defaultUsername = document.getElementById("txtUsername").value;

            // Call showRequests to fetch and display user requests
            showRequests(defaultUsername);
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
