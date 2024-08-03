<?php
include 'Connection.php';

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the new coordinates from the AJAX request
$latitude = $_POST['latitude'];
$longitude = $_POST['longitude'];
$base_id = 1; // Assuming you always update the same base with ID 1

// Update the base_location in the database
$sql = "UPDATE base SET base_location = '$latitude, $longitude' WHERE base_id = $base_id";

if ($conn->query($sql) === TRUE) {
    echo "Record updated successfully";
} else {
    echo "Error updating record: " . $conn->error;
}

$conn->close();
?>
