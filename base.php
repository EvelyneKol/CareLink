<?php
include 'Connection.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['Insert']) && isset($_POST['Category']) && isset($_POST['Product']) && isset($_POST['Quantity']) && isset($_POST['Details'])) {
        // Handle the insert logic
        $category = $_POST['Category'];
        $product = $_POST['Product'];
        $quantity = (int)$_POST['Quantity'];
        $details = $_POST['Details'];

        $Checkprod = $conn->prepare("SELECT quantity_on_stock FROM categories WHERE category_name = ? AND products = ? ");
        $Checkprod->bind_param("ss", $category, $product);
        $Checkprod->execute();
        $Checkprod->store_result();

        if ($Checkprod->num_rows > 0) {
            $Checkprod->bind_result($currentQuantity);
            $Checkprod->fetch();
            $newQuantity = $currentQuantity + $quantity;

            $stmtUpdate = $conn->prepare("UPDATE categories SET quantity_on_stock = ? WHERE category_name = ? AND products = ?");
            $stmtUpdate->bind_param("iss", $newQuantity, $category, $product);
            $stmtUpdate->execute();
            $stmtUpdate->close();
        } else {
            $stmtInsert = $conn->prepare("INSERT INTO categories (base_id_categories, category_id, category_name, products, quantity_on_stock, details)VALUES (1, null , ?, ?, ?, ?)");
            $stmtInsert->bind_param("ssis", $category, $product, $quantity, $details);
            $stmtInsert->execute();
            $stmtInsert->close();
        }
        $Checkprod->close();

    } elseif (isset($_POST['Delete']) && isset($_POST['Category']) && isset($_POST['Product']) && isset($_POST['Quantity']) && isset($_POST['Details'])) {
        // Handle the delete logic
        $category = $_POST['Category'];
        $product = $_POST['Product'];
        $quantity = (int)$_POST['Quantity'];
        $details = $_POST['Details'];

        $Checkprod = $conn->prepare("SELECT quantity_on_stock FROM categories WHERE category_name = ? AND products = ? ");
        $Checkprod->bind_param("ss", $category, $product);
        $Checkprod->execute();
        $Checkprod->store_result();

        if ($Checkprod->num_rows > 0) {
            $Checkprod->bind_result($currentQuantity);
            $Checkprod->fetch();
            $newQuantity = $currentQuantity - $quantity;
            if ($currentQuantity >= $quantity) {
                $stmtUpdate = $conn->prepare("UPDATE categories SET quantity_on_stock = ? WHERE category_name = ? AND products = ?");
                $stmtUpdate->bind_param("iss", $newQuantity, $category, $product);
                $stmtUpdate->execute();
                $stmtUpdate->close();
            }else{
                echo "Error: Not enough quantity to delete.";
            }
        } else {
            echo "Error: Not such product to delete.";
        }
        $Checkprod->close();

    }
}
// Close the database connection
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
    <link rel="stylesheet" href="css/base.css">
</head>
<body>
    <div class="header">
        <h2><img src="images/logo.png" alt="Logo" width="210"></h2>
        <p><strong>Base management</strong><br>Add or delete categories and items or make ony other changes</p>
    </div>

    <div class="home">
        <i><a href="admin.php"><i class="fa fa-home" style="font-size:24px"></i>Home</a></i>
    </div>

    <div class="form_container">
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
                // Fetch data from the shortage table
                $sql = "SELECT * FROM categories ORDER BY category_name";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    // Output data of each row
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["category_name"] . "</td>";
                        echo "<td>" . $row["products"] . "</td>";
                        echo "<td>" . $row["total_quantity"] . "</td>";
                        echo "<td>" . $row["details"] . "</td>";
                        echo "</tr>";
                    }
                } else {
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
            productDropdown.innerHTML = ""; // Clear existing options

            // Fetch products for the selected category using AJAX
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "fetch_products.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        var products = JSON.parse(xhr.responseText);
                        products.forEach(function(product) {
                            var option = document.createElement("option");
                            option.text = product;
                            option.value = product;
                            productDropdown.add(option);
                        });
                    } else {
                        console.error("Error fetching products");
                    }
                }
            };
            xhr.send("category_name=" + categoryId);
        });
    </script>
</body>
</html>
