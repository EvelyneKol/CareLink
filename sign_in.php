<?php
//σύνδεση με βάση
include 'Connection.php';

//έλεγχος σύνδεσης
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

//ξεκινάει session που κρατά τα credits του χρήστη
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") //όταν η φόρμα υποβληθεί 
{

    //Ανάγνωση στοιχείων: username & password
    $username = $_POST['username'];
    $password = $_POST['password'];

    //αναγνώριση και προετοιμασία για αναγνώριση ρόλου: admin/volunteer/civilian
    $admin_query = $conn->prepare("SELECT * FROM admin WHERE adm_username=? AND adm_password=?");
    $admin_query->bind_param("ss", $username, $password);
    $admin_query->execute();
    $admin_result = $admin_query->get_result();

    // prepare και execute το select query για έλεγχο ρόλου volunteer 
    $volunteer_query = $conn->prepare("SELECT * FROM volunteer WHERE vol_username=? AND vol_password=?");
    $volunteer_query->bind_param("ss", $username, $password);
    $volunteer_query->execute();
    $volunteer_result = $volunteer_query->get_result();

    // prepare και execute το select query για έλεγχο ρόλου civilian 
    $civilian_query = $conn->prepare("SELECT * FROM civilian WHERE civilian_username=? AND civilian_password=?");
    $civilian_query->bind_param("ss", $username, $password);
    $civilian_query->execute();
    $civilian_result = $civilian_query->get_result();

    if ($admin_result->num_rows > 0) {
        // θέτει τα cookies username and password
        setcookie("username", $username, time() + (86400 * 30), "/"); // 86400 seconds = 1 day
        //setcookie("password", $password, time() + (86400 * 30), "/");

        /*Θέτει τις μεταβλητές συνεδρίας (session) για την είσοδο του χρήστη, το όνομα χρήστη και τον ρόλο του ως admin.*/   
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['role'] = 'admin';
        header("Location: admin.php");
        exit();
    } elseif ($volunteer_result->num_rows > 0) {
         // θέτει τα cookies username and password
        setcookie("username", $username, time() + (86400 * 30), "/"); // 86400 seconds = 1 day
        //setcookie("password", $password, time() + (86400 * 30), "/");

        /*θέτει τις μεταβλητές συνεδρίας του χρήστη σε ρόλο εθελοντή*/
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['role'] = 'volunteer';
        header("Location: volunteer.php");
        exit();
    } elseif ($civilian_result->num_rows > 0) {
        // θέτει τα cookies username and password
        setcookie("username", $username, time() + (86400 * 30), "/"); // 86400 seconds = 1 day
        //setcookie("password", $password, time() + (86400 * 30), "/");
        
        /*θέτει τις μεταβλητές συνεδρίας του χρήστη σε ρόλο πολίτη*/
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['role'] = 'civilian';
        header("Location: Civilian.php");  /* Ανακατεύθυνση του χρήστη στη σελίδα Civilian.php */
        exit();
    } else {
        $error_message = "Invalid username or password!";
    }
}

//τερμστισμός σύνδεσης
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/jpg" sizes="96x96" href="favicon-96x96.png"> <!-- favicon -->
    <link rel="stylesheet" href="form.css"> <!-- css -->
    <title>Sign in Form</title> <!-- τίτλος -->
</head>

<body>
    <!-- container φόρμας -->
    <div class="container">
        <div class="form-container">
            <div class="tabs">
                <!-- καρτέλα sign_in ενεργή/ sign_up κουμπί για μετάβαση στην καρτέλα για εγγραφή -->
                <button onclick="showTab('signin')" class="active">Sign In</button>
                <a href="sign_up.php"><button onclick="showTab('signup')">Sign Up</button></a>
            </div>

            <!-- καρτέλα sign_in -->
            <div id="signin-tab" class="tab">
                <p>Welcome back to CareLink!</p>
                <!-- φόρμα εισόδου -->
                <form id="signin-form" action="" method="post">
                    <label for="username">Username</label>
                    <input type="text" placeholder="Enter Your Username" id="username" name="username" required>
                    <label for="password">Password</label>
                    <input type="password" placeholder="Enter Your Password" id="password" name="password" required>
                    <label for="showpass" id="rlabel">Show Password</label>
                    <input type="checkbox" id="showpass" name="showpass" onclick="passwordvisibility()">
                    <?php
                    if (isset($error_message)) {
                        echo "<p class='error-message'>$error_message</p>";
                    }
                    ?>
                    <div class="button-center">
                    <!-- με το click του κουμπιού γίνεται Triggered η συνάρτηση  login-->    
                    <button class="button1" onclick="checkEmptyFields()"><strong>Log In</strong></button>
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
        var currentTab = 0; // η τρέχουσα καρτέλα θέτεται ως 1η(0)
        showTab(currentTab); // προβολή τρέχουσας καρτέλας

        function showTab(n) {
            //προβάλει την καρτέλα που παίρνει ως είσοδο
            var x = document.getElementsByClassName("tab");
            x[n].style.display = "block";
        }


        function passwordvisibility() { //προβάλει τον κωδικό 
            var x = document.getElementById("password");
            if (x.type === "password") {
                x.type = "text";
            } else {
                x.type = "password";
            }
        }

        function checkEmptyFields() { //εάν υπάρχει κενό πεδίο βγάζει μήνυμα 
            var username = document.getElementById("username").value;
            var password = document.getElementById("password").value;

            if (username === "" || password === "") {
                alert("Please fill in all fields.");
            } 
        }
    </script>

</body>
</html>