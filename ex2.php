<?php
$servername = "localhost";
$username = "root";
$password = "karagiannis";
$dbname = "test";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['name'];
    $last_name = $_POST['lastname'];
    $username = $_POST['username'];
    $phone = (int)$_POST['phone']; // Convert to integer
    $password = $_POST['password']; 

    // Insert user information into the 'civilian' table
    $stmt = $conn->prepare("INSERT INTO civilian (first_name, last_name, username, phone, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssis", $first_name, $last_name, $username, $phone, $password);

    if ($stmt->execute()) {
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the statement and the database connection
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
  <link rel="stylesheet" href="css/form.css">
  <title>Signup Form</title>
</head>

<body>
  <div class="container">
    <div class="form-container">
      <div class="tabs">
        <a href="sign_in.html"> <button onclick="showTab('signin')">Sign In</button></a>
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
                    <input type="text" placeholder="Enter Your Address" id="address" name="address" autocomplete="on"
                        spellcheck="false" required>
                    <label for="phone">Phone</label>
                    <input type="text" placeholder="Enter Your Phone Number" id="phone" name="phone" autocomplete="on"
                        spellcheck="false" required>
                    <label for="password">Password</label>
                    <input type="password" placeholder="Enter Your Password" id="password" name="password"
                        autocomplete="on" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                        title="Must contain at least one number and one uppercase and lowercase letter, and at least 8 or more characters"
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
      var password = document.getElementById("password").value; // Get password from the input field

      if (name === "" || password === "") {
        alert("Please fill in all fields.");
        return false;
      } else if (password.length < 8 || password.length > 15 || !/[A-Z]/.test(password) || !/[!@#$%^&*()_+{}\[\]:;<>,.?~\\/-]/.test(password)) {
        alert("Password must be 8-15 characters long and include at least one capital letter and one symbol.");
      } else {
        // Add your signup logic here
        // Inserted data successfully, now redirect to sign_in.html
        alert("Account created!");
        return true;
        window.location.href = "example.php";
        return false;

      }
    }

  </script>

</body>

</html>