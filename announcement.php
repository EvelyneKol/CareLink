<?php
$servername = "localhost";
$username = "evelina";
$password = "Evel1084599!";
$dbname = "carelink";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the submitted form data
    $category = $_POST["categorySelect"];
    $product = $_POST["productSelect"];
    $quantity = max(0, intval($_POST["quantity"])); // Ensure quantity is non-negative
    $datetime = $_POST["datetime"];

    // Check if the same entry already exists
    $existing_entry_query = "SELECT * FROM shortage WHERE shortage_category = ? AND shortage_product_name = ?";
    $stmt = $conn->prepare($existing_entry_query);
    $stmt->bind_param("ss", $category, $product);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) { // If no duplicate entry found, insert new data
        // Prepare and execute the SQL statement to insert data into the shortage table
        $insert_query = "INSERT INTO shortage (shortage_category, shortage_product_name, shortage_quantity, shortage_datetime) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_query);
        $stmt->bind_param("ssis", $category, $product, $quantity, $datetime);

        if ($stmt->execute()) {
            echo "Data inserted successfully.";
        } else {
            echo "Error inserting data: " . $conn->error;
        }
    } else {
        echo "This entry already exists.";
    }

    $stmt->close();
}

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

date_default_timezone_set('Europe/Athens');

$productsByCategory = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $category = $row["category_name"];
        $product = $row["products"];
        
        // If the category doesn't exist in the array, create a new entry
        if (!isset($productsByCategory[$category])) {
            $productsByCategory[$category] = array();
        }
        
        // Add the product to the array of products for this category
        $productsByCategory[$category][] = $product;
    }
}

//new page
// Define the number of announcements per page
$announcementsPerPage = 20;

// Fetch the total number of announcements
$totalAnnouncementsQuery = "SELECT COUNT(*) AS total FROM shortage";
$totalAnnouncementsResult = $conn->query($totalAnnouncementsQuery);
$totalAnnouncements = $totalAnnouncementsResult->fetch_assoc()['total'];

// Calculate the total number of pages
$totalPages = ceil($totalAnnouncements / $announcementsPerPage);

// Determine the current page number (default to 1 if not provided)
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

// Calculate the offset for pagination
$offset = ($currentPage - 1) * $announcementsPerPage;

// Fetch data from the shortage table for the current page
$sql = "SELECT * FROM shortage ORDER BY shortage_datetime DESC LIMIT $offset, $announcementsPerPage";
$result = $conn->query($sql);


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
    <link rel="stylesheet" href="css/announcement.css">
</head>
<body>
    <div class="header">
        <h2><img src="images/logo.png" alt="Logo" width="200"></h2>
        <p><strong>Inform people for any shortages</strong><br>Choose the items that are in low stock and announcements will appear to every member of the community so that everyone can donate!</p>
    </div>

    <div class="home">
        <i><a href="admin.php"><i class="fa fa-home" style="font-size:24px"></i>Home</a></i>
    </div>

    <div class="form">
        <div class="col1 col-sm-7">
            <h4>Submit a shortage</h4>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <div class="row">
                    <div class="col-sm-6">
                        <label for="category">Category:</label>
                        <select id="categorySelect" name="categorySelect">
                            <option value="">Select a category</option>
                            <?php foreach ($categories as $categoryItem): ?>
                                <option value="<?php echo $categoryItem['category_name']; ?>"><?php echo $categoryItem['category_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div id="productSelection" class="col-sm-6">
                        <label for="productName">Product Name:</label>
                        <select id="productSelect" name="productSelect">
                            <option value="">Select a product</option>
                            <?php foreach ($products as $productItem): ?>
                                <option value="<?php echo $productItem['products']; ?>"><?php echo $productItem['products']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <label for="quantity">Quantity:</label>
                <input type="number" id="quantity" name="quantity" required min="0"><br><br>

                <!-- datetime field will be automatically filled with current datetime -->
                <input type="hidden" id="datetime" name="datetime" value="<?php echo date('Y-m-d\TH:i:s'); ?>">
                
                <button class="submit" type="submit">Submit</button>
                <button class="reset" type="reset">Reset</button>
            </form>
        </div>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="table-container">
                    <h4>Shortage Table</h4>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Category</th>
                                <th>Product Name</th>
                                <th>Quantity</th>
                                <th>Date & Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Fetch data from the shortage table
                            $sql = "SELECT * FROM shortage ORDER BY shortage_datetime DESC";
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                // Output data of each row
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $row["id_shortage"] . "</td>";
                                    echo "<td>" . $row["shortage_category"] . "</td>";
                                    echo "<td>" . $row["shortage_product_name"] . "</td>";
                                    echo "<td>" . $row["shortage_quantity"] . "</td>";
                                    echo "<td>" . $row["shortage_datetime"] . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5'>No data available in shortage table</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="pagination">
                <?php for ($page = 1; $page <= $totalPages; $page++): ?>
                    <a href="?page=<?php echo $page; ?>"<?php if ($page === $currentPage) echo ' class="active"'; ?>><?php echo $page; ?></a>
                <?php endfor; ?>
            </div>
        </div>
    </div>
</body>
</html>
