<?php
include 'Connection.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['name'];
    $last_name = $_POST['lastname'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if the username already exists in the 'volunteer' table
    $check_query = "SELECT * FROM volunteer WHERE vol_username = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("s", $username);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        // Username exists, show alert and redirect back to sign_up_volunteer.php
        echo "<script>alert('Have already registered!'); window.location.href = 'sign_up_volunteer.php';</script>";
        exit(); // Stop further execution
    } else {
        // Username doesn't exist, proceed with insertion
        $insert_stmt = $conn->prepare("INSERT INTO volunteer (vol_first_name, vol_last_name, vol_username, vol_password) VALUES (?, ?, ?, ?)");
        $insert_stmt->bind_param("ssss", $first_name, $last_name, $username, $password);

        if ($insert_stmt->execute()) {
            // Set a session variable with the success message
            $_SESSION['success_message'] = "New volunteer has been successfully registered!";
            header("Location: sign_up_volunteer.php");
            exit();
        } else {
            echo "Error: " . $insert_stmt->error;
        }
    }

    // Close statements and the database connection
    $check_stmt->close();
    $insert_stmt->close();
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
            display: none; /* Hidden by default */
        }
    </style>
</head>

<body>
  <div class="container">
    <div class="form-container">
      <i><a href="admin.php"><i class="fa fa-home" style="font-size:24px" ></i>Home</a></i>
      <div id="signup-tab" class="tab">
        <p>Add a volunteer to<strong>CareLink</strong> !</p>
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
                    <label for="password">Password</label>
                    <input type="password" placeholder="Enter Your Password" id="password" name="password"
                        autocomplete="on" pattern="(?=.*\d)(?=.*[a-z]).{8,}"
                        title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters"
                        required>
                       <!-- Hidden field with a default value of 0 -->
                    <input type="hidden" id="lastElement" name="lastElement" value="0">
                    <label for="showpass" id="rlabel">Show Password</label>
                    <input type="checkbox" id="showpass" name="showpass" onclick="passwordvisibility()">
                    <button class="button" type="reset" value="reset">Reset</button>
                    <?php
                    if (isset($_SESSION['success_message'])) {
                        echo "<p class='success-message' id='success-message'>" . $_SESSION['success_message'] . "</p>";
                        unset($_SESSION['success_message']); // Unset the message after displaying it
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
    var currentTab = 0; // Current tab is set to be the first tab (0)
    showTab(currentTab); // Display the current tab

    function showTab(n) {
      // This function will display the specified tab of the form...
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

    function validatePassword() {
      var name = document.getElementById("name").value;
      var lastname = document.getElementById("lastname").value;
      var username = document.getElementById("username").value;
      var password = document.getElementById("password").value;

      if (name === "" || lastname === "" || username === "" || password === "") {
        alert("Please fill in all fields.");
        return false;
      } else if (password.length < 8 || password.length > 15 || !/[a-z]/.test(password)) {
        alert("Password must be 8-15 characters long and include at least one capital letter and one symbol.");
        return false;
      } else {
        // Display the alert
        alert("Account created!");
        return true;
        // Redirect to another page after alert
        window.location.href = "admin.php";
        // Return false to prevent form submission
        return false;
      }
    }

  </script>

</body>

</html>