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
date_default_timezone_set("Europe/Athens");
$date = date("Y-m-d");

// Ελέγχουμε αν το προιον είναι στο όχημα
$stmtCheck = $conn->prepare("SELECT quantity FROM vehiclesOnAction WHERE category = ? AND products = ? AND driver = ?");
$stmtCheck->bind_param("sss", $category, $product, $username);
$stmtCheck->execute();
$stmtCheck->bind_result($existingQuantity);
$stmtCheck->fetch();
$stmtCheck->close();

// Αν η ποσότητα που ξεφορτώνουμε είναι μικρότερη ή ίση από αυτή που υπάρχει τότε:
if ($existingQuantity >= $quantity) {
    $newQuantity = $existingQuantity - $quantity;

    // Ενημερώνουμε του αιτήματος σε COMPLETED
    $updaterequest = $conn->prepare("UPDATE request SET state = 'COMPLETED', complete_request=? WHERE id_request = ?");
    $updaterequest->bind_param("si", $date, $requestId);
    if ($updaterequest->execute()) {
        echo "Request state updated successfully.<br>";
    } else {
        echo "Error updating request state: " . $conn->error . "<br>";
    }

    // Διαγραφή του αιτήματος από τα task 
    $updatetasks = $conn->prepare("DELETE FROM task WHERE task_request_id = ?");
    $updatetasks->bind_param("i", $requestId);
    if ($updatetasks->execute()) {
        echo "Task deleted successfully.<br>";
    } else {
        echo "Error deleting task: " . $conn->error . "<br>";
    }

    // Αν η ποσότητα δεν είναι ίδια με αυτή του οχήματος τότε
    if ($newQuantity != 0) {
        // Ενημέρωση της ποσότητας του προιόντος στο όχημα (μείωση)
        $stmtUpdate = $conn->prepare("UPDATE vehiclesOnAction SET quantity = ? WHERE category = ? AND products = ? AND driver = ?");
        $stmtUpdate->bind_param("isss", $newQuantity, $category, $product, $username);
        
        // έλεγχος του SQL execution
        if ($stmtUpdate->execute()) {
            echo "Product quantity updated successfully to $newQuantity.<br>";
        } else {
            echo "Error updating product quantity: " . $conn->error . "<br>";
        }
        $stmtUpdate->close();
    // Αν η ποσότητα είναι ίδια με αυτή του οχήματος τότε
    } 
    if ($existingQuantity=$quantity) {
        // Διαγραφή του προιόντος από το όχημα
        $stmtDelete = $conn->prepare("DELETE FROM vehiclesOnAction WHERE category = ? AND products = ? AND driver = ?");
        $stmtDelete->bind_param("sss", $category, $product, $username);
        
        // έλεγχος του SQL execution
        if ($stmtDelete->execute()) {
            // έλεγχος
            if ($stmtDelete->affected_rows > 0) {
                echo "Product deleted successfully from the vehicle.<br>";
            } else {
                echo "No matching product found to delete.<br>";
            }
        } else {
            echo "Error deleting product: " . $conn->error . "<br>";
        }
        $stmtDelete->close();
    }

    echo "Success: The request has been completed.";

} else {
    echo "Error: Not enough quantity to unload.";
}

// κλείσιμο της σύνδεσης με την βάση 
$conn->close();
?>
