<?php
include 'Connection.php';

// Check connection
if ($conn->connect_error) {
    die("Failed to connect to MySQL: " . $conn->connect_error);
}

// Get data from the AJAX request
$shortageId = $_POST['shortageId'];
$username = $_POST['username'];
$offerCategory = $_POST['category'];
$offerProduct = $_POST['product'];
$offerQuantity = (int)$_POST['quantity']; // Ensure quantity is an integer

// Set date and time
date_default_timezone_set("Europe/Athens");
$date = date("Y-m-d");
$time = date("H:i:s"); 

$status = "WAITING";

// Insert the offer into the database
$insertQuery = $conn->prepare("INSERT INTO offer (offer_civilian, offer_category, offer_product_name, offer_quantity, offer_date_posted, offer_time_posted, offer_status) VALUES (?, ?, ?, ?, ?, ?, ?)");
$insertQuery->bind_param("sssisss", $username, $offerCategory, $offerProduct, $offerQuantity, $date, $time, $status);

if ($insertQuery->execute()) {
    // Delete the shortage entry
    $deleteQuery = $conn->prepare("DELETE FROM shortage WHERE id_shortage = ?");
    $deleteQuery->bind_param("i", $shortageId);
    $deleteQuery->execute();

    echo "Offer added and shortage deleted successfully.";
} else {
    echo "Error: " . $insertQuery->error;
}

// Close the database connection
$conn->close();
?>
