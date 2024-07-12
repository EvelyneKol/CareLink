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
$offerId = $_POST['offerId'];
$username = $_POST['username'];

// Prepare and execute the select query
$selectQuery = $conn->prepare("SELECT offer_civilian FROM offer WHERE offer_id = ?");
$selectQuery->bind_param("i", $offerId);
$selectQuery->execute();
$selectResult = $selectQuery->get_result();
$row = $selectResult->fetch_assoc();
$offerCivilian = $row['offer_civilian'];
$date = date("Y-m-d");
date_default_timezone_set("Europe/Athens");
$time = date("H:i:s"); 


// Check if the username matches the request_civilian
// Prepare and execute the insert query
$insertQuery = $conn->prepare("INSERT INTO task (task_volunteer, donator, task_offer_id, task_date, task_time) VALUES (?, ?, ?, ?, ?)");
$insertQuery->bind_param("ssiss", $username, $offerCivilian, $offerId, $date, $time);
$insertQuery->execute();

// Prepare and execute the update query
$updateQuery = $conn->prepare("UPDATE offer SET offer_status = 'ON THE WAY' WHERE offer_id = ?");
$updateQuery->bind_param("i", $offerId);
$updateQuery->execute();


// Close the database connection
$conn->close();
?>
