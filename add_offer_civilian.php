<?php
include 'Connection.php'; // αρχείο για σύνδεση με τη βάση δεδομένων

// Έλεγχος σύνδεσης με τη βάση δεδομένων
if ($conn->connect_error) {
    die("Failed to connect to MySQL: " . $conn->connect_error);
}

// Λήψη δεδομένων από το αίτημα AJAX 
$shortageId = $_POST['shortageId'];
$username = $_POST['username'];
$offerCategory = $_POST['category'];
$offerProduct = $_POST['product'];
$offerQuantity = (int)$_POST['quantity']; // μετατροπή quantity σε integer

// τοπικη date και time
date_default_timezone_set("Europe/Athens");
$date = date("Y-m-d");
$time = date("H:i:s"); 

$status = "WAITING";

// Εισαγωγή της προσφοράς στη βάση δεδομένων
$insertQuery = $conn->prepare("INSERT INTO offer (offer_civilian, offer_category, offer_product_name, offer_quantity, offer_date_posted, offer_time_posted, offer_status) VALUES (?, ?, ?, ?, ?, ?, ?)");
$insertQuery->bind_param("sssisss", $username, $offerCategory, $offerProduct, $offerQuantity, $date, $time, $status); // Δέσμευση των μεταβλτών

if ($insertQuery->execute()) {
    // Διαγραφή της καταχώρησης έλλειψης από τη βάση δεδομένων
    $deleteQuery = $conn->prepare("DELETE FROM shortage WHERE id_shortage = ?");
    $deleteQuery->bind_param("i", $shortageId); // Δέσμευση του ID της έλλειψης στο prepare statement
    $deleteQuery->execute();

    echo "Offer added and shortage deleted successfully."; // Επιστροφή μηνύματος επιτυχίας
} else {
    echo "Error: " . $insertQuery->error; // Επιστροφή μηνύματος σφάλματος αν η εισαγωγή αποτύχει
}

// Κλείσιμο της σύνδεσης με τη βάση δεδομένων
$conn->close();
?>
