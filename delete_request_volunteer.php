<?php
//σύνδεση με mySQL βαση
include 'Connection.php';

// Έλεγχος αν η σύνδεση με τη βάση δεδομένων ήταν επιτυχής
if ($conn->connect_error) {
    die("Failed to connect to MySQL: " . $conn->connect_error);
}

// Λήψη παραμέτρων
$requestId = $_POST['requestId'];

// Διαγραφή αιτήματος απο τα task του εθελοντή 
$deleteoffer = $conn->prepare("DELETE FROM task where task_request_id = ? ");
$deleteoffer->bind_param("i",  $requestId);
$deleteoffer->execute();

//Ενημέρωση του  αιτήματος σε WAITING 
$updateoffer = $conn->prepare("UPDATE request SET state = 'WAITING' WHERE id_request = ?");
$updateoffer->bind_param("i", $requestId);
$updateoffer->execute();


// κλείσιμο της σύνδεσης με την βάση 
$conn->close();
?>