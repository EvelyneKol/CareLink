<!DOCTYPE html>
<html>
<head>
<style>
/* Your existing CSS */
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
  background-color: #ffe28c; /* Yellow color */
  color: #252422;
  margin-bottom: 20px;
  position: relative;
  margin: 0 10px; /* Added margin to each card for spacing */
  padding-right: 20px;
  padding-bottom: 50px;
  padding-left: 20px;
}

h3, .h3 {
  text-align:center;
  font-size: 1.5em;
  font-weight: 600;
  line-height: 1.2em;
  margin: 10px 0; /* Adjusted margin to separate the heading from other content */
}

.card .description {
  font-size: 16px;
  color: #66615b;
  margin-bottom: 10px; /* Adjusted margin to separate the description from other content */
}

.card button{
  margin-top:5px;
  float:right;
}

.DeleteReq {
  margin-top: 20px;
  padding: 5px 14px;
  border-radius: 4px;
  background-color: rgba(221, 221, 221, 0.662);
  color: black;
  border: none;
  transition-duration: 0.4s;
}

.DeleteReq:active,
.DeleteReq:hover {
  background-color: rgb(195, 0, 0);
  color: rgb(255, 255, 255);
}

.DeliverReq {
  margin-top: 20px;
  margin-right: 20px;
  padding: 5px 14px;
  border-radius: 4px;
  background-color: rgba(221, 221, 221, 0.662);
  color: black;
  border: none;
  transition-duration: 0.4s;
}

.DeliverReq:active,
.DeliverReq:hover {
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
session_start();

// Retrieve data from session
$myRequests = isset($_SESSION['myRequests']) ? $_SESSION['myRequests'] : [];

foreach ($myRequests as $row) {
    echo '<div class="card">';
    echo '<div class="content">';
    echo '<h3 class="title">'.htmlspecialchars($row["civilian_first_name"]).' '.htmlspecialchars($row["civilian_last_name"]).' </h3>';
    echo '<p class="description"> Category: ' . htmlspecialchars($row["request_category"]) . '</p>';
    echo '<p class="description"> Product: ' . htmlspecialchars($row["request_product_name"]) . '</p>';
    echo '<p class="description"> Num of People: ' . htmlspecialchars($row["persons"]) . '</p>';
    echo '<p class="description"> Date Posted: ' . htmlspecialchars($row["request_date_posted"]) . '</p>';
    echo '<p class="description"> Phone: +30 ' . htmlspecialchars($row["civilian_number"]) . '</p>';
    echo '<p class="description"> State: ' . htmlspecialchars($row["state"]) . '</p>';
    echo '<button class="DeleteReq" onclick="delete_request(' . htmlspecialchars($row["id_request"]) . ')">Delete</button>';
    echo '<button class="DeliverReq" onclick="deliver_requests(\'' . htmlspecialchars($row["id_request"]) . '\', \'' . htmlspecialchars($row["request_category"]) . '\', \'' . htmlspecialchars($row["request_product_name"]) . '\', \'' . htmlspecialchars($row["persons"]) . '\')" disabled>Deliver</button>';
    echo '</div>';
    echo '</div>';
}
?>
</div>
</body>
</html>
