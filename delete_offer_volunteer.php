<?php
//σύνδεση με mySQL βαση
include 'Connection.php';

// Έλεγχος αν η σύνδεση με τη βάση δεδομένων ήταν επιτυχής
if ($conn->connect_error) {
    die("Failed to connect to MySQL: " . $conn->connect_error);
}

// Λήψη παραμέτρων
$offerId = $_POST['offerId'];

//Διαγραφή προσφορών απο τα task 
$deleteoffer = $conn->prepare("DELETE FROM task where task_offer_id = ? ");
$deleteoffer->bind_param("i",  $offerId);
$deleteoffer->execute();

//Ενημέρωση προσφορών σε WAITING
$updateoffer = $conn->prepare("UPDATE offer SET offer_status = 'WAITING' WHERE offer_id = ?");
$updateoffer->bind_param("i", $offerId);
$updateoffer->execute();


// κλείσιμο της σύνδεσης με την βάση 
$conn->close();
?>