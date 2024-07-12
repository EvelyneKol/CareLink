<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/jpg" sizes="96x96" href="favicon-96x96.png">
    <link rel="stylesheet" href="css/form.css">
    <title>Sign in Form</title>
</head>
<body>

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
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Using prepared statements to prevent SQL injection
    $admin_query = $conn->prepare("SELECT * FROM admin WHERE name=? AND password=?");
    $admin_query->bind_param("ss", $username, $password);
    $admin_query->execute();
    $admin_result = $admin_query->get_result();

    $volunteer_query = $conn->prepare("SELECT * FROM volunteer WHERE name=? AND password=?");
    $volunteer_query->bind_param("ss", $username, $password);
    $volunteer_query->execute();
    $volunteer_result = $volunteer_query->get_result();

    if ($admin_result->num_rows > 0) {
        // Set cookies for the username and password
        setcookie("username", $username, time() + (86400 * 30), "/"); // 86400 seconds = 1 day
        setcookie("password", $password, time() + (86400 * 30), "/");
    
        header("Location: Civilian.html?username=$username");
        exit();
    } elseif ($volunteer_result->num_rows > 0) {
        // Set cookies for the username and password
        setcookie("username", $username, time() + (86400 * 30), "/"); // 86400 seconds = 1 day
        setcookie("password", $password, time() + (86400 * 30), "/");
    
        header("Location: Home.html?username=$username");
        exit();
    } else {
        $error_message = "Invalid username or password";
    }
}

$conn->close();
?>

    <?php
    if (isset($error_message)) {
        echo "<p>$error_message</p>";
    }
    ?>

    <div class="container">

        <div class="form-container">
            <div class="tabs">
                <button onclick="showTab('signin')" class="active">Sign In</button>
                <a href="sign_up_civilian.html"><button onclick="showTab('signup')">Sign Up</button></a>
            </div>
            <div id="signin-tab" class="tab">
                <p>Welcome back to CareLink!</p>
                <form id="signin-form" action="" method="post">
                    <label for="username">Username</label>
                    <input type="text" placeholder="Enter Your Username" id="username" name="username" required>
                    <label for="password">Password</label>
                    <input type="password" placeholder="Enter Your Password" id="password" name="password" required>
                    <label for="showpass" id="rlabel">Show Password</label>
                    <input type="checkbox" id="showpass" name="showpass" onclick="passwordvisibility()">
                    <label for="keepme" id="keepme">Keep me Signed in</label>
                    <input type="checkbox" id="remember" name="remember" onclick="">
                    <div class="button-center">
                    <button class="button1" onclick="login()"><strong>Log In</strong></button>
                    </div>
                </form>
                <footer>
                    <div class="hr"></div>
                    <div class="fp"><a href="forget_pass.html">Forgot Password?</a></div>
                </footer>
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

        function login() {
    var username = document.getElementById("username").value;
    var password = document.getElementById("password").value;

    if (username === "" || password === "") {
        alert("Please fill in all fields.");
    } else if (password.length < 8 || password.length > 15 || !/[A-Z]/.test(password) || !/[!@#$%^&*()_+{}\[\]:;<>,.?~\\/-]/.test(password)) {
        alert("Password must be 8-15 characters long and include at least one capital letter and one symbol.");
    } else {
        // Add your login logic here
        alert("Logging in...");
    }
}
    </script>

</body>
</html>
