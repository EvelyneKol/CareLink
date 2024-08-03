<!DOCTYPE html>
<html>
<head>
<style>
*{  font-family: Arial, Helvetica, sans-serif;}

 /*Second section*/
.Secondsection {
  margin-top: 20px;
  font-family: 'Work Sans', sans-serif;
  float: center;
}

.Secondsection h3,
.Secondsection p {
  margin: 10px 0px 5px 0px;
  text-align: center;
}

.reminder-notes {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  align-items: center;
  gap: 20px; /* Adjust gap between items as needed */
}

.reminder-notes ul {
  display: flex;
  flex-wrap: wrap;
  list-style-type: none; /* Remove default list styling */
  padding: 0;
  display: flex;
  flex-wrap: wrap;
  text-align: center;
}

.reminder-notes ul li {
  background-color: #faf3cd; /* Yellow background */
  color: black; /* Text color */
  border: 1px solid #ccc; /* Border color */
  border-radius: 5px; /* Rounded corners */
  padding: 20px;
  margin: 20px;
  width: 240px; /* Fixed width */
  height: 280px; /* Fixed height */
  overflow: hidden; /* Hide overflow text */
  box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1); /* Optional: add a slight shadow */
  display: flex;
  flex-direction: column;
  justify-content: space-between; /* Distribute space evenly */
}

.reminder-notes ul li a {
  text-decoration: none; /* Remove underline from links */
  color: inherit; /* Inherit text color */
}

.reminder-notes ul li h2 {
  font-size: 1.2em; /* Adjust title font size */
  margin: 0;
  padding: 0;
}

.reminder-notes ul li p {
  font-size: 0.9em; /* Adjust text font size */
  margin: 5px 0 0 0;
  padding: 0;
}

.delete {
  margin-top: 20px;
  padding: 5px 14px;
  border-radius: 4px;
  background-color: rgba(221, 221, 221, 0.662);
  color: black;
  border: none;
  transition-duration: 0.4s;
}

.delete button{
  margin-top:5px;
  float:right;
}

.delete:active,
.delete:hover {
  background-color: rgb(0, 146, 195);
  color: rgb(255, 255, 255);
}


</style>
</head>
<body>
  
<div class="Secondsection">
  <div class="reminder-notes">
    <?php
    include 'Connection.php';

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }


    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $username = $_GET['q'];

        $sql = "SELECT * FROM request WHERE request_civilian = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);

        $result = $stmt->execute();

        if (!$result) {
            die("Error: " . $stmt->error);
        }

        $stmt->bind_result($id_request, $request_civilian, $request_category, $request_product_name, $persons, $request_date_posted, $request_time_posted, $state, $complete_request);
        echo '<ul>';
        // Loop through the records and generate HTML for each
        while ($stmt->fetch()) {
            echo '<li>';  
            echo '<h2 class="title"> Your request from <strong>' . htmlspecialchars($request_category) . '</strong></h2>';
            echo '<p> Product: ' . htmlspecialchars($request_product_name) . '</p>';
            echo '<p> Num of People: ' . htmlspecialchars($persons) . '</p>';
            echo '<p> Date Posted: ' . htmlspecialchars($request_date_posted) . '</p>';
            echo '<p> Time Posted: ' . htmlspecialchars($request_time_posted) . '</p>';
            if($state == "WAITING") {
              echo '<p> State of request: <strong>' . htmlspecialchars($state) . '</strong></p>';
              echo '<button class="delete" onclick="deleteRequest(' . $id_request . ')">Delete</button>';
            } else if($state == "ON THE WAY") {
              echo '<p> State of request: <strong>' . htmlspecialchars($state) . '</strong></p>';
            } else if($state == "CANCELED") {
              echo '<p> State of request: <strong>' . htmlspecialchars($state) . '</strong></p>';
            }else{
              echo '<p> Time Completed: ' . htmlspecialchars($complete_request) . '</p>';
              echo '<p> State of request: <strong>' . htmlspecialchars($state) . '</strong></p>';
            }
            echo '</li>';
        }
        echo '</ul>';

        $stmt->close();
    }

    $conn->close();

    ?>
  </div>
</div>
</body>
</html>
