<?php
include 'Connection.php';

// Check connection
if ($conn->connect_error) {
    die("Failed to connect to MySQL: " . $conn->connect_error);
}

// Get data from the AJAX request
$id_request = $_POST['id_request'];
$username = $_POST['username'];

// Prepare and execute the select query
$selectQuery = $conn->prepare("SELECT request_civilian FROM request WHERE id_request = ?");
$selectQuery->bind_param("i", $id_request);
$selectQuery->execute();
$selectResult = $selectQuery->get_result();
$row = $selectResult->fetch_assoc();
$requestCivilian = $row['request_civilian'];

$date = date("Y-m-d");
date_default_timezone_set("Europe/Athens");
$time = date("H:i:s"); 

// Prepare and execute the insert query
$insertQuery = $conn->prepare("INSERT INTO task (task_volunteer, receiver, task_request_id, task_date, task_time) VALUES (?, ?, ?, ?, ?)");
$insertQuery->bind_param("ssiss", $username, $requestCivilian, $id_request, $date, $time);
$insertQuery->execute();

// Prepare and execute the update query
$updateQuery = $conn->prepare("UPDATE request SET state = 'ON THE WAY' WHERE id_request = ?");
$updateQuery->bind_param("i", $id_request);
$updateQuery->execute();

// Close the database connection
$conn->close();

echo "Success"; // Send a success response
?>
