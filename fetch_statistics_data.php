<?php
// Database connection parameters
$servername = "localhost";
$username = "evelina";
$password = "Evel1084599!";
$dbname = "carelink";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$start_date = $_POST['startDate'];
$end_date = $_POST['endDate'];

// Fetch counts from database
$sql = "SELECT 
            (SELECT COUNT(*) FROM offer WHERE offer_status = 'WAITING' AND offer_date_posted BETWEEN '$start_date' AND '$end_date') AS WaitingOffers,
            (SELECT COUNT(*) FROM offer WHERE offer_status = 'COMPLETED' AND offer_date_posted BETWEEN '$start_date' AND '$end_date') AS CompletedOffers,
            (SELECT COUNT(*) FROM request WHERE state = 'WAITING' AND request_date_posted BETWEEN '$start_date' AND '$end_date') AS WaitingRequests,
            (SELECT COUNT(*) FROM request WHERE state = 'COMPLETED' AND request_date_posted BETWEEN '$start_date' AND '$end_date') AS CompletedRequests";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $data = array(
        "WaitingOffers" => $row["WaitingOffers"],
        "CompletedOffers" => $row["CompletedOffers"],
        "WaitingRequests" => $row["WaitingRequests"],
        "CompletedRequests" => $row["CompletedRequests"]
    );
} else {
    $data = array(
        "WaitingOffers" => 0,
        "CompletedOffers" => 0,
        "WaitingRequests" => 0,
        "CompletedRequests" => 0
    );
}

// Close database connection
$conn->close();

// Return data as JSON
header('Content-Type: application/json');
echo json_encode($data);

?>