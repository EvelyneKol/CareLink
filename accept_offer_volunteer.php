<?php
//σύνδεση με mySQL βαση
include 'Connection.php';

// Έλεγχος αν η σύνδεση με τη βάση δεδομένων ήταν επιτυχής
if ($conn->connect_error) {
    die("Failed to connect to MySQL: " . $conn->connect_error);
}


// Λήψη παραμέτρων 
$offerId = $_POST['offerId'];
$category = $_POST['category'];
$product = $_POST['product'];
$quantity = (int)$_POST['quantity']; 
$latitude = $_POST['latitude'];
$longitude = $_POST['longitude'];
$username = $_POST['username'];
$vehicle_location = $latitude . ', ' . $longitude;
$date = date("Y-m-d");
date_default_timezone_set("Europe/Athens");

// Ενημερώνουμε την προσφορά σε COMPLETED
$updaterequest = $conn->prepare("UPDATE offer SET offer_status = 'COMPLETED', complete_offer = ? WHERE offer_id = ?");
$updaterequest->bind_param("si", $date, $offerId);
$updaterequest->execute();
$updaterequest->close();

//Διαγραφή της προσφοράς απο τα task 
$updatetasks = $conn->prepare("DELETE FROM task WHERE task_offer_id = ?");
$updatetasks->bind_param("i", $offerId);
$updatetasks->execute();
$updatetasks->close();

//Ελέγχουμε αν το προιον είναι στο όχημα
$stmtCheck = $conn->prepare("SELECT quantity FROM vehiclesOnAction WHERE category = ? AND products = ? AND driver = ?");
$stmtCheck->bind_param("sss", $category, $product, $username);
$stmtCheck->execute();
$stmtCheck->store_result();
//Αν ναι τότε:
if ($stmtCheck->num_rows > 0) {
    $stmtCheck->bind_result($currentQuantity);
    $stmtCheck->fetch();
    $newQuantity = $currentQuantity + $quantity;

    // Ενημερώνουμε το οχήμα με το προιον 
    $stmtUpdate = $conn->prepare("UPDATE vehiclesOnAction SET quantity = ? WHERE category = ? AND products = ? AND driver = ?");
    $stmtUpdate->bind_param("isss", $newQuantity, $category, $product, $username);
    $stmtUpdate->execute();
    $stmtUpdate->close();
//Αν όχι τότε:
} else {
    //Ελέγχουμε το όνομα του φορτηγού
    $vehicleCheck = $conn->prepare("SELECT DISTINCT v_name FROM vehiclesOnAction WHERE driver = ?");
    $vehicleCheck->bind_param("s", $username);
    $vehicleCheck->execute();
    $vehicleCheck->bind_result($vehicle);
    $vehicleCheck->fetch();
    $vehicleCheck->close();

    // Εισαγωγή του προιόντος στο όχημα  
    $stmtInsert = $conn->prepare("INSERT INTO vehiclesOnAction (v_name, driver, products, quantity, category, vehicle_location) VALUES (?, ?, ?, ?, ?, ?)");
    $stmtInsert->bind_param("sssiss", $vehicle, $username, $product, $quantity, $category, $vehicle_location);
    $stmtInsert->execute();
    $stmtInsert->close();
}

$stmtCheck->close();

// κλείσιμο της σύνδεσης με την βάση 
$conn->close();
?>