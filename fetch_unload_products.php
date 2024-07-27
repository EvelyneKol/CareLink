<?php
$servername = "localhost";
$username = "evelina";
$password = "Evel1084599!";
$dbname = "carelink";

 $conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$category = $_POST['category'];

// Fetch products based on category name
$sql = "SELECT products FROM vehiclesOnAction WHERE category = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $category);
$stmt->execute();
$result = $stmt->get_result();

$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row['products'];
}

$stmt->close();
$conn->close();

echo json_encode($products);
?>
