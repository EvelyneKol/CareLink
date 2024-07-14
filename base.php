<?php
$servername = "localhost";
$username = "evelina";
$password = "Evel1084599!";
$dbname = "carelink";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//add a category
if (isset($_POST['add_category'])) {
    $category_name = $_POST['category_name'];
    
    // Check if the category name is not empty
    if (!empty($category_name)) {
        // Prepare and execute SQL statement to insert the new category into the table
        $sql = "INSERT INTO categories (base_id_categories, category_name) VALUES (1, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $category_name);
        
        if ($stmt->execute()) {
            echo "Category added successfully.";
        } else {
            echo "Error adding category: " . $conn->error;
        }
    } else {
        echo "Category name cannot be empty.";
    }
}

//Delete category
if (isset($_POST['delete_selected_category'])) {
    $category_name = $_POST['delete_category'];

    // Prepare and execute SQL statement to delete the selected category
    $sql = "DELETE FROM categories WHERE category_name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $category_name);
    
    if ($stmt->execute()) {
        echo "Category '$category_name' deleted successfully.";
    } else {
        echo "Error deleting category: " . $conn->error;
    }
}

//Delete products
if (isset($_POST['delete_product'])) {
    $category_name = $_POST['category_name'];
    $product_name = $_POST['product_name'];

    // Prepare and execute SQL statement to delete the selected product from the selected category
    $sql = "DELETE FROM categories WHERE category_name = ? AND products = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $category_name, $product_name);
    
    if ($stmt->execute()) {
        echo "Product '$product_name' from category '$category_name' deleted successfully.";
    } else {
        echo "Error deleting product: " . $conn->error;
    }
}


//Add products
if (isset($_POST['add_product'])) {
    $category_name = $_POST['category_name'];
    $product_name = $_POST['product_name'];

    // Prepare and execute SQL statement to insert the new product into the selected category
    $sql = "INSERT INTO categories (category_name, products) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $category_name, $product_name);
    
    if ($stmt->execute()) {
        echo "Product '$product_name' added to category '$category_name' successfully.";
    } else {
        echo "Error adding product: " . $conn->error;
    }
}

//



// Fetch categories from the database
$query = "SELECT distinct category_name FROM categories";
$result = $conn->query($query);

$categories = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}

// Fetch products from the database
$query_product = "SELECT distinct products FROM categories";
$product_result = $conn->query($query_product);

$products = array();
if ($product_result->num_rows > 0) {
    while ($row = $product_result->fetch_assoc()) {
        $products[] = $row;
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
        <h2>Add New Category</h2>
        <form action="base.php" method="post">
            <label for="category_name">Category Name:</label>
            <input type="text" id="category_name" name="category_name" required>
            <input class="button" type="submit" name="add_category" value="Add Category">
        </form>

        <h2>Delete Category</h2>
        <form action="base.php" method="post">
            <label for="delete_category">Choose a Category to Delete:</label>
            <select id="delete_category" name="delete_category">
            <option value="">Select a category</option>
                <?php
                // Fetch existing categories from the database
                $sql = "SELECT category_name FROM categories";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row["category_name"] . "'>" . $row["category_name"] . "</option>";
                    }
                } else {
                    echo "<option value='' disabled>No categories available</option>";
                }
                ?>
            </select>
            <input class="button" type="submit" name="delete_selected_category" value="Delete Category">
        </form>

        <h2>Delete Product</h2>
        <form action="base.php" method="post">
            <label for="category_name">Choose a Category:</label>
            <select id="category_name" name="category_name">
                <?php
                // Fetch existing categories from the database
                $sql = "SELECT DISTINCT category_name FROM categories";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row["category_name"] . "'>" . $row["category_name"] . "</option>";
                    }
                } else {
                    echo "<option value='' disabled>No categories available</option>";
                }
                ?>
            </select>
            <br><br>
            <label for="product_name">Choose a Product:</label>
            <select id="product_name" name="product_name">
                <!-- Options will be populated based on the selected category using JavaScript -->
            </select>
            <br><br>
            <input type="submit" name="delete_product" value="Delete Product">
        </form>

        <!--Add product -->
        <h2>Add Product</h2>
        <form action="base.php" method="post">
            <label for="category_name">Choose a Category:</label>
            <select id="category_name" name="category_name">
                <?php
                // Fetch existing categories from the database
                $sql = "SELECT DISTINCT category_name FROM categories";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row["category_name"] . "'>" . $row["category_name"] . "</option>";
                    }
                } else {
                    echo "<option value='' disabled>No categories available</option>";
                }
                ?>
            </select>
            <br><br>
            <label for="product_name">Enter Product Name:</label>
            <input type="text" id="product_name" name="product_name">
            <br><br>
            <input type="submit" name="add_product" value="Add Product">
        </form>


        <!-- Form for adding or deleting products -->
        <form action="base.php" method="post">
            <h3>Add/Delete Product</h3>
            <label for="category_id">Category ID:</label>
            <input type="text" id="category_id" name="category_id">
            <label for="product_name">Product Name:</label>
            <input type="text" id="product_name" name="product_name">
            <input class="button" type="submit" name="add_product" value="Add Product">
            <input class="button" type="submit" name="delete_product" value="Delete Product">
        </form>

        <!-- Form for updating details or quantity on stock -->
        <form action="base.php" method="post">
            <h2>Update Details of the Products</h2>
            <label for="category_id_update">Choose a Category:</label>
            <select id="category_id_update" name="category_id_update">
            <option value="">Select a category</option>
                <?php
                $sql = "SELECT distinct category_name FROM categories";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row["category_id"] . "'>" . $row["category_name"] . "</option>";
                    }
                }
                ?>
            </select>

            <!-- Populate dropdown with products based on selected category -->
            <label for="product_id">Choose a Product:</label>
            <select id="product_id" name="product_id">
                <?php
                $query_product = "SELECT distinct products FROM categories";
                $product_result = $conn->query($query_product);
                
                $products = array();
                if ($product_result->num_rows > 0) {
                    while ($row = $product_result->fetch_assoc()) {
                        $products[] = $row;
                    }
                }
                ?>
            </select>

            <label for="details">Details:</label>
            <textarea id="details" name="details"></textarea>
        </form>

        <form action="process_form.php" method="post" id="update_quantity_form">
            <h2>Increase the Quantity of Product</h2>
            <label for="category_id_update">Choose a Category:</label>
            <select id="category_id_update" name="category_id_update">
                <option value="">Select a category</option>
                <!-- Populate dropdown with categories from the database -->
                <?php
                $query_product = "SELECT distinct products FROM categories";
                $product_result = $conn->query($query_product);
                
                $products = array();
                if ($product_result->num_rows > 0) {
                    while ($row = $product_result->fetch_assoc()) {
                        $products[] = $row;
                    }
                }
                ?>
            </select>

            <!-- Populate dropdown with products based on selected category -->
            <label for="product_id">Choose a Product:</label>
            <select id="product_id" name="product_id">
                <?php
                $product_query = "SELECT DISTINCT products FROM categories";
                $product_result = $conn->query($product_query);

                if ($product_result->num_rows > 0) {
                    while ($row = $product_result->fetch_assoc()) {
                        echo "<option value='" . $row["category_name"] . "'>" . $row["category_name"] . "</option>";
                    }
                }
                ?>
            </select>

            <label for="quantity_on_stock">Quantity on Stock:</label>
            <input type="text" id="quantity_on_stock" name="quantity_on_stock">
            <input type="submit" name="update_quantity" value="Update Quantity">
        </form>

        <!-- Form for updating database from JSON URL -->
        <form action="process_form.php" method="post">
            <h3>Update Database from JSON URL</h3>
            <input class="button" type="submit" name="update_from_json_url" value="Update from JSON URL">
        </form>

        <!-- Form for uploading JSON file -->
        <form action="process_form.php" method="post" enctype="multipart/form-data">
            <h3>Upload JSON File</h3>
            <input type="file" name="json_file" id="json_file">
            <input type="submit" name="upload_json_file" value="Upload JSON File">
        </form>
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
                    echo "<tr><td colspan='5'>No data available in shortage table</td></tr>";
                }
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
