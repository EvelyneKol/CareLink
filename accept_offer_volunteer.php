<?php
include 'Connection.php';

// Check connection
if ($conn->connect_error) {
    die("Failed to connect to MySQL: " . $conn->connect_error);
}


$date = date("Y-m-d");
date_default_timezone_set("Europe/Athens");

// Get data from the AJAX request
$offerId = $_POST['offerId'];
$category = $_POST['category'];
$product = $_POST['product'];
$quantity = (int)$_POST['quantity']; // Ensure quantity is an integer
$latitude = $_POST['latitude'];
$longitude = $_POST['longitude'];
$username = $_POST['username'];

$vehicle_location = $latitude . ', ' . $longitude;

// Prepare and execute the update query for the offer
$updaterequest = $conn->prepare("UPDATE offer SET offer_status = 'COMPLETED' AND complete_offer = ? WHERE offer_id = ?");
$updaterequest->bind_param("si", $date, $offerId);
$updaterequest->execute();
$updaterequest->close();

// Prepare and execute the delete query for the task
$updatetasks = $conn->prepare("DELETE FROM task WHERE task_offer_id = ?");
$updatetasks->bind_param("i", $offerId);
$updatetasks->execute();
$updatetasks->close();

// Prepare and execute the select query to check the current quantity
$stmtCheck = $conn->prepare("SELECT quantity FROM vehiclesOnAction WHERE category = ? AND products = ? AND driver = ?");
$stmtCheck->bind_param("sss", $category, $product, $username);
$stmtCheck->execute();
$stmtCheck->store_result();

if ($stmtCheck->num_rows > 0) {
    $stmtCheck->bind_result($currentQuantity);
    $stmtCheck->fetch();
    $newQuantity = $currentQuantity + $quantity;

    // Prepare and execute the update query for the quantity
    $stmtUpdate = $conn->prepare("UPDATE vehiclesOnAction SET quantity = ? WHERE category = ? AND products = ? AND driver = ?");
    $stmtUpdate->bind_param("isss", $newQuantity, $category, $product, $username);
    $stmtUpdate->execute();
    $stmtUpdate->close();
} else {
    // Prepare and execute the select query to get the vehicle name
    $vehicleCheck = $conn->prepare("SELECT DISTINCT v_name FROM vehiclesOnAction WHERE driver = ?");
    $vehicleCheck->bind_param("s", $username);
    $vehicleCheck->execute();
    $vehicleCheck->bind_result($vehicle);
    $vehicleCheck->fetch();
    $vehicleCheck->close();

    // Prepare and execute the insert query
    $stmtInsert = $conn->prepare("INSERT INTO vehiclesOnAction (v_name, driver, products, quantity, category, vehicle_location) VALUES (?, ?, ?, ?, ?, ?)");
    $stmtInsert->bind_param("sssiss", $vehicle, $username, $product, $quantity, $category, $vehicle_location);
    $stmtInsert->execute();
    $stmtInsert->close();
}

// Close the select statement
$stmtCheck->close();

// Close the database connection
$conn->close();
?>