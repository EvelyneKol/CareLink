<?php
//σύνδεση με βάση
include 'Connection.php';

//έλεγχος σύνδεσης
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['name'];
    $last_name = $_POST['lastname'];
    $username = $_POST['username'];
    $phone = (int)$_POST['phone'];
    $password = $_POST['password'];
    $location = $_POST['address'];

    // Insert τα δεδoμένα του χρήστη στο civilian table
    $stmt = $conn->prepare("INSERT INTO civilian (civilian_first_name, civilian_last_name, civilian_username, civilian_number, civilian_password, civilian_location) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssiss", $first_name, $last_name, $username, $phone, $password, $location);
    if ($stmt->execute()) {
        // μετα την επιτυχημενη εγγραφη ξανα φορτώνει την ίδια σελίδα
        header("Location: sign_in.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    // κλείσιμο statement και σύνδεσης με database
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <link rel="icon" type="image/jpg" sizes="96x96" href="favicon-96x96.png">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="form.css">
  <title>Signup Form</title>
</head>

<body>
  <div class="container">
    <div class="form-container">
      <div class="tabs">
        <a href="sign_in.php"> <button onclick="showTab('signin')">Sign In</button></a>
        <button onclick="showTab('signup')" class="active">Sign Up</button>
      </div>
      <div id="signup-tab" class="tab">
          <p> <strong>CareLink</strong> is even better with an account!</p>
          <form id="signup-form" action="<?php echo $_SERVER['PHP_SELF']; ?>" onsubmit="return validatePassword()"
            method="post">
            <label for="name">First Name</label>
            <input type="text" placeholder="Enter Your First Name" id="name" name="name" autocomplete="on"
                spellcheck="false" required>
            <label for="lastname">Last Name</label>
            <input type="text" placeholder="Enter Your Last Name" id="lastname" name="lastname"
                autocomplete="on" spellcheck="false" required>
            <label for="username">Username</label>
            <input type="text" placeholder="Enter Your Username" id="username" name="username" autocomplete="on"
                spellcheck="false" required>
            <label for="address">Address</label>
            <input type="text" placeholder="Enter Your Address" id="address" name="address" 
                spellcheck="false" required readonly>
            <label for="phone">Phone</label>
            <input type="text" placeholder="Enter Your Phone Number" id="phone" name="phone" autocomplete="on"
                spellcheck="false" required>
            <label for="password">Password</label>
            <input type="password" placeholder="Enter Your Password" id="password" name="password"
                autocomplete="on" 
                title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters"
                pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                required>
            <label for="showpass" id="rlabel">Show Password</label>
            <input type="checkbox" id="showpass" name="showpass" onclick="passwordvisibility()">
            <button class="button" type="reset" value="reset">Reset</button>
            <div class="button-center">
                <button class="button1" type="submit"><strong><span>Submit</span></strong></button>
            </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    var currentTab = 0; // η τρέχουσα καρτέλα (sign up) τίθεται στο 0 
    showTab(currentTab); // προβολή τρέχπυσασ καρτέλας

    function showTab(n) {
      // συνάρτηση για εμφάνιση καρτέλας 
      var x = document.getElementsByClassName("tab");
      x[n].style.display = "block";

    }

    function passwordvisibility() {
      var x = document.getElementById("password");
      if (x.type === "password") {
        x.type = "text";
      } else {
        x.type = "password";
      }
    }

    function validatePassword() { //συνάρτηση ελέγχου για έγκυρο κωδικό 
      var name = document.getElementById("name").value;
      var lastname = document.getElementById("lastname").value;
      var username = document.getElementById("username").value;
      var address = document.getElementById("address").value;
      var phone = document.getElementById("phone").value;
      var password = document.getElementById("password").value;

      if (name === "" || lastname === "" || username === "" || address === "" || phone === "" || password === "") {
        alert("Please fill in all fields.");
        return false;
      } else {
        alert("Account created!");
        return true;
        window.location.href = "sign_in.php";
        return false;

      }
    }
    
    function getLocation() {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(Position);
      } else {
        alert("Geolocation is not supported by this browser.");
      }
    }

    function Position(position) {
      var latitude = position.coords.latitude;
      var longitude = position.coords.longitude;
      var geolocation = latitude + ", " + longitude;

      // εισάγει τισ συντετγμένες στο πεδίο τοποθεσία 
      document.getElementById("address").value = geolocation;
    }

    // καλείται η getLocation μόλις η σελίδα φορτωθεί 
    getLocation();
  
  </script>

</body>

</html>