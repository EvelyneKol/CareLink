<?php
//σύνδεση με mySQL βαση
include 'Connection.php';

// Έλεγχος αν η σύνδεση με τη βάση δεδομένων ήταν επιτυχής
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Λήψη της παραμέτρου 'category_name'
$category_name = $_POST['category_name'];

//Ανάκτηση προϊόντων βάσει του ονόματος της κατηγορίας
$sql = "SELECT products FROM categories WHERE category_name = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $category_name);
$stmt->execute();
$result = $stmt->get_result();

// Δημιουργία ενός πίνακα για την αποθήκευση των προϊόντων
$products = [];
// Επανάληψη για κάθε γραμμή αποτελέσματος και προσθήκη του προϊόντος στον πίνακα
while ($row = $result->fetch_assoc()) {
    $products[] = $row['products'];
}

$stmt->close();
// κλείσιμο της σύνδεσης με την βάση
$conn->close();

// Επιστροφή των προϊόντων σε μορφή JSON
echo json_encode($products);
?>
