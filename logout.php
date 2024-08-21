<?php
/* καταστρέφει το session όταν πατηθεί το logout έτσι ώστε να μην μπορεί ο χρήστης να επιστρέψει στην σελίδα χωρίς να ξανα συνδεθεί */
session_start();
session_destroy();
header('Location: home.html');
exit();
?>
