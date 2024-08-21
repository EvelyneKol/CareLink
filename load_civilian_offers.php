<!DOCTYPE html>
<html>
<head>
<style>
* {
  margin: 0;
  padding: 0;
  font-family: 'Work Sans', sans-serif;
}

 /*Second section*/
 .Secondsection {
  margin-top: 20px;
  font-family: 'Work Sans', sans-serif;
  float: center;
}

.Secondsection h3,
.Secondsection p {
  margin: 10px 0px 5px 0px;
  text-align: center;
}

.reminder-notes {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  align-items: center;
  gap: 20px; 
}

.reminder-notes ul {
  display: flex;
  flex-wrap: wrap;
  list-style-type: none; 
  padding: 0;
  display: flex;
  flex-wrap: wrap;
  text-align: center;
}

.reminder-notes ul li {
  background-color: #faf3cd; 
  color: black; 
  border: 1px solid #ccc; 
  border-radius: 5px; 
  padding: 20px;
  margin: 20px;
  width: 240px; 
  height: 280px; 
  overflow: hidden; 
  box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1); 
  display: flex;
  flex-direction: column;
  justify-content: space-between; 
}

.reminder-notes ul li h2 {
  font-size: 1.2em; 
  margin: 0;
  padding: 0;
}

.reminder-notes ul li p {
  font-size: 0.9em; 
  margin: 5px 0 0 0;
  padding: 0;
}

.delete {
  margin-top: 20px;
  padding: 5px 14px;
  border-radius: 4px;
  background-color: rgba(221, 221, 221, 0.662);
  color: black;
  border: none;
  transition-duration: 0.4s;
}

.delete button{
  margin-top:5px;
  float:right;
}

.delete:active,
.delete:hover {
  background-color: rgb(0, 146, 195);
  color: rgb(255, 255, 255);
}


</style>
</head>
<body>
  
<div class="Secondsection">
  <div class="reminder-notes">
    <?php
    //σύνδεση και έλεγχος
    include 'Connection.php';

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }


    if ($_SERVER["REQUEST_METHOD"] == "GET") {
      // Παίρνει το όνομα χρήστη από το αίτημα GET
        $username = $_GET['q'];

        //query για select προσφορών εν αναμονή και σε σε διανομή 
        $sql = "SELECT * FROM offer WHERE offer_civilian = ? AND offer_status IN ('WAITING', 'ON THE WAY')";
        $stmt = $conn->prepare($sql);
        // Δέσμευση της παραμέτρου username στην SQL δήλωση
        $stmt->bind_param("s", $username);

        $result = $stmt->execute();

        // Έλεγχος αν η εκτέλεση του ερωτήματος ήταν επιτυχής
        if (!$result) {
            die("Error: " . $stmt->error);
        }

        // Δέσμευση των αποτελεσμάτων του ερωτήματος στις αντίστοιχες μεταβλητές
        $stmt->bind_result($offer_id, $offer_civilian, $offer_category, $offer_product_name, $offer_quantity, $offer_date_posted, $offer_time_posted, $offer_status, $complete_offer);
        echo '<ul>';
        // Loop στις εγγραφές και εμφάνιση τουσ 
        while ($stmt->fetch()) {
            echo '<li>';  
            echo '<h2 class="title"> Your Offer for <strong>' . htmlspecialchars($offer_category) . '</strong></h2>';
            echo '<p> Product: ' . htmlspecialchars($offer_product_name) . '</p>';
            echo '<p> Num of People: ' . htmlspecialchars($offer_quantity) . '</p>';
            echo '<p> Date Posted: ' . htmlspecialchars($offer_date_posted) . '</p>';
            echo '<p> Time Posted: ' . htmlspecialchars($offer_time_posted) . '</p>';

            // Έλεγχος της κατάστασης της προσφοράς και εμφάνιση του κατάλληλου μηνύματος
            if($offer_status == "WAITING") {
              echo '<p> Status of offer: <strong>' . htmlspecialchars($offer_status) . '</strong></p>';
              echo '<button class="delete" onclick="deleteOffers(\'' . htmlspecialchars($offer_id) . '\', \'' . htmlspecialchars($offer_category) . '\', \'' . htmlspecialchars($offer_product_name) . '\', \'' . htmlspecialchars($offer_quantity) . '\')">Delete</button>';
            } else if($offer_status == "ON THE WAY") {
              echo '<p> Status of offer: <strong>' . htmlspecialchars($offer_status) . '</strong></p>';
            }else{
              echo '<p> Time Completed: ' . htmlspecialchars($complete_offer) . '</p>';
              echo '<p> Status of offer: <strong>' . htmlspecialchars($offer_status) . '</strong></p>';
            }
            echo '</li>';
        }
        echo '</ul>';

        $stmt->close();
    }

    //κλήση σύνδεσης
    $conn->close();

    ?>
  </div>
</div>
</body>
</html>
