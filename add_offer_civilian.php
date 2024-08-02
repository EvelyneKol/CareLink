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
$offerProduct =$_POST['product'];
$offerQuantity =$_POST['quantity'];


$date = date("Y-m-d");
date_default_timezone_set("Europe/Athens");
$time = date("H:i:s"); 

$status = "WAITING";


// Check if the username matches the request_civilian
// Prepare and execute the insert query
$insertQuery = $conn->prepare("INSERT INTO offer VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, NULL)");
$insertQuery->bind_param("sssisss", $username, $offerCategory, $offerProduct, $offerQuantity, $date, $time, $status);
$insertQuery->execute();

// Prepare and execute the update query
$deleteQuery = $conn->prepare("DELETE from shortage WHERE id_shortage = ?");
$deleteQuery->bind_param("i", $shortageId);
$deleteQuery->execute();


// Close the database connection
$conn->close();
?>
