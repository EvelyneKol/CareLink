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

// Fetch counts from database
$sql = "SELECT 
            (SELECT COUNT(*) FROM offer WHERE offer_status = 'WAITING') AS WaitingOffers,
            (SELECT COUNT(*) FROM offer WHERE offer_status = 'COMPLETED') AS CompletedOffers,
            (SELECT COUNT(*) FROM request WHERE state = 'WAITING') AS WaitingRequests,
            (SELECT COUNT(*) FROM request WHERE state = 'COMPLETED') AS CompletedRequests";

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



