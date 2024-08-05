<?php
//σύνδεση με mySQL βαση 
include 'Connection.php';

// έλεγχος σύνδεσης 
if ($conn->connect_error) {
    die("Failed to connect to MySQL: " . $conn->connect_error);
}


// fetch δεδομένα απο τον πίνακα offer με χρήση AJAX 
$offerId = $_POST['offerId'];
$category = $_POST['category'];
$product = $_POST['product'];
$quantity = (int)$_POST['quantity']; 
$latitude = $_POST['latitude'];
$longitude = $_POST['longitude'];
$username = $_POST['username'];

$vehicle_location = $latitude . ', ' . $longitude;

$date = date("Y-m-d");

//τοπική ώρα για τα inserts 
date_default_timezone_set("Europe/Athens");

// Prepare και execute το update query που θέτει το offer σαν ολοκληρωμένο 
$updaterequest = $conn->prepare("UPDATE offer SET offer_status = 'COMPLETED', complete_offer = ? WHERE offer_id = ?");
$updaterequest->bind_param("si", $date, $offerId);
$updaterequest->execute();
$updaterequest->close();

// Prepare και execute το delete query για δραγραφή του task
$updatetasks = $conn->prepare("DELETE FROM task WHERE task_offer_id = ?");
$updatetasks->bind_param("i", $offerId);
$updatetasks->execute();
$updatetasks->close();

// Prepare και execute το select query για έλεγχο της ποσότητας προιόντων πάνω στο φορτηγό
$stmtCheck = $conn->prepare("SELECT quantity FROM vehiclesOnAction WHERE category = ? AND products = ? AND driver = ?");
$stmtCheck->bind_param("sss", $category, $product, $username);
$stmtCheck->execute();
$stmtCheck->store_result();

if ($stmtCheck->num_rows > 0) {
    $stmtCheck->bind_result($currentQuantity);
    $stmtCheck->fetch();
    $newQuantity = $currentQuantity + $quantity;

    // Prepare και execute το update query για το πεδίο quantity
    $stmtUpdate = $conn->prepare("UPDATE vehiclesOnAction SET quantity = ? WHERE category = ? AND products = ? AND driver = ?");
    $stmtUpdate->bind_param("isss", $newQuantity, $category, $product, $username);
    $stmtUpdate->execute();
    $stmtUpdate->close();
} else {
    // Prepare και execute το select query που επιστρέφει το vehicle name
    $vehicleCheck = $conn->prepare("SELECT DISTINCT v_name FROM vehiclesOnAction WHERE driver = ?");
    $vehicleCheck->bind_param("s", $username);
    $vehicleCheck->execute();
    $vehicleCheck->bind_result($vehicle);
    $vehicleCheck->fetch();
    $vehicleCheck->close();

    // Prepare και execute το insert query για εισαγωγή στον vehiclesOnAction πίνακα
    $stmtInsert = $conn->prepare("INSERT INTO vehiclesOnAction (v_name, driver, products, quantity, category, vehicle_location) VALUES (?, ?, ?, ?, ?, ?)");
    $stmtInsert->bind_param("sssiss", $vehicle, $username, $product, $quantity, $category, $vehicle_location);
    $stmtInsert->execute();
    $stmtInsert->close();
}

// κλείσιμο του select statement
$stmtCheck->close();

// κλείσιμο της σύνδεσης με την βάση / τερματισμός σύνδεσης
$conn->close();
?>