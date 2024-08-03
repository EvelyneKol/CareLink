<?php
include 'Connection.php';

// Check connection
if ($conn->connect_error) {
    die("Failed to connect to MySQL: " . $conn->connect_error);
}

// Get data from the AJAX request
$OfferId = (int)$_POST['OfferId'];
$offerCategory = $_POST['category'];
$offerProduct = $_POST['product'];
$offerQuantity = (int)$_POST['quantity']; // Ensure quantity is an integer

// Set date and time
date_default_timezone_set("Europe/Athens");
$date = date("Y-m-d");
$time = date("H:i:s"); 

// Combine date and time into a single DateTime object
$dateTime = $date . ' ' . $time;

// Insert the offer into the database
$insertQuery = $conn->prepare("DELETE FROM offer WHERE offer_id =?");
$insertQuery->bind_param("i", $OfferId);

if ($insertQuery->execute()) {
    // Delete the shortage entry
    $deleteQuery = $conn->prepare("INSERT INTO shortage VALUES (NULL, ?, ?, ?, ?)");
    $deleteQuery->bind_param("ssis", $offerCategory,$offerProduct,$offerQuantity,$dateTime);
    $deleteQuery->execute();
} else {
    echo "Error: " . $insertQuery->error;
}

// Close the database connection
$conn->close();
?>