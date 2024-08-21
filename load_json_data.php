<?php
// σύνδεση με τη βάση δεδομένων
 include 'Connection.php';

// Έλεγχος της σύνδεσης με τη βάση δεδομένων  
 if ($conn->connect_error) {
     die("Connection failed: " . $conn->connect_error);
 }

// Έλεγχος αν το αίτημα είναι POST και αν η κατηγορία έχει επιλεγεί
// διαχείηση AJAX request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["category"])) {
    $selected_category = $_POST["category"];
    // Έλεγχος αν η επιλεγμένη κατηγορία δεν είναι κενή
    if (!empty($selected_category)) {
        // query για να ανακτηθούν τα δεδομένα για την επιλεγμένη κατηγορία
        $sql = "SELECT * FROM categories WHERE category_name = ?";
        //προετοιμασία και δέσμευση παραμέτρου και εκτέλεση
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $selected_category);
        $stmt->execute();
        $result = $stmt->get_result();

        // Έλεγχος αν υπάρχουν εγγραφές για την επιλεγμένη κατηγορία
        if ($result->num_rows > 0) {
            // Αν υπάρχουν δεδομένα, εμφανίζει τον πίνακα με τα δεδομένα σε αυτόν
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
            // Αν δεν υπάρχουν δεδομένα για την κατηγορία, εμφανίζει μήνυμα
            echo "No data available for selected category.";
        }
        $stmt->close();
    } else {
         // Αν δεν έχει επιλεγεί κατηγορία εμφανίζει κατάλληλο μήνυμα 
        echo "Please select a category.";
    }
}
?>
