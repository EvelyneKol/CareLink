<?php
include 'Connection.php';


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

date_default_timezone_set('Europe/Athens');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
if (isset($_POST['categorySelect']) && isset($_POST['productSelect']) && isset($_POST['quantity'])&& isset($_POST['datetime'])) {
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
                
            } else {
                echo "Error inserting data: " . $conn->error;
            }
        } 

        $stmt->close(); }
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
$shortage = "SELECT * FROM shortage ORDER BY shortage_datetime DESC LIMIT $offset, $announcementsPerPage";
$resultShortage = $conn->query($shortage); 

// Fetch categories from the database
$sql = "SELECT distinct category_name FROM categories";
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>
<body>

    <div class="header">
        <h2><img src="images/logo1.png" alt="Logo"></h2>
        <hr>
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
                        <label for="categorySelect" class="form-label">Category</label>
                        <select id="categorySelect" class="form-control p-2" name="categorySelect">
                        <option value="">Select a category</option>
                            <?php
                            // Check if there are results
                            if ($result->num_rows > 0) {
                                // Output data of each row
                                while($row = $result->fetch_assoc()) {
                                    echo '<option value="' . htmlspecialchars($row["category_name"]) . '">' . htmlspecialchars($row["category_name"]) . '</option>';
                                }
                            } else {
                                echo '<option value="">No categories available</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-sm-6">
                        <label for="productSelect" class="form-label">Product</label>
                        <select id="productSelect" class="form-control p-2" name="productSelect">
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-sm-4"> </div>
                            <div class="col-sm-4"> 
                                <br>
                                <label for="quantity" class="form-label" >Quantity </label>
                                <input type="number" class="form-control p-2" id="quantity" name="quantity" required min="0" autocomplete="off" required ><br><br>
                            </div>
                        <div class="col-sm-4"> </div>
                    </div>


                </div>

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
                            $shortage = "SELECT * FROM shortage ORDER BY shortage_datetime DESC";
                            $resultShortage = $conn->query($shortage);

                            if ($resultShortage->num_rows > 0) {
                                // Output data of each row
                                while ($row = $resultShortage->fetch_assoc()) {
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

    
    <script>
        $(document).ready(function() {
            $('#categorySelect').change(function() {
                var category_name = $(this).val();
                $.ajax({
                    type: 'POST',
                    url: 'fetch_products.php',
                    data: {category_name: category_name},
                    dataType: 'json',
                    success: function(data) {
                        $('#productSelect').empty();
                        $('#productSelect').append('<option value="">Select Product</option>');
                        $.each(data, function(index, value) {
                            $('#productSelect').append('<option value="'+ value +'">'+ value +'</option>');
                        });
                    }
                });
            });
        });
    </script>

</body>
</html>
