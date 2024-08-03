<?php
 include 'Connection.php';

 if ($conn->connect_error) {
     die("Connection failed: " . $conn->connect_error);
 }

// Process AJAX request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["category"])) {
    $selected_category = $_POST["category"];
    if (!empty($selected_category)) {
        // Fetch data based on selected category
        $sql = "SELECT * FROM categories WHERE category_name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $selected_category);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<h2>Product status for $selected_category Category</h2>";
            echo "<table border='1'>";
            echo "<tr><th>Product</th><th>Quantity on Stock</th><th>Quantity on Truck</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["products"] . "</td>";
                echo "<td>" . $row["quantity_on_stock"] . "</td>";
                echo "<td>" . $row["quantity_on_truck"] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "No data available for selected category.";
        }
        $stmt->close();
    } else {
        echo "Please select a category.";
    }
}
?>
