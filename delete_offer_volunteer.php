<?php
include 'Connection.php';

// Check connection
if ($conn->connect_error) {
    die("Failed to connect to MySQL: " . $conn->connect_error);
}

// Get data from the AJAX request
$offerId = $_POST['offerId'];
// Check if the username matches the request_civilian 
// Prepare and execute the insert query
$deleteoffer = $conn->prepare("DELETE FROM task where task_offer_id = ? ");
$deleteoffer->bind_param("i",  $offerId);
$deleteoffer->execute();

// Prepare and execute the update query
$updateoffer = $conn->prepare("UPDATE offer SET offer_status = 'WAITING' WHERE offer_id = ?");
$updateoffer->bind_param("i", $offerId);
$updateoffer->execute();


// Close the database connection
$conn->close();
?>