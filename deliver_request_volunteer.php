<?php
include 'Connection.php';

// Check connection
if ($conn->connect_error) {
    die("Failed to connect to MySQL: " . $conn->connect_error);
}

// Get data from the AJAX request
$requestId = $_POST['requestId'];
$category = $_POST['category'];
$product = $_POST['product'];
$quantity = (int)$_POST['quantity'];
$username = $_POST['username'];

// Prepare and execute the update query
$updaterequest = $conn->prepare("UPDATE request SET state = 'COMPLETED' WHERE id_request = ?");
$updaterequest->bind_param("i", $requestId);
$updaterequest->execute();

// Prepare and execute the update query
$updatetasks = $conn->prepare("DELETE FROM task WHERE task_request_id = ? ");
$updatetasks->bind_param("i", $requestId);
$updatetasks->execute();

$stmtCheck = $conn->prepare("SELECT quantity FROM vehiclesOnAction WHERE category = ? AND products = ? AND driver = ?");
$stmtCheck->bind_param("sss", $category, $product, $username);
$stmtCheck->execute();
$stmtCheck->bind_result($existingQuantity);
$stmtCheck->fetch();
$stmtCheck->close();

if ($existingQuantity >= $quantity) {
    $newQuantity = $existingQuantity - $quantity;

    if ($newQuantity != 0) {
        $stmtUpdate = $conn->prepare("UPDATE vehiclesOnAction SET quantity = ? WHERE category = ? AND products = ? AND driver = ?");
        $stmtUpdate->bind_param("isss", $newQuantity, $category, $product, $username);
        $stmtUpdate->execute();
        $stmtUpdate->close();
    } else {
        $stmtDelete = $conn->prepare("DELETE FROM vehiclesOnAction WHERE category = ? AND products = ? AND driver = ?");
        $stmtDelete->bind_param("sss", $category, $product, $username);
        $stmtDelete->execute();
        $stmtDelete->close();
    }

} else {
    echo "Error: Not enough quantity to unload.";
}

// Close the database connection
$conn->close();
?>
