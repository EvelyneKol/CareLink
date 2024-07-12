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

// Check if the username matches the request_civilian
// Prepare and execute the insert query
$deleteoffer = $conn->prepare("DELETE FROM task where task_volunteer = ? and task_request_id = ? ");
$deleteoffer->bind_param("si", $username,  $requestId);
$deleteoffer->execute();

// Prepare and execute the update query
$updateoffer = $conn->prepare("UPDATE request SET state = 'WAITING' WHERE id_request = ?");
$updateoffer->bind_param("i", $requestId);
$updateoffer->execute();


// Close the database connection
$conn->close();
?>