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

// Check if the username matches the request_civilian
// Prepare and execute the insert query
$deleteoffer = $conn->prepare("DELETE FROM task where task_volunteer = ? and task_offer_id = ? ");
$deleteoffer->bind_param("si", $username,  $offerId);
$deleteoffer->execute();

// Prepare and execute the update query
$updateoffer = $conn->prepare("UPDATE offer SET offer_status = 'WAITING' WHERE offer_id = ?");
$updateoffer->bind_param("i", $offerId);
$updateoffer->execute();


// Close the database connection
$conn->close();
?>