<?php
$servername = "localhost";
$username = "root";
$password = "karagiannis";
$dbname = "carelink";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the request ID is provided in the URL
if (isset($_GET['id'])) {
    $requestId = $_GET['id'];

    // Prepare and execute the SQL query to delete the row
    $sql = "DELETE FROM request WHERE id_request = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $requestId);

    $result = $stmt->execute();
    $stmt->close();
} else {
    // Return an error message if the request ID is not provided
    echo json_encode(['success' => false, 'message' => 'Request ID not provided.']);
}

$conn->close();
?>
