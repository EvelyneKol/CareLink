<?php
// Replace with your actual database credentials
$host = 'your_database_host';
$username = 'your_database_username';
$password = 'your_database_password';
$database = 'your_database_name';

// Create a database connection
$conn = new mysqli($host, $username, $password, $database);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Your SQL query for requests
$requestQuery = "SELECT request.*, civilian.civilian_location 
                 FROM request 
                 JOIN civilian ON request.request_civilian = civilian.civilian_username";

// Your SQL query for offers
$offersQuery = "SELECT offer.*, civilian.civilian_location 
                FROM offer 
                JOIN civilian ON offer.offer_civilian = civilian.civilian_username";

$data = array();

// Fetch data for requests
$requestResult = $conn->query($requestQuery);
if ($requestResult) {
    while ($row = $requestResult->fetch_assoc()) {
        list($latitude, $longitude) = explode(",", $row["civilian_location"]);

        $processedData = array(
            "type" => "request",
            "id_request" => $row["id_request"],
            "request_civilian" => $row["request_civilian"],
            "request_category" => $row["request_category"],
            "request_product_name" => $row["request_product_name"],
            "persons" => $row["persons"],
            "date_posted" => $row["date_posted"],
            "time_posted" => $row["time_posted"],
            "state" => $row["state"],
            "latitude" => $latitude,
            "longitude" => $longitude
        );

        $data[] = $processedData;
    }
    $requestResult->close();
} else {
    die("Error executing the request SQL query: " . $conn->error);
}

// Fetch data for offers
$offersResult = $conn->query($offersQuery);
if ($offersResult) {
    while ($row = $offersResult->fetch_assoc()) {
        list($latitude, $longitude) = explode(",", $row["civilian_location"]);

        $processedData = array(
            "type" => "offer",
            "offer_id" => $row["offer_id"],
            "offer_quantity" => $row["offer_quantity"],
            "offer_civilian" => $row["offer_civilian"],
            "offer_datetime" => $row["offer_datetime"],
            "offer_status" => $row["offer_status"],
            "latitude" => $latitude,
            "longitude" => $longitude
        );

        $data[] = $processedData;
    }
    $offersResult->close();
} else {
    die("Error executing the offers SQL query: " . $conn->error);
}

// Close the database connection
$conn->close();

// Output the data as JSON
header('Content-Type: application/json');
echo json_encode($data);
?>
