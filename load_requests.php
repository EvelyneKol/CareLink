<!DOCTYPE html>
<html>
<head>
<style>
*{  font-family: Arial, Helvetica, sans-serif;}

.album-container {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  align-items: center;
  gap: 5px;
  flex-direction: row;
}

.coloured-cards .card {
  margin-top: 30px;
}

.card {
  list-style-type: none; /* Remove default list styling */
  flex-wrap: wrap;
  background-color: #faf3cd; /* Yellow background */
  color: black; /* Text color */
  border: 1px solid #ccc; /* Border color */
  border-radius: 5px; /* Rounded corners */
  padding: 20px;
  height: 280px; /* Fixed height */
  overflow: hidden; /* Hide overflow text */
  box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1); /* Optional: add a slight shadow */
  justify-content: space-between; 
  position: relative;
  width: 250px;
}

h4, .h4 {
  text-align:center;
  font-size: 1.2em;
  margin: 10px 0; /* Adjusted margin to separate the heading from other content */
}

p{
  font-size: 0.9em; /* Adjust text font size */
  margin: 5px 0 0 0;
  padding: 0;
}

.card button{
  margin-top:5px;
  float:right;
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

.delete:active,
.delete:hover {
  background-color: rgb(0, 146, 195);
  color: rgb(255, 255, 255);
}

.content-card {
  margin-top: 20px;
}

</style>
</head>
<body>

<div class="album-container">

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

    $stmt->bind_result($id_request, $request_civilian, $request_category, $request_product_name, $persons, $request_date_posted, $request_time_posted, $state);

    // Loop through the records and generate HTML for each
    while ($stmt->fetch()) {
        echo '<div class="col-md-4 col-sm-6 content-card" id="card_' . $id_request . '">';
        echo '<div class="card">';
        echo '<div class="content">';
        echo '<h4 class="title"> Your request from ' . htmlspecialchars($request_category) . '</h4>';
        echo '<p> Product: ' . htmlspecialchars($request_product_name) . '</p>';
        echo '<p> Num of People: ' . htmlspecialchars($persons) . '</p>';
        echo '<p> Date Posted: ' . htmlspecialchars($request_date_posted) . '</p>';
        echo '<p> Time Posted: ' . htmlspecialchars($request_time_posted) . '</p>';
        if($state == "WAITING") {
            echo '<p> State: ' . htmlspecialchars($state) . '</p>';
            echo '<button class="delete" onclick="deleteRequest(' . $id_request . ')">Delete</button>';
        } else {
            echo '<p> State: ' . htmlspecialchars($state) . '</p>';
        }
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }

    $stmt->close();
}

$conn->close();

?>

</div>
</body>
</html>
