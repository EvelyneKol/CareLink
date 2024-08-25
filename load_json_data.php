<?php
// σύνδεση με τη βάση δεδομένων
include 'Connection.php';

// Έλεγχος της σύνδεσης με τη βάση δεδομένων  
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Διαχείριση της AJAX αίτησης
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["category"])) {
    $selected_category = $_POST["category"];
    
    if (!empty($selected_category)) {
        // Ερώτημα για να ανακτηθούν τα προϊόντα και οι ποσότητες για την επιλεγμένη κατηγορία
        $sql_products_by_category = "
        SELECT products, quantity_on_stock
        FROM categories
        WHERE category_name = ?
        ORDER BY products";

        // Ερώτημα για να ανακτηθούν τα προϊόντα και οι ποσότητες που είναι σε φορτηγά για την επιλεγμένη κατηγορία
        $sql_vehicles_on_action = "
        SELECT products, SUM(quantity) AS total_quantity
        FROM vehiclesOnAction
        WHERE category = ?
        GROUP BY products
        ORDER BY products";

        // Προετοιμασία και εκτέλεση του ερωτήματος για προϊόντα ανά κατηγορία
        if ($stmt = $conn->prepare($sql_products_by_category)) {
            $stmt->bind_param("s", $selected_category);
            $stmt->execute();
            $result_products_by_category = $stmt->get_result();

            echo "<h2>Products in Base: $selected_category</h2>";
            if ($result_products_by_category->num_rows > 0) {
                echo "<table border='1'>";
                echo "<tr><th>Product</th><th>Quantity on Stock</th></tr>";
                while ($row = $result_products_by_category->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row["products"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["quantity_on_stock"]) . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "No products available for the selected category.";
            }

            $stmt->close();
        }

        // Προετοιμασία και εκτέλεση του ερωτήματος για προϊόντα σε φορτηγά
        if ($stmt = $conn->prepare($sql_vehicles_on_action)) {
            $stmt->bind_param("s", $selected_category);
            $stmt->execute();
            $result_vehicles_on_action = $stmt->get_result();

            echo "<h2>Products on Vehicles for Category: $selected_category</h2>";
            if ($result_vehicles_on_action->num_rows > 0) {
                echo "<table border='1'>";
                echo "<tr><th>Product</th><th>Quantity on Truck</th></tr>";
                while ($row = $result_vehicles_on_action->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row["products"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["total_quantity"]) . "</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "No products on vehicles for the selected category.";
            }

            $stmt->close();
        }
    } else {
        echo "Please select a category.";
    }
}
?>
