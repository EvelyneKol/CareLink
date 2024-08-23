<?php
include 'Connection.php';

// ελεγχος σύνδεσης
if ($conn->connect_error) {
    die("Failed to connect to MySQL: " . $conn->connect_error);
}

// Λήψη δεδομένων με χρήση AJAX
//μεταβλητές για αποθήκευση δεδομένων
$OfferId = (int)$_POST['OfferId'];
$offerCategory = $_POST['category'];
$offerProduct = $_POST['product'];
$offerQuantity = (int)$_POST['quantity']; // διασφάλιση της ποσότητας ως ακέραιος

// τοπική ώρα/ημερομηνία
date_default_timezone_set("Europe/Athens");
$date = date("Y-m-d");
$time = date("H:i:s"); 

// Συνδυασμός ημερομηνίας και ώρας σε μια ενιαία μεταβλητή 
$dateTime = $date . ' ' . $time;

// Εκτέλεση ερώτησης για διαγραφή της προσφοράς από τη βάση δεδομένων
$deleteQuery = $conn->prepare("DELETE FROM offer WHERE offer_id =?");
$deleteQuery->bind_param("i", $OfferId);

if ($deleteQuery->execute()) {
    // Αν η διαγραφή της προσφοράς ήταν επιτυχής, εκτέλεση ερώτησης για εισαγωγή της έλλειψης στη βάση δεδομένων
    $deleteQueryData = $conn->prepare("INSERT INTO shortage VALUES (NULL, ?, ?, ?, ?)");
    $deleteQueryData->bind_param("ssis", $offerCategory,$offerProduct,$offerQuantity,$dateTime);
    $deleteQueryData->execute();
} else {
    //σφάλμα
    echo "Error: " . $deleteQuery->error;
}

// κλέισιμο σύνδεσης
$conn->close();
?>