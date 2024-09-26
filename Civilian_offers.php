<?php
include 'Connection.php'; // αρχείο για σύνδεση με τη βάση δεδομένων

//έλεγχος συνδεσης
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


session_start(); // Εκκίνηση session

// Έλεγχος αν ο χρήστης είναι συνδεδεμένος και αν έχει τον ρόλο του "civilian"
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'civilian') {
    header('Location: sign_in.php');
    exit(); }

// Έλεγχος αν το όνομα χρήστη είναι αποθηκευμένο στα cookies
if(isset($_COOKIE['username'])){
  $defaultUsername = $_COOKIE['username']; //αποθήκευση του ονόματος αν υπάρχει στην μεταβλητή
} else {
  $defaultUsername = ""; //αλλιώς κενό
}

// Κλείσιμο της σύνδεσης με τη βάση δεδομένων
$conn->close();
?>

<!DOCTYPE html>
<html lang="el">

<head>
  <meta charset="utf-8">
  <title>CareLink</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" sizes="96x96" href="images/favicon.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="Civilian.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

</head>

<body>

  <div class="navbar">
      <img src="images/logo1.png" alt="Logo">
      <ul class="nav">
          <li><a href="Civilian.php"><i class="fa fa-home" style="font-size:24px"></i> Home</a></li>
          <p> | </p>
          <li>
            <a href="civilian_requests.php">
              <i class="fa fa-bullhorn" style="font-size:24px"></i>  Requests
            </a>
          </li>
      </ul>
      <ul class="nav">
          <li><a href="logout.php"><i class="fa fa-sign-out" style="font-size:24px"></i> Log out</a></li>
      </ul>
  </div>

    <div class="Secondsection" style="display: none;">   
        <h2 id="txtUsername"><?php echo $defaultUsername; ?></h2>
    </div>

    <div class="thirdsection">
        <h3>My offers</h3>
        <hr>
        <div id="userOffers"></div>
        <br>
        <h3>Completed offers</h3>
        <hr>
        <div id="pastOffers"></div>
    </div>

   
  <div class="Footer container-fluid">
    <div class="row">
      <div class="col-sm-3">
        <h2>Social Media</h2>
        <nav class="nav02">
          <a href="https://www.linkedin.com/in/george-karagiannis-00a683222/" class="fa fa-linkedin"></a>
          <a href="https://www.facebook.com/george.karagiannis.9406" class="fa fa-facebook"></a>
          <a href="https://www.instagram.com/_karagiannis_g/" class="fa fa-instagram"></a>
        </nav>
      </div>
      <div class="col-sm-6">
        <h2>Licences</h2>
        <br>
        <p>© George Karagiannis/Ceid/Upatras/Year 2023-2024</p>
        <p>© Evelina Kolagki/Ceid/Upatras/Year 2023-2024</p>
      </div>
      <div class="column col-sm-3">
        <h2>Contact info</h2>
        <br>
        <ul class="list-unstyled">
          <li>Email: <span class="email">Karagiannis.giorg@gmail.com</span></li>
          <br>
          <li>Phone: +30 123456789</li>
        </ul>
      </div>
    </div>
  </div>


  <script>
        function showOffers(username) {
            var xmlhttp = new XMLHttpRequest(); // Δημιουργία νέου αιτήματος AJAX
            xmlhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("userOffers").innerHTML = this.responseText; // Ενημέρωση του περιεχομένου με τις προσφορές του χρήστη
                }
            };
            xmlhttp.open("GET", "load_civilian_offers.php?q=" + username, true); // Αποστολή αιτήματος GET με το όνομα χρήστη
            xmlhttp.send(); // Εκτέλεση του αιτήματος
        }

        document.addEventListener("DOMContentLoaded", function () {

            var defaultUsername = document.getElementById('txtUsername').innerText;  // Απόκτηση του ονόματος χρήστη από το h2
            // Κλήση της συνάρτησης για την εμφάνιση των προσφορών του χρήστη
            showOffers(defaultUsername);
        });

        function showPastOffers(username) {
            var xmlhttp = new XMLHttpRequest(); // Δημιουργία νέου αιτήματος AJAX
            xmlhttp.onreadystatechange = function () {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("pastOffers").innerHTML = this.responseText; // Ενημέρωση του περιεχομένου με τις ολοκληρωμένες προσφορές του χρήστη
                }
            };
            xmlhttp.open("GET", "load_past_Offers_civilian.php?q=" + username, true); // Αποστολή αιτήματος GET με το όνομα χρήστη
            xmlhttp.send(); // Εκτέλεση του αιτήματος
        }

        document.addEventListener("DOMContentLoaded", function () {
            var defaultUsername = document.getElementById('txtUsername').innerText; // Απόκτηση του ονόματος χρήστη από το txtUsername
            // Κλήση της συνάρτησης για την εμφάνιση των ολοκληρωμένων προσφορών του χρήστη
            showPastOffers(defaultUsername);
        });
    

        function deleteOffers(OfferId, category, product, quantity) {

            var url = "delete_offer.php"; // Ορισμός της διεύθυνσης για τη διαγραφή προσφοράς
            var formData = new FormData(); // Δημιουργία αντικειμένου FormData για αποστολή δεδομένων μέσω POST
            formData.append("OfferId", OfferId);
            formData.append("category", category);
            formData.append("product", product);
            formData.append("quantity", quantity);

            var xhr = new XMLHttpRequest();
            xhr.open("POST", url, true); // Αποστολή POST αιτήματος

            xhr.onload = function () {
                if (xhr.status === 200) {
                    console.log("Success:", xhr.responseText); //μήνυμα επιτυχίας
                    location.reload(); // Ανανέωση της σελίδας μετά τη διαγραφή

                } else {
                    console.error("Error:", xhr.statusText); // Εμφάνιση μηνύματος σφάλματος 
                }
            };

            xhr.onerror = function () {
                console.error("Network error"); // Εμφάνιση μηνύματος σφάλματος δικτύου
            };

            xhr.send(formData); // Αποστολή των δεδομένων
            console.log("AJAX request sent"); // Εμφάνιση μηνύματος επιβεβαίωσης
        }

  </script>
    
</body>

</html>
