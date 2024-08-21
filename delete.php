<?php
include 'Connection.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// εέλγχος ύπαρξης του request ID
if (isset($_GET['id'])) {
    $requestId = $_GET['id'];

    // επιλογή και εκτέλεση του query για διαραφή της εγγραφής απο τον πίνακα request
    $sql = "DELETE FROM request WHERE id_request = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $requestId);

    $result = $stmt->execute();
    $stmt->close();
} else {
    // μήνυμα λάθους αν το request ID δεν δωθεί 
    echo json_encode(['success' => false, 'message' => 'Request ID not provided.']);
}

$conn->close();
?>
