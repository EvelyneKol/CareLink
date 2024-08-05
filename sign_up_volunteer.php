<?php
//σύνδεση με βάση
include 'Connection.php';

//έλεγχος σύνδεσης
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//ξεκινάει session που κρατά τα credits
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['name'];
    $last_name = $_POST['lastname'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // έλεγχος με το select query αν ο εθελοντής υπάρχει στο volunteer table
    $check_query = "SELECT * FROM volunteer WHERE vol_username = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("s", $username);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        // εάν το usernmae υπάρχει, εμφάνισε μήνυμα και ανακατεύθυνε στο sign_up_volunteer.php
        echo "<script>alert('Have already registered!'); window.location.href = 'sign_up_volunteer.php';</script>";
        exit(); 
    } else {
        // το Username δεν υπάρχει άρα κάνει insert 
        $insert_stmt = $conn->prepare("INSERT INTO volunteer (vol_first_name, vol_last_name, vol_username, vol_password) VALUES (?, ?, ?, ?)");
        $insert_stmt->bind_param("ssss", $first_name, $last_name, $username, $password);

        if ($insert_stmt->execute()) {
            // μήνυμα επιτυχίας
            $_SESSION['success_message'] = "New volunteer has been successfully registered!";
            header("Location: sign_up_volunteer.php"); 
            exit();
        } else {
            echo "Error: " . $insert_stmt->error;
        }
    }

    //Κλείσιμο statements 
    $check_stmt->close();
    $insert_stmt->close();
    //τερματισμός σύνδεσης
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
  <title>Volunteer Registration</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <style>
        .success-message {
            color: green;
            text-align: center;
            margin: 10px 0;
            display: none; 
        }
    </style>
</head>

<body>
  <div class="container">
    <div class="form-container">
      <i><a href="admin.php"><i class="fa fa-home" style="font-size:24px" ></i>Home</a></i>
      <div id="signup-tab" class="tab">
        <p>Add a volunteer to<strong>CareLink</strong> !</p>
        <!-- Το action ορίζει τη διεύθυνση URL στην οποία θα σταλούν τα δεδομένα της φόρμας όταν υποβληθεί. -->
        <form id="signup-form" action="<?php echo $_SERVER['PHP_SELF']; ?>" onsubmit="return validatePassword()"
          method="post"> <!-- επιστροφή των δεδομένων στην ίδια σελίδα -->
          <!-- φόρμα εγγραφής volunteer -->
          <label for="name">First Name</label>
                    <input type="text" placeholder="Enter Your First Name" id="name" name="name" autocomplete="on"
                        spellcheck="false" required>
                    <label for="lastname">Last Name</label>
                    <input type="text" placeholder="Enter Your Last Name" id="lastname" name="lastname"
                        autocomplete="on" spellcheck="false" required>
                    <label for="username">Username</label>
                    <input type="text" placeholder="Enter Your Username" id="username" name="username" autocomplete="on"
                        spellcheck="false" required>
                    <label for="password">Password</label>
                    <input type="password" placeholder="Enter Your Password" id="password" name="password"
                    autocomplete="on" pattern="(?=.*\d)(?=.*[a-z]).{8,}"
                    title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters"
                    required>
                    <label for="showpass" id="rlabel">Show Password</label>
                    <input type="checkbox" id="showpass" name="showpass" onclick="passwordvisibility()">
                    <button class="button" type="reset" value="reset">Reset</button>
                    <?php  //με επιτυχημένο session προβάλει μήνυμα το οποίο μετά αφαιρεί για να μην ξανα εμφανιστεί
                    if (isset($_SESSION['success_message'])) {
                        echo "<p class='success-message' id='success-message'>" . $_SESSION['success_message'] . "</p>";
                        unset($_SESSION['success_message']); 
                    }
                    ?>
                    <div class="button-center">
                        <button class="button1" type="submit"><strong><span>Submit</span></strong></button>
                    </div>
                </form>
      </div>
    </div>
  </div>

  <script>
    var currentTab = 0; // η τρέχουσα καρτέλα θέτεται ως 1η(0)
    showTab(currentTab); // προβολή τρέχουσας καρτέλας

    function showTab(n) {
       //προβάλει την καρτέλα που παίρνει ως είσοδο
      var x = document.getElementsByClassName("tab");
      x[n].style.display = "block";

    }

    //προβάλει τον κωδικό 
    function passwordvisibility() {
      var x = document.getElementById("password");
      if (x.type === "password") {
        x.type = "text";
      } else {
        x.type = "password";
      }
    }

    function validatePassword() {
      var name = document.getElementById("name").value;
      var lastname = document.getElementById("lastname").value;
      var username = document.getElementById("username").value;
      var password = document.getElementById("password").value;

      if (name === "" || lastname === "" || username === "" || password === "") {
        alert("Please fill in all fields.");  //εάν υπάρχουν κενά πεδία
        return false;
      } else if (password.length < 8 || password.length > 15 || !/[a-z]/.test(password)) {
        alert("Password must be 8-15 characters long and include at least one capital letter and one symbol.");
        return false;
      } else {
        // προβολή alert
        alert("Account created!");
        return true;
        // ανακατεύθυνση πίσω στον admin.php
        window.location.href = "admin.php";
        // Επιστρέφει false για να αποτρέψει το submission της φόρμας
        return false;
      }
    }

  </script>

</body>

</html>