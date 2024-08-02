<?php
include 'Connection.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = $_POST['firstname'];
    $Lastname = $_POST['Lastname']; 
    $Email = $_POST['Email'];
    $Phone = (int)$_POST['Phone'];
    $Comments = $_POST['Comments'];

    $stmt = $conn->prepare("INSERT INTO contact_info (info_fname , info_lname , info_mail , info_phone, comments) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssis", $firstname, $Lastname, $Email, $Phone, $Comments); // Fixed order of parameters
    if ($stmt->execute()) {
      // Redirect to a different page after successful form submission
      header('Location: Contact_info.php');
      exit(); // Make sure to exit to prevent further execution of the script
    } else {
      echo "Error: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection outside the if block
$conn->close();
?>



<!DOCTYPE html>
<html lang="el">

<head>
    <meta charset="utf-8">
    <title>CareLink</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="Contact_info.css">
    <link rel="icon" type="image/jpg" sizes="96x96" href="images/favicon.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
</head>

<body>

    <div class="navbar">
        <img src="images/logo1.png" alt="Logo" >
        <ul class="nav">
        <li><a href="Home.html">Home</a></li>
        <p style="font-size: 18px;"> | </p >
        <li><a class="active" href="Contact_info.php">Contact Info</a></li>
        </ul>
        <ul class="nav">
        <li style="float:right;"><a href="sign_up.php"><i class="fa fa-pencil-square-o" style="font-size:24px"></i>Sign
            Up</a></li>
        <li style="float:right;"><a href="sign_in.php"><i class="fa fa-sign-in" style="font-size:24px"></i>Sign In</a>
        </li>
        </ul>
    </div>
    <hr>


    <div class="contacts">
        <h2>Main Contacts</h2>

        <div class="container">
            <div class="row row-centered">
                <div class="col-sm-6">
                    <div class="container">
                        <h3>Evangelia Kolagki</h3>
                        <img class="photo1" src="images/Evangelia.jpg" alt="Evangelia">
                        <p class="title">Co-Founder</p>
                        <p>Responsible for the supplies.</p>
                        <p>ekolagki@gmail.com</p>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="container">
                        <h3>George Karagiannis</h3>
                        <img class="photo2" src="images/George.png" alt="George">
                        <p class="title">Co-Founder</p>
                        <p>Responsible for the transformations of the products.</p>
                        <p>karagiannis302@gmail.com</p>
                    </div>
                </div>
            </div>
        </div>


        <div class="Secondsection">
            <div class="row">
                <div class="col1 col-sm-7">
                    <h3>Contact Us</h3>
                    <form  action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" onsubmit="resetForm()">
                        <div class="row">
                            <div class="col-sm-6">
                                <label for="firstname" class="form-label">First Name:</label>
                                <input type="text" id="firstname" class="form-control p-2 w-60" name="firstname" maxlength="30"
                                    placeholder="Enter your First Name.." autocomplete="on" pattern="[a-zA-Z]{0-25}"
                                    required>
                            </div>
                            <div class="col-sm-6">
                                <label for="Lastname" class="form-label">Last Name:</label>
                                <input type="text" id="Lastname" class="form-control p-2" name="Lastname" maxlength="30"
                                    placeholder="Enter your Last Name.." pattern="[a-zA-Z]{0-25}" required>
                            </div>
                        </div>
                        <br>
                        <label for="Email" class="form-label">Email:</label>
                        <input type="email" id="Email" class="form-control p-2" name="Email"
                            placeholder="Enter your email.." autocomplete="off" required>

                        <br>
                        <label for="Phone" class="form-label">Phone:</label>
                        <input type="tel" id="Phone" class="form-control p-2" name="Phone"
                            placeholder="Enter your phone.." pattern="[0-9]{10}" required>
                        <br>
                        <label for="Comments" class="form-label">Comments:</label>
                        <textarea id="Comments" class="form-control p-2" name="Comments" rows="3" cols="20"
                            placeholder="Enter your comments..." required></textarea>
                        <button class="submit" type="submit">Submit</button>
                        <button class="reset" type="reset" onclick="resetForm()">Reset</button>
                    </form>
                </div>
                <div class="col2 col-sm-5">
                    <h3>Find us Here!</h3>
                    <div id="map"></div>
                </div>
            </div>
        </div>
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
                    <li>Email: <span class="email">carelink@gmail.com</span></li>
                    <br>
                    <li>Phone: +30 123456789</li>
                </ul>
            </div>
        </div>
    </div>

</body>

</html>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>

    // Initialize the map
    var map = L.map('map').setView([38.247368, 21.736798], 16); // Set your preferred coordinates and zoom level

    // Add the tile layer (replace with your desired map provider)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
    }).addTo(map);

    // Add a marker for your headquarters
    var headquartersMarker = L.marker([38.247368, 21.736798]).addTo(map); // Set your headquarters coordinates

    // Add a popup to the marker
    headquartersMarker.bindPopup("<b>Headquarters: </b><br>Agiou Nikolaou 38").openPopup(); // Set your headquarters address 


</script>