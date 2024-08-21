<?php
include 'Connection.php';

// έλεγχος σύνδεσης
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Με AJAX request πάρε τις νέες συντεταγμένες της βάσης απο τον χάρτη
$latitude = $_POST['latitude'];
$longitude = $_POST['longitude'];
$base_id = 1; // ανενέωση της υπάρχουσας βάσης με ID 1

// ανενέωσε το base_location στον πίνακας της βάσης 
$sql = "UPDATE base SET base_location = '$latitude, $longitude' WHERE base_id = $base_id";

//μήνυμα επιτυχίας ή αποτυχίας
if ($conn->query($sql) === TRUE) {
    echo "Record updated successfully";
} else {
    echo "Error updating record: " . $conn->error;
}

$conn->close();
?>
