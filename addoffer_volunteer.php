<?php
//σύνδεση με mySQL βαση
include 'Connection.php';

// Έλεγχος αν η σύνδεση με τη βάση δεδομένων ήταν επιτυχής
if ($conn->connect_error) {
    die("Failed to connect to MySQL: " . $conn->connect_error);
}

// Λήψη παραμέτρων
$offerId = $_POST['offerId'];
$username = $_POST['username'];

//Ανάκτηση του πολήτη που έκανε το Offer
$selectQuery = $conn->prepare("SELECT offer_civilian FROM offer WHERE offer_id = ?");
$selectQuery->bind_param("i", $offerId);
$selectQuery->execute();
$selectResult = $selectQuery->get_result();
$row = $selectResult->fetch_assoc();
$offerCivilian = $row['offer_civilian'];
$date = date("Y-m-d");
date_default_timezone_set("Europe/Athens");
$time = date("H:i:s"); 

// Εισαγωγή του Offer στα task
$insertQuery = $conn->prepare("INSERT INTO task (task_volunteer, donator, task_offer_id, task_date, task_time) VALUES (?, ?, ?, ?, ?)");
$insertQuery->bind_param("ssiss", $username, $offerCivilian, $offerId, $date, $time);
$insertQuery->execute();

//Ανανέωση του Offer σε ON THE WAY
$updateQuery = $conn->prepare("UPDATE offer SET offer_status = 'ON THE WAY' WHERE offer_id = ?");
$updateQuery->bind_param("i", $offerId);
$updateQuery->execute();


// κλείσιμο της σύνδεσης με την βάση
$conn->close();
?>
