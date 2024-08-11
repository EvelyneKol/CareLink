<?php
//σύνδεση με mySQL βαση
include 'Connection.php';

// Έλεγχος αν η σύνδεση με τη βάση δεδομένων ήταν επιτυχής
if ($conn->connect_error) {
    die("Failed to connect to MySQL: " . $conn->connect_error);
}

// Λήψη παραμέτρων
$id_request = $_POST['id_request'];
$username = $_POST['username'];

//Ανάκτηση του πολήτη που έκανε το Request
$selectQuery = $conn->prepare("SELECT request_civilian FROM request WHERE id_request = ?");
$selectQuery->bind_param("i", $id_request);
$selectQuery->execute();
$selectResult = $selectQuery->get_result();
$row = $selectResult->fetch_assoc();
$requestCivilian = $row['request_civilian'];
$date = date("Y-m-d");
date_default_timezone_set("Europe/Athens");
$time = date("H:i:s"); 

// Εισαγωγή του Request στα task 
$insertQuery = $conn->prepare("INSERT INTO task (task_volunteer, receiver, task_request_id, task_date, task_time) VALUES (?, ?, ?, ?, ?)");
$insertQuery->bind_param("ssiss", $username, $requestCivilian, $id_request, $date, $time);
$insertQuery->execute();

//Ανανέωση του Request σε ON THE WAY
$updateQuery = $conn->prepare("UPDATE request SET state = 'ON THE WAY' WHERE id_request = ?");
$updateQuery->bind_param("i", $id_request);
$updateQuery->execute();

// κλείσιμο της σύνδεσης με την βάση
$conn->close();
?>
