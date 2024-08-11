<!DOCTYPE html>
<html>
<head>
<style>
    table,
    th,
    td {
        border: 2px solid #f3ebdb;
        font-size: 20px;
        margin: auto;
        justify-content: center;
    }
    table {
        margin-top: 15px;
        width: 100%;
    }
</style>
</head>
<body>
<?php
    //σύνδεση με mySQL βαση
    include 'Connection.php';
    // Έλεγχος αν η σύνδεση με τη βάση δεδομένων ήταν επιτυχής
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Έλεγχος αν η μέθοδος του αιτήματος είναι GET
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
         // Λήψη της παραμέτρου 'q' από το GET αίτημα (το username)
        $username = $_GET['q'];

        //Επιλογή προιόντων και τις ποσότητες από τον πίνακα vehiclesOnAction με βάση τον οδηγό (driver)
        $sql = "SELECT products, quantity FROM vehiclesOnAction WHERE driver = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);

        $result = $stmt->execute();
    // Έλεγχος αν το ερώτημα εκτελέστηκε επιτυχώς
    if (!$result) {
         die("Error: " . $stmt->error);
        }
         // Δέσμευση των αποτελεσμάτων
        $stmt->bind_result($products, $quantity);

        // Έλεγχος αν βρέθηκε κάποιο αποτέλεσμα
        if ($stmt->fetch()) {
            // Δημιουργία HTML πίνακα για την εμφάνιση των δεδομένων
            echo '<div class="row">';
            echo '<div class="col-sm-4"></div>';
            echo '<div class="col-sm-4">';
            echo '<table>';
            echo '<tr>';
            echo '<th>Product</th>';
            echo '<th>Quantity</th>';
            echo '</tr>';

            // Επανάληψη σε όλα τα αποτελέσματα και προσθήκη τους στον πίνακα
            do {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($products) . '</td>';
                echo '<td>' . htmlspecialchars($quantity) . '</td>';
                echo '</tr>';
            } while ($stmt->fetch());

            echo '</table>';
            echo '</div>';
            echo '<div class="col-sm-4"></div>';
            echo '</div>';
        } else {
             // Αν δεν βρέθηκαν αποτελέσματα, εμφάνιση σχετικού μηνύματος
            echo "<p>No records found for the given driver.</p>";
        }

        $stmt->close();
    }
    // κλείσιμο της σύνδεσης με την βάση / τερματισμός σύνδεσης
    $conn->close();
?>
</body>
</html>
