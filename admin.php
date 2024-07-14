<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['role'] !== 'admin') {
    header('Location: sign_in.php');
    exit();
}
?>

<!DOCTYPE html>

<html lang="el">

<head>
    <meta charset="utf-8">
    <title>CareLink</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="icon" type="image/jpg" sizes="96x96" href="images/favicon.png">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Work+Sans:wght@200&display=swap');
    </style>
</head>

<body>
    <div class="header">
        <h2><img src="images/logo.png" alt="Logo" width="200"></h2>
        <div class="h3-header">
            <h3>Back to admin tasks!</h3> 
          </div>
    </div>

    <div class="navbar">
        <ul class="nav">
            <li><a class="active" href="Home.html">Home</a></li>
            <li><a href="Contact-info-main-menu.html">Map</a></li>
            <li><a href="Contact-info-main-menu.html">Database </a></li>
        </ul>
        <ul class="nav">            
            <li><a href="logout.php"><i class="fa fa-sign-out" style="font-size:24px" ></i> Log out</a></li>
        </ul>
    </div>

    <div class="Main container-fluid">
        <div class="Firstsection">
          <h2>  Volunteer members</h2>
          <div class="container mt-5">
            <div class="row">
              <div class="col-sm-4">
                <h3><a class="a2" href="#A">My Vehicle</a><i class="fa fas fa-truck" style="font-size:23px" > </i></h3> 
                <p>Volunteers can <strong>tracking</strong> their <strong>vehicle</strong> and see what they tranfer at any time.</p>
              </div>
              <div class="col-sm-4">
                <h3><a class="a2" href="#B">Βase management </a><i class="fa fa-map" style="font-size:24px"></i></h3>
                <p>A map with all the tasks available, 
                  the <strong>tasks</strong> you have taken with their <strong>route</strong> and the 
                  location of the <strong>store</strong> in available for you.</p>
              </div>
              <div class="col-sm-4">
                <h3><a class="a2" href="#C">My tasks</a> <i class="fa fa-tasks" style="font-size:24px"></i></h3>        
                <p>In this section you can see all the information about your task.
                  You can also update the task-status to <strong>"Done" </strong> or <strong>"Canceled"</strong> </p>     
              </div>
              <div class="col-sm-4">
                <h3><a class="a2" href="#C">My tasks</a> <i class="fa fa-tasks" style="font-size:24px"></i></h3>        
                <p>In this section you can see all the information about your task.
                  You can also update the task-status to <strong>"Done" </strong> or <strong>"Canceled"</strong> </p>     
              </div>
              <div class="col-sm-4">
                <h3><a class="a2" href="#C">My tasks</a> <i class="fa fa-tasks" style="font-size:24px"></i></h3>        
                <p>In this section you can see all the information about your task.
                  You can also update the task-status to <strong>"Done" </strong> or <strong>"Canceled"</strong> </p>     
              </div>
              <div class="col-sm-4">
                <h3><a class="a2" href="#C">My tasks</a> <i class="fa fa-tasks" style="font-size:24px"></i></h3>        
                <p>In this section you can see all the information about your task.
                  You can also update the task-status to <strong>"Done" </strong> or <strong>"Canceled"</strong> </p>     
              </div>
            </div>
          </div>
        </div>
        
    </div>

    <div class="Forthsection">
        <div class="row">
            <div class="col4 col-sm-6">
                <h2>If you want to join please Sign-Up</h2>
                <button class="singup"><a href="sign_up_civilian.html">Sign-Up</a></button>
            </div>
            <div class="col5 col-sm-6">
                <h2>If you are a member please Sign-In</h2>
                <button class="singin"><a href="sign_in.html">Sign-In</a></button>
            </div>
        </div>
    </div>

    <div class="Fifthsection">
        <div class="row">
            <h2><strong> Statistics </strong></h2>
            <div class="col1 col-sm-4">
                <h2>100 people have been helped</h2>
                <img class="photo" src="images/social-care.png" alt="People icen">
            </div>

            <div class="col2 col-sm-4">
                <h2>100 items have been delivered</h2>
                <img class="photo" src="images/items.png"alt="Items icon">
            </div>

            <div class="col3 col-sm-4">
                <h2>In 45 different regions</h2>
                <img class="photo" src="images/map-point.png"alt="Regions icon">
            </div>

        </div>
    </div>

    <div class="Footer container-fluid">
        <div class="row">
          <div class="column col-sm-3">
            <h2>Social Media</h2>
            <nav class="nav02">
              <a href="https://www.linkedin.com/in/george-karagiannis-00a683222/" class="fa fa-linkedin"></a>
              <a href="https://www.facebook.com/george.karagiannis.9406" class="fa fa-facebook"></a>
              <a href="https://www.instagram.com/_karagiannis_g/" class="fa fa-instagram "></a>
            </nav>
          </div>
          <div class="column col-sm-6">
            <h2>Licences</h2>
            <br>
            <p>© George Karagiannis/Ceid/Upatras/Year 2023-2024</p>
            <p>© Evelina Kolagki/Ceid/Upatras/Year 2023-2024</p>
    
          </div>
          <div class="column col-sm-3">
            <h2>Contact info</h2>
            <br>
            <ul>
              <li>Email: Karagiannis.giorg@gmail.com</li>
              <br>
              <li>Phone: +30 123456789</li>
            </ul>
    
          </div>
        </div>
    
    </div>


</body>

</html>