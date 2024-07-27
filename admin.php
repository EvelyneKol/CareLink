<?php
include 'Connection.php';

session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'admin') {
    header('Location: sign_in.php');
    exit();
}

// Fetch unique category names
$sql = "SELECT DISTINCT category_name FROM categories";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>CareLink Administrator</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="icon" type="image/jpg" sizes="96x96" href="images/favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="header">
        <h2><img src="images/logo.png" alt="Logo" width="200"></h2>
        <div class="h3-header">
            <h4>Back to admin actions!</h4> 
          </div>
    </div>

    <div class="navbar">
        <ul class="nav">
            <li><a class="active" href="Home.html">Home</a></li>
            <li><a href="admin_map.php">Map</a></li>
            <li><a href="base.php">Database </a></li>
        </ul>
        <ul class="nav">            
            <li><a href="home.html"><i class="fa fa-sign-out" style="font-size:24px" ></i> Log out</a></li>
        </ul>
    </div>

    <div class="Main container-fluid">
        <div class="Firstsection">
          <h2> What to do as an admin:</h2>
          <div class="container mt-5">
            <div class="row">
              <div class="col-sm-4">
                <h3><a class="a2" href="#A">Create volunteer account</a><i class='fa fa-address-card' style='font-size:24px'></i></h3> 
                <p>The administrator is the only one who can <strong>add a volunteer</strong> in our society.</p>
              </div>
              <div class="col-sm-4">
                <h3><a class="a2" href="#B">Create announcement</a> <i class="fa fa-bullhorn" style="font-size:24px"></i></h3>        
                <p>Admin creates new <strong>announcements</strong> to be displayed
                  in the application of the citizen, and concern <strong>needs</strong> for various items.</p>     
              </div>
              <div class="col-sm-4">
                <h3><a class="a2" href="base.php">Βase management</a><i class="fa fa-clipboard" style="font-size:24px"></i></h3>
                <p>The administrator is able to <strong>add categories and items</strong> as well as manage the details of all the items to be delivered.</p>
              </div>
              <div class="col-sm-4">
                <h3><a class="a2" href="#C">Base status</a><i class="fa fa-folder-open" style="font-size:24px"></i></h3>        
                <p>The administrator sees a <strong>detailed status</strong> of all <strong>items</strong>, whether they are in the base or loaded on vehicles,filtered based on item categories.
                </p>     
              </div>
              <div class="col-sm-4">
                <h3><a class="a2" href="admin_map.php">Maps</a><i class="fa fa-map" style="font-size:24px"></i></h3>        
                <p>With different <strong>markers</strong>, the base, the location of every
                  rescue vehicle, the requests and offers not completed can be seen in the map.</p>     
              </div>
              <div class="col-sm-4">
                <h3><a class="a2" href="#D">Service statistics</a><i class="fa fa-pie-chart" style="font-size:24px"></i></h3>        
                <p>The administrator sees a <strong>graph</strong> depicting the number of
                  Requests, new Offers, completed Requests and Offers in a time period he wants.</p>     
              </div>             
            </div>
          </div>
        </div>
        
    </div>

    <div class="Forthsection">
        <div class="row">
            <div id= 'A' class="col4 col-sm-6">
                <h2>Create a volunteer account</h2>
                <button class="add"><a href="sign_up_volunteer.php">+ Volunteer</a></button>
            </div>
            <div id= 'B' class="col5 col-sm-6">
                <h2>Create announcement for items in sortage</h2>
                <button class="add"><a href="announcement.php">+ Announcement</a></button>
            </div>
        </div>
    </div>

    <div class="Secondsection">
      <div id='C'>
          <hr>
          <h2>Base Status</h2>
          <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
              <select name="category">
              <option value="">Select a category</option>
                  <?php
                  if ($result->num_rows > 0) {
                      while ($row = $result->fetch_assoc()) {
                          echo "<option value='" . $row["category_name"] . "'>" . $row["category_name"] . "</option>";
                      }
                  } else {
                      echo "<option value=''>No categories found</option>";
                  }
                  ?>
              </select>
              <input type="submit" name="submit" value="Submit">
          </form>

          <?php
          // Process form submission
          if ($_SERVER["REQUEST_METHOD"] == "POST") {
              $selected_category = $_POST["category"];
              if (!empty($selected_category)) {
                  // Fetch data based on selected category
                  $sql = "SELECT * FROM categories WHERE category_name = ?";
                  $stmt = $conn->prepare($sql);
                  $stmt->bind_param("s", $selected_category);
                  $stmt->execute();
                  $result = $stmt->get_result();

                  if ($result->num_rows > 0) {
                      echo "<h2>Product status for $selected_category Category</h2>";
                      echo "<table border='1'>";
                      echo "<tr><th>Product</th><th>Quantity on Stock</th><th>Quantity on Truck</th></tr>";
                      while ($row = $result->fetch_assoc()) {
                          echo "<tr>";
                          echo "<td>" . $row["products"] . "</td>";
                          echo "<td>" . $row["quantity_on_stock"] . "</td>";
                          echo "<td>" . $row["quantity_on_truck"] . "</td>";
                          echo "</tr>";
                      }
                      echo "</table>";
                  } else {
                      echo "No data available for selected category.";
                  }
                  $stmt->close();
              } else {
                  echo "Please select a category.";
              }
          }
          ?>

          <?php?>
      </div>
    </div>

    <div id="D" class="Fifthsection">
        <h2>Service statistics</h2>
        <h6>Find the number of New (Waiting) and Completed Requests and Offers in a specific time period</h6>
        <br>
        <form id="filterForm">
            <label for="start_date">Start Date:</label>
            <input type="date" id="start_date" name="start_date" required>
            <label for="end_date">End Date:</label>
            <input type="date" id="end_date" name="end_date" required>
            <button type="submit">Apply</button>
        </form>
        <div style="width: 70%; margin: auto;">
          <canvas id="statisticsChart"></canvas>
        </div>
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


<script>
        $(document).ready(function() {
            var ctx = document.getElementById('statisticsChart').getContext('2d');
            var statisticsChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Waiting Offers', 'Completed Offers', 'Waiting Requests', 'Completed Requests'],
                    datasets: [{
                        label: 'Count',
                        data: [0, 0, 0, 0],
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            $('#filterForm').on('submit', function(e) {
                e.preventDefault();
                var startDate = $('#start_date').val();
                var endDate = $('#end_date').val();
                $.ajax({
                    type: 'POST',
                    url: 'fetch_statistics_data.php',
                    data: {
                        startDate: startDate,
                        endDate: endDate
                    },
                    dataType: 'json',
                    success: function(response) {
                        statisticsChart.data.datasets[0].data = [
                            response.WaitingOffers,
                            response.CompletedOffers,
                            response.WaitingRequests,
                            response.CompletedRequests
                        ];
                        statisticsChart.update();
                    }
                });
            });
        });
    </script>

</body>

</html>