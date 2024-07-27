<?php
 $servername = "localhost";
 $username = "root";
 $password = "karagiannis";
 $dbname = "carelink";

 $conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$category_name = $_POST['category_name'];

// Fetch products based on category name
$sql = "SELECT products FROM categories WHERE category_name = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $category_name);
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
