<?php
    $servername = "localhost";
    $username = "root";
    $password = "karagiannis";
    $dbname = "test";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Failed to connect to MySQL: " . $conn->connect_error);
    }

    // Read the json file contents 
    $jsondata = file_get_contents('test_json.json');
    // Convert json object to php associative array
    $data = json_decode($jsondata, true);

    // Function to insert categories into the database
    function insertCategories($conn, $categories) {
        foreach ($categories as $category) {
            $category_id = $category['id'];
            $category_name = $category['category_name'];
        }
    }

    // Insert categories
    insertCategories($conn, $data['categories']);

    // Loop through items in the JSON data
    foreach ($data['items'] as $item) {
        // Get the details
        $category_id = $item['category'];
        $category_name = '';
        // Find the category name that corresponds to the category ID
        foreach ($data['categories'] as $category) {
            if ($category['id'] == $category_id) {
                $category_name = $category['category_name'];
                break;
            }
        }
        if ($category_name == '') {
            // Handle if category name is not found
            echo "Category name not found for category ID: $category_id";
            continue; // Skip this item
        }
        $product_name = $item['name'];
        // Concatenate details into a string
        $details = '';
        foreach ($item['details'] as $detail) {
            $details .= $detail['detail_name'] . ' ' . $detail['detail_value'] ;
        }
        // Remove trailing comma and space
        $details = rtrim($details, ', ');

        // Insert into MySQL table
        $sql = "INSERT INTO categories (base_id_categories, category_id, category_name, products, quantity_on_stock, details)
                VALUES (1, null , '$category_name', '$product_name', 50, '$details')";
        if ($conn->query($sql) === FALSE) {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    // Close the database connection
    $conn->close();
?>
