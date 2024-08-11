<?php
//σύνδεση με mySQL βαση
include 'Connection.php';

// Έλεγχος αν η σύνδεση με τη βάση δεδομένων ήταν επιτυχής
if ($conn->connect_error) {
    die("Failed to connect to MySQL: " . $conn->connect_error);
}

// Λήψη παραμέτρων
$requestId = $_POST['requestId'];
$category = $_POST['category'];
$product = $_POST['product'];
$quantity = (int)$_POST['quantity'];
$username = $_POST['username'];
$date = date("Y-m-d");
date_default_timezone_set("Europe/Athens");
//Ελέγχουμε αν το προιον είναι στο όχημα
$stmtCheck = $conn->prepare("SELECT quantity FROM vehiclesOnAction WHERE category = ? AND products = ? AND driver = ?");
$stmtCheck->bind_param("sss", $category, $product, $username);
$stmtCheck->execute();
$stmtCheck->bind_result($existingQuantity);
$stmtCheck->fetch();
$stmtCheck->close();
//Αν ναι τότε:
//Αν η ποσότητα που ξεφορτώνουμε ειναί μικρότερη ή ίση απο αυτή που υπάρχει τότε:
if ($existingQuantity >= $quantity) {
    $newQuantity = $existingQuantity - $quantity;
        // Ενημερώνουμε του αιτήματος σε COMPLETED
        $updaterequest = $conn->prepare("UPDATE request SET state = 'COMPLETED', complete_request=? WHERE id_request = ?");
        $updaterequest->bind_param("si",$date, $requestId);
        $updaterequest->execute();

        //Διαγραφή του αιτήματος απο τα task 
        $updatetasks = $conn->prepare("DELETE FROM task WHERE task_request_id = ? ");
        $updatetasks->bind_param("i", $requestId);
        $updatetasks->execute();
    //Αν η ποσότητα δεν είνια ίδια με αυτή του οχήματος τότε
    if ($newQuantity != 0) {
        //Ενημέρωση της πσοσότητας του προιόντος στο όχημα 
        $stmtUpdate = $conn->prepare("UPDATE vehiclesOnAction SET quantity = ? WHERE category = ? AND products = ? AND driver = ?");
        $stmtUpdate->bind_param("isss", $newQuantity, $category, $product, $username);
        $stmtUpdate->execute();
        $stmtUpdate->close();
    //Αν η ποσότητα είνια ίδια με αυτή του οχήματος τότε
    } else {
        //Διαγραφή του προιόντος απο το όχημα
        $stmtDelete = $conn->prepare("DELETE FROM vehiclesOnAction WHERE category = ? AND products = ? AND driver = ?");
        $stmtDelete->bind_param("sss", $category, $product, $username);
        $stmtDelete->execute();
        $stmtDelete->close();
    }
//Αν όχι τότε:
} else {
    echo "Error: Not enough quantity to unload.";
}

// κλείσιμο της σύνδεσης με την βάση 
$conn->close();
?>
