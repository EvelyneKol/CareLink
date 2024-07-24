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
    $servername = "localhost";
    $username = "root";
    $password = "karagiannis";
    $dbname = "carelink";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $username = $_GET['q'];

        $sql = "SELECT driver, products, quantity, vehicle_location FROM vehicle WHERE driver = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);

        $result = $stmt->execute();
    if (!$result) {
         die("Error: " . $stmt->error);
        }
        $stmt->bind_result($driver, $products, $quantity, $vehicle_location);

        if ($stmt->fetch()) {
            echo '<div class="row">';
            echo '<div class="col-sm-4"></div>';
            echo '<div class="col-sm-4">';
            echo '<table>';
            echo '<tr>';
            echo '<th>Driver</th>';
            echo '<th>Product</th>';
            echo '<th>Quantity</th>';
            echo '<th>Location</th>';
            echo '</tr>';

            // Loop through all records
            do {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($driver) . '</td>';
                echo '<td>' . htmlspecialchars($products) . '</td>';
                echo '<td>' . htmlspecialchars($quantity) . '</td>';
                echo '<td>' . htmlspecialchars($vehicle_location) . '</td>';
                echo '</tr>';
            } while ($stmt->fetch());

            echo '</table>';
            echo '</div>';
            echo '<div class="col-sm-4"></div>';
            echo '</div>';
        } else {
            echo "<p>No records found for the given driver.</p>";
        }

        $stmt->close();
    }

    $conn->close();
?>
</body>
</html>
