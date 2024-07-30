<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "karagiannis";
$dbname = "test";

// Δημιουργία σύνδεσης
$conn = new mysqli($servername, $username, $password, $dbname);

// Έλεγχος σύνδεσης
if ($conn->connect_error) {
    $_SESSION['message'] = "Failed to connect to MySQL: " . $conn->connect_error;
    header("Location: {$_SERVER['HTTP_REFERER']}");
    exit();
}

if (isset($_FILES['json_file'])) {
    $file_tmp = $_FILES['json_file']['tmp_name'];
    $file_name = $_FILES['json_file']['name'];
    
    // Βεβαιωθείτε ότι το αρχείο που ανεβάστηκε είναι JSON
    $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
    if ($file_ext != 'json') {
        $_SESSION['message'] = "Please upload a valid JSON file.";
        header("Location: {$_SERVER['HTTP_REFERER']}");
        exit();
    }
    
    // Ανάγνωση του περιεχομένου του αρχείου JSON
    $jsondata = file_get_contents($file_tmp);
    // Μετατροπή του αντικειμένου JSON σε συνειρμικό πίνακα PHP
    $data = json_decode($jsondata, true);

    if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
        $_SESSION['message'] = "Error decoding JSON file.";
        header("Location: {$_SERVER['HTTP_REFERER']}");
        exit();
    }

    // Συνάρτηση για εισαγωγή κατηγοριών στη βάση δεδομένων
    function insertCategories($conn, $categories) {
        foreach ($categories as $category) {
            $category_id = $category['id'];
            $category_name = $category['category_name'];
            
        }
    }

    // Εισαγωγή κατηγοριών
    insertCategories($conn, $data['categories']);

    // Βρόχος για τα αντικείμενα στα δεδομένα JSON
    foreach ($data['items'] as $item) {
        // Λήψη των λεπτομερειών
        $category_id = $item['category'];
        $category_name = '';
        // Εύρεση του ονόματος της κατηγορίας που αντιστοιχεί στο ID της κατηγορίας
        foreach ($data['categories'] as $category) {
            if ($category['id'] == $category_id) {
                $category_name = $category['category_name'];
                break;
            }
        }
        if ($category_name == '') {
            // Χειρισμός αν δεν βρεθεί το όνομα της κατηγορίας
            $_SESSION['message'] = "Category name not found for category ID: $category_id";
            continue; // Παράλειψη αυτού του αντικειμένου
        }
        $product_name = $item['name'];
        $quantity = (int)$item['quantity'];
        // Συνένωση των λεπτομερειών σε μια συμβολοσειρά
        $details = '';
        foreach ($item['details'] as $detail) {
            $details .= $detail['detail_name'] . ' ' . $detail['detail_value'] . ', ';
        }
        // Αφαίρεση του τελικού κόμματος και του κενού
        $details = rtrim($details, ', ');

        $Checkprod = $conn->prepare("SELECT quantity_on_stock FROM categories WHERE category_name = ? AND products = ? ");
        $Checkprod->bind_param("ss", $category_name, $product_name);
        $Checkprod->execute();
        $Checkprod->store_result();

        if ($Checkprod->num_rows > 0) {
            $Checkprod->bind_result($currentQuantity);
            $Checkprod->fetch();
            $newQuantity = $currentQuantity + $quantity;

            $stmtUpdate = $conn->prepare("UPDATE categories SET quantity_on_stock = ? WHERE category_name = ? AND products = ?");
            $stmtUpdate->bind_param("iss", $newQuantity, $category_name, $product_name);
            $stmtUpdate->execute();
            $stmtUpdate->close();
        } else {
            $stmtInsert = $conn->prepare("INSERT INTO categories (base_id_categories, category_id, category_name, products, quantity_on_stock, details)VALUES (1, null , ?, ?, ?, ?)");
            $stmtInsert->bind_param("ssis", $category_name, $product_name, $quantity, $details);
            $stmtInsert->execute();
            $stmtInsert->close();
        }
        $Checkprod->close();
    }
}

// Κλείσιμο της σύνδεσης στη βάση δεδομένων
$conn->close();

header("Location: {$_SERVER['HTTP_REFERER']}");
exit();
?>
