<?php
include 'Connection.php'; // αρχείο για σύνδεση με τη βάση δεδομένων


//έλεγχος συνδεσης
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Έλεγχος αν το αίτημα είναι POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Έλεγχος αν έχουν υποβληθεί όλα τα απαραίτητα δεδομένα για την εισαγωγή
    if (isset($_POST['Insert']) && isset($_POST['Category']) && isset($_POST['Product']) && isset($_POST['Quantity']) && isset($_POST['Details'])) {
        // μεταβλητές
        $category = $_POST['Category'];
        $product = $_POST['Product'];
        $quantity = (int)$_POST['Quantity'];
        $details = $_POST['Details'];

        // Έλεγχος αν το προϊόν υπάρχει ήδη στη βάση δεδομένων
        $Checkprod = $conn->prepare("SELECT quantity_on_stock FROM categories WHERE category_name = ? AND products = ? ");
        $Checkprod->bind_param("ss", $category, $product);
        $Checkprod->execute();
        $Checkprod->store_result();

        if ($Checkprod->num_rows > 0) {
            // Αν το προϊόν υπάρχει, ενημέρωση της ποσότητας στο απόθεμα
            $Checkprod->bind_result($currentQuantity);
            $Checkprod->fetch();
            $newQuantity = $currentQuantity + $quantity;

            $stmtUpdate = $conn->prepare("UPDATE categories SET quantity_on_stock = ? WHERE category_name = ? AND products = ?");
            $stmtUpdate->bind_param("iss", $newQuantity, $category, $product);
            $stmtUpdate->execute();
            $stmtUpdate->close();
        } else {
            // Αν το προϊόν δεν υπάρχει, εισαγωγή του στη βάση δεδομένων
            $stmtInsert = $conn->prepare("INSERT INTO categories (base_id_categories, category_id, category_name, products, quantity_on_stock, details)VALUES (1, null , ?, ?, ?, ?)");
            $stmtInsert->bind_param("ssis", $category, $product, $quantity, $details);
            $stmtInsert->execute();
            $stmtInsert->close();
        }
        $Checkprod->close();

    } elseif (isset($_POST['Delete']) && isset($_POST['Category']) && isset($_POST['Product']) && isset($_POST['Quantity']) && isset($_POST['Details'])) {
        // διαγραφή
        $category = $_POST['Category'];
        $product = $_POST['Product'];
        $quantity = (int)$_POST['Quantity'];
        $details = $_POST['Details'];

        // Έλεγχος αν το προϊόν υπάρχει στη βάση δεδομένων
        $Checkprod = $conn->prepare("SELECT quantity_on_stock FROM categories WHERE category_name = ? AND products = ? ");
        $Checkprod->bind_param("ss", $category, $product);
        $Checkprod->execute();
        $Checkprod->store_result();

        if ($Checkprod->num_rows > 0) {
            // Αν το προϊόν υπάρχει, έλεγχος αν η ποσότητα είναι επαρκής για διαγραφή
            $Checkprod->bind_result($currentQuantity);
            $Checkprod->fetch();
            $newQuantity = $currentQuantity - $quantity;
            if ($currentQuantity >= $quantity) {
                $stmtUpdate = $conn->prepare("UPDATE categories SET quantity_on_stock = ? WHERE category_name = ? AND products = ?");
                $stmtUpdate->bind_param("iss", $newQuantity, $category, $product);
                $stmtUpdate->execute();
                $stmtUpdate->close();
            }else{
                // Εμφάνιση μηνύματος σφάλματος αν η ποσότητα δεν είναι επαρκής
                echo "Error: Not enough quantity to delete.";
            }
        } else {
            // Εμφάνιση μηνύματος σφάλματος αν το προϊόν δεν υπάρχει στη βάση δεδομένων
            echo "Error: Not such product to delete.";
        }
        $Checkprod->close();

    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>CareLink</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/jpg" sizes="96x96" href="images/favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="base.css">
</head>
<body>
    <div class="header">
        <h2><img src="images/logo1.png" alt="Logo"></h2>
        <hr>
    </div>

    <div class="home">
        <i><a href="admin.php"><i class="fa fa-home" style="font-size:24px"></i>Home</a></i>
    </div>

    <div class="form_container">
        <h2>Base management<br></h2>
        <h4>Add or delete categories and items or make ony other changes</h4>
        <div id="base_managment">
            <div class="row">
                <div class="col-sm-3"></div>
                <div class="col-sm-6">
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" onsubmit="resetForm()">
                        <div class="row">
                            <div class="col-sm-6">
                                <label for="Category" class="form-label">Category</label>
                                <input type="text" class="form-control p-2" id="Category" name="Category" placeholder="Write the category..." autocomplete="on" required>
                            </div>

                            <div class="col-sm-6">
                                <label for="Product" class="form-label">Product</label>
                                <input type="text" class="form-control p-2" placeholder="Write the Product..." id="Product" name="Product" autocomplete="on" required>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-sm-6">
                                <label for="Quantity" class="form-label">Quantity</label>
                                <input type="number" class="form-control p-2" id="Quantity" name="Quantity"
                                    placeholder="Insert the quantity of the product" autocomplete="on" required min="0">
                            </div>
                            <div class="col-sm-6">
                                <label for="Details" class="form-label">Details</label>
                                <input type="text" class="form-control p-2" placeholder="Write the Details..." id="Details" name="Details" autocomplete="on" required>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-sm-3"></div>
                            <div class="col-sm-6">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <button class="Insert" type="submit" name="Insert">Insert</button>
                                    </div>
                                    <div class="col-sm-6">
                                        <button class="Delete" type="submit" name="Delete">Delete</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3"></div>
                        </div>
                    </form>
                </div>
                <div class="col-sm-3"></div>
            </div>
            <br>
            <div class="row">
                <div class="col-sm-3"></div>
                <div class="col-sm-6">
                    <form action="add_data.php" method="POST" enctype="multipart/form-data">
                        <h3>Upload JSON File</h3>
                        <input type="file" name="json_file" id="json_file" required>
                        <button class="update" type="submit" name="Update">Update</button>
                    </form>
                </div>
                <div class="col-sm-3"></div>
            </div>
        </div>
    </div>    


    <div class="dataTable">
        <h4>Base Data Table</h4>
        <table class="table">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Products</th>
                    <th>Total quantity</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Λήψη δεδομένων από τον πίνακα shortage
                $sql = "SELECT * FROM categories ORDER BY category_name";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    // Εμφάνιση των δεδομένων 
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["category_name"] . "</td>";
                        echo "<td>" . $row["products"] . "</td>";
                        echo "<td>" . $row["total_quantity"] . "</td>";
                        echo "<td>" . $row["details"] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    // Εμφάνιση μηνύματος αν δεν υπάρχουν δεδομένα
                    echo "<tr><td colspan='5'>No data available in Product table</td></tr>";
                }
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>   
    
    <script>
        document.getElementById("category_name").addEventListener("change", function() {
            var categoryId = this.value;
            var productDropdown = document.getElementById("product_name");
            productDropdown.innerHTML = ""; // Εκκαθάριση των προηγούμενων επιλογών

             // Λήψη προϊόντων για την επιλεγμένη κατηγορία μέσω AJAX
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "fetch_products.php", true); // Αποστολή αιτήματος POST στο αρχείο fetch_products.php
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        var products = JSON.parse(xhr.responseText); // Μετατροπή του JSON σε αντικείμενο JavaScript
                        products.forEach(function(product) {
                            var option = document.createElement("option");
                            option.text = product;
                            option.value = product;
                            productDropdown.add(option); // Προσθήκη επιλογής στο dropdown προϊόντων
                        });
                    } else {
                        console.error("Error fetching products");
                    }
                }
            };
            xhr.send("category_name=" + categoryId); // Αποστολή της επιλεγμένης κατηγορίας στο fetch_products.php
        });
    </script>
</body>
</html>
