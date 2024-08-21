<?php
// σύνδεση
include 'Connection.php';

// έλεγχος connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//αποθήκευση του διαστήματος αναζήτησης απο τα φίλτρα
$start_date = $_POST['startDate'];
$end_date = $_POST['endDate'];

// επιλογή του πλήθους προσφορών και αιτημάτων απο την βάση 
$sql = "SELECT 
            (SELECT COUNT(*) FROM offer WHERE offer_status = 'WAITING' AND offer_date_posted BETWEEN '$start_date' AND '$end_date') AS WaitingOffers,
            (SELECT COUNT(*) FROM offer WHERE offer_status = 'COMPLETED' AND offer_date_posted BETWEEN '$start_date' AND '$end_date') AS CompletedOffers,
            (SELECT COUNT(*) FROM request WHERE state = 'WAITING' AND request_date_posted BETWEEN '$start_date' AND '$end_date') AS WaitingRequests,
            (SELECT COUNT(*) FROM request WHERE state = 'COMPLETED' AND request_date_posted BETWEEN '$start_date' AND '$end_date') AS CompletedRequests";

$result = $conn->query($sql);

//αν υπάρχουν εγγραφές αποθήκευσε τες στο array data
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $data = array(
        "WaitingOffers" => $row["WaitingOffers"],
        "CompletedOffers" => $row["CompletedOffers"],
        "WaitingRequests" => $row["WaitingRequests"],
        "CompletedRequests" => $row["CompletedRequests"]
    );
} else {
    //αν δεν υπάρχουν εγγραφές βάλε 0
    $data = array(
        "WaitingOffers" => 0,
        "CompletedOffers" => 0,
        "WaitingRequests" => 0,
        "CompletedRequests" => 0
    );
}

// κλέισιμο σύνδεσης
$conn->close();

// επιστροφή των δεδομένων σε μορφή JSON
header('Content-Type: application/json');
echo json_encode($data);

?>