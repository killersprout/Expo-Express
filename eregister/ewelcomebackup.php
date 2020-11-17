<?php
// Initialize the session
session_start();

require_once "../db/config.php"; //DB connection


// if the user isnt logged in, go back to login
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: elogin.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" />
    <style type="text/css">
        body{ font: 18px sans-serif; text-align: center; background:grey; }
        li{font: 18px sans-serif; text-align:left;}

    </style>
</head>
<body>

    <div class="w3-container w3-black">
        <h1><b><?php echo htmlspecialchars($_SESSION["event"]); ?></b>.</h1>
        <p>We are in the process of testing out comments for you!</p>
    </div>

<div class="w3-container w3-white">
    <h2><b><?php echo htmlspecialchars($_SESSION["exhibitname"]); ?></b>.</h2>
    <ol class="list-unstyled">
        <li><b><?php echo htmlspecialchars($_SESSION["firstname"]); ?></b></li>
        <li><b><?php echo htmlspecialchars($_SESSION["lastname"]); ?></b></li>
        <li><b><?php echo htmlspecialchars($_SESSION["id"]. ": This is your unique ID"); ?></b></li>
        <li><b><?php echo htmlspecialchars($_SESSION["exhibitname"].": Exhibit Name"); ?></b></li>
        <li><b><?php echo htmlspecialchars($_SESSION["organization"].": Organization I am with"); ?></b></li>
        <li><b><?php echo htmlspecialchars($_SESSION["event"].": Event Name"); ?></b></li>
    </ol>
</div>



    <footer class="w3-container w3-black">
        <a href="epasswordReset.php" class="btn btn-warning">Reset Your Password</a>
        <a href="elogout.php" class="btn btn-danger">Sign Out of Your Account</a>
        <a href="https://www.expoexpress.online/" class="btn btn-default">Return to Home Page</a>
        <a href="https://www.expoexpress.online/videotest/videotopeer.php" class="btn btn-default">Video Test</a>
        <a href="https://www.expoexpress.online/comments/postdetails.php" class="btn btn-default">Comment Tests</a>
    </footer>


</body>
</html>