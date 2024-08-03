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
  $Category = $_POST['categorySelect'];
  $product = $_POST['productSelect'];
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
      $success_message = "Request was made successfully";
      // Redirect to a different page after successful form submission
      header("Location: civilian_requests.php");
      exit(); // Make sure to exit to prevent further execution of the script
  } else {
      // Handle the error
      echo "Error: " . $stmt->error;
  }

  // Close the statement
  $stmt->close();
}

// Fetch categories from the database
$sql = "SELECT distinct category_name FROM categories";
$result = $conn->query($sql);

// Fetch shortage records from the database
$shortageQuery = "SELECT * FROM shortage ORDER BY shortage_datetime DESC";
$shortageResult = $conn->query($shortageQuery);


// Close the database connection outside the if block

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
          <li><a href="Civilian.php"><i class="fa fa-home" style="font-size:24px"></i> Home</a></li>
          <p> | </p>
          <li>
            <a href="Civilian_offers.php">
            <i class="fa fa-heart" style="font-size:24px"></i> Offers
            </a>
          </li>
          <li style="margin-left:55px"><a href="logout.php"><i class="fa fa-sign-out" style="font-size:24px"></i> Log out</a></li>
      </ul>
      
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
              <br>
              <div class="col-sm-6">
                <label for="people" class="form-label">People</label>
                <input type="text" class="form-control p-2" id="people" name="People"
                  placeholder="Number of people" pattern="[0-9]{1,2}" autocomplete="on" required>
              </div>
            </div>
            <br>
            <div>
                <div class="col-sm-6">
                    <label for="categorySelect" class="form-label">Category</label>
                    <select id="categorySelect" class="form-control p-2" name="categorySelect">
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
                        $conn->close();
                        ?>
                    </select>
                    
                    <br>
                    
                    <div class="col-sm-6">
                        <label for="productSelect" class="form-label">Product</label>
                        <select id="productSelect" class="form-control p-2" name="productSelect">
                        <option value="">Select a category First</option>
                        </select>
                    </div>
                </div>
            </div>
            <?php
            if (!empty($success_message)) {
                echo "<p class='success-message'>$success_message</p>";
            }
            ?>
            <br>
            <button class="submit" type="submit">Submit</button>
            <button class="reset" type="reset" onclick="resetForm()">Reset</button>
           
          </form>
        </div>
        <div class="col-sm-3"></div>
      </div>
    </div>

    <div class="thirdsection">
        <h3 id="B" >My requests</h3>
        <hr>
        <!-- Add a div to display the user requests -->
        <div id="userRequests"></div>
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
        $(document).ready(function() {
            $('#categorySelect').change(function() {
                var category_name = $(this).val();
                $.ajax({
                    type: 'POST',
                    url: 'fetch_products.php',
                    data: {category_name: category_name},
                    dataType: 'json',
                    success: function(data) {
                        $('#productSelect').empty();
                        $('#productSelect').append('<option value="">Select Product</option>');
                        $.each(data, function(index, value) {
                            $('#productSelect').append('<option value="'+ value +'">'+ value +'</option>');
                        });
                    }
                });
            });
        });
    
        function showRequests(username) {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("userRequests").innerHTML = this.responseText;
                }
            };
            xmlhttp.open("GET", "load_civilian_requests.php?q=" + username, true);
            xmlhttp.send();
        }

        document.addEventListener("DOMContentLoaded", function () {
            // Get the default username value
            var defaultUsername = document.getElementById("txtUsername").value;

            // Call showRequests to fetch and display user requests
            showRequests(defaultUsername);
        });
    

        function deleteRequest(requestId) {
          // Example using fetch API
          fetch('delete.php?id=' + requestId, {
              method: 'GET',
          })
          .then(response => {
              if (response.ok) {
                  // Reload the page if the request was successful
                  location.reload();
              } else {
                  // Handle non-200 responses
                  console.error('Error:', response.statusText);
              }
          })
          .catch(error => {
              console.error('Error:', error);
          });
      }

  </script>
    
</body>

</html>
