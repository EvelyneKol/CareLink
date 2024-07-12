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

// Get data from the AJAX request
$requestId = $_POST['requestId'];
$username = $_POST['username'];
$date = date("Y-m-d");
date_default_timezone_set("Europe/Athens");
$time = date("H:i:s"); 

// Check if the username matches the request_civilian
// Prepare and execute the insert query
$updatetasks = $conn->prepare("UPDATE task SET task_date = ?, task_time = ?  WHERE task_request_id = ?");
$updatetasks->bind_param("ssi", $date, $time, $requestId);
$updatetasks->execute();

// Prepare and execute the update query
$updaterequest = $conn->prepare("UPDATE request SET state = 'COMPLETED' WHERE id_request = ?");
$updaterequest->bind_param("i", $requestId);
$updaterequest->execute();


// Close the database connection
$conn->close();
?>
