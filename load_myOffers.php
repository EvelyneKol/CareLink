<!DOCTYPE html>
<html>
<head>
<style>

.album-container {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  align-items: center;
}

.coloured-cards .card {
  margin-top: 30px;
}

.card {
  border-radius: 4px;
  box-shadow: 0 2px 2px rgba(204, 197, 185, 0.5);
  background-color: #faf3cd; 
  color: #252422;
  margin-bottom: 20px;
  position: relative;
  margin: 0 10px; 
  padding-right: 20px;
  padding-bottom: 50px;
  padding-left: 20px;
}

h3, .h3 {
  text-align:center;
  font-size: 1.5em;
  font-weight: 600;
  line-height: 1.2em;
  margin: 10px 0; 
}

.card .description {
  font-size: 16px;
  color: #66615b;
  margin-bottom: 10px; 
}

.card button{
  margin-top:5px;
  float:right;
}

.DeleteOffer {
  margin-top: 20px;
  padding: 5px 14px;
  border-radius: 4px;
  background-color: rgba(221, 221, 221, 0.662);
  color: black;
  border: none;
  transition-duration: 0.4s;
}

.DeleteOffer:active,
.DeleteOffer:hover {
  background-color: rgb(195, 0, 0);
  color: rgb(255, 255, 255);
}

.AcceptOffer {
  margin-top: 20px;
  margin-right: 20px;
  padding: 5px 14px;
  border-radius: 4px;
  background-color: rgba(221, 221, 221, 0.662);
  color: black;
  border: none;
  transition-duration: 0.4s;
}

.AcceptOffer:active,
.AcceptOffer:hover {
  background-color: rgb(33, 195, 0);
  color: rgb(255, 255, 255);
}

.content-card {
  margin-top: 20px;
}
</style>
</head>
<body>

<div class="album-container">

<?php
// Ξεκινάει η συνεδρία για να έχουμε πρόσβαση στα δεδομένα που έχουμε αποθηκεύσει
session_start();

// Ανάκτηση των δεδομένων από τη συνεδρία
$myOffers = isset($_SESSION['myOffers']) ? $_SESSION['myOffers'] : [];

// Βρόχος για την εμφάνιση των δεδομένων κάθε αιτήματος σε μορφή καρτέλας
foreach ($myOffers as $row) {
    echo '<div class="card">';
    echo '<div class="content">';
    echo '<h3 class="title">'.htmlspecialchars($row["civilian_first_name"]).' '.htmlspecialchars($row["civilian_last_name"]).' </h3>';
    echo '<p class="description"> Category: ' . htmlspecialchars($row["offer_category"]) . '</p>';
    echo '<p class="description"> Product: ' . htmlspecialchars($row["offer_product_name"]) . '</p>';
    echo '<p class="description"> Num of People: ' . htmlspecialchars($row["offer_quantity"]) . '</p>';
    echo '<p class="description"> Date Posted: ' . htmlspecialchars($row["offer_date_posted"]) . '</p>';
    echo '<p class="description"> Phone: +30 ' . htmlspecialchars($row["civilian_number"]) . '</p>';
    echo '<p class="description"> State: ' . htmlspecialchars($row["offer_status"]) . '</p>';
    echo '<button class="DeleteOffer" onclick="delete_offer(' . htmlspecialchars($row["offer_id"]) . ')">Delete</button>';
    echo '<button class="AcceptOffer" onclick="accept_offer(\'' . htmlspecialchars($row["offer_id"]) . '\', \'' . htmlspecialchars($row["offer_category"]) . '\', \'' . htmlspecialchars($row["offer_product_name"]) . '\', \'' . htmlspecialchars($row["offer_quantity"]) . '\', \'' . htmlspecialchars($row["latitude"]) . '\', \'' . htmlspecialchars($row["longitude"]) . '\')" disabled>Accept</button>';
    echo '</div>';
    echo '</div>';
}
?>

</div>
</body>
</html>
