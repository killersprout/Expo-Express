<?php
// Initialize the session
session_start();
include('../comments/functions.php');
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
    <title><?php echo htmlspecialchars($_SESSION["exhibitname"]); ?></title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../comments/main.css">
    <style type="text/css">
        li{font: 18px sans-serif; text-align:left;}
        body{ font: 18px sans-serif; text-align: center; background:white; }
    </style>
</head>
<body>

<div class="w3-container w3-black">
    <h1><b><?php echo htmlspecialchars($_SESSION["event"]); ?></b>.</h1>
    <p>We are in the process of updating our site!</p>
</div>

<div class="w3-container w3-white">
    <h2><b><?php echo htmlspecialchars($_SESSION["exhibitname"]); ?></b></h2>
    <ol class="list-unstyled">
        <li><b><?php echo htmlspecialchars($_SESSION["firstname"]); ?></b></li>
        <li><b><?php echo htmlspecialchars($_SESSION["lastname"]); ?></b></li>
        <li><b><?php echo "This is your unique ID: ".htmlspecialchars($_SESSION["id"] ); ?></b></li>
        <li><b><?php echo "Exhibit Name: ".htmlspecialchars($_SESSION["exhibitname"]); ?></b></li>
        <li><b><?php echo "Sponsored Organization: ".htmlspecialchars($_SESSION["organization"]); ?></b></li>
        <li><b><?php echo "Name of event: ".htmlspecialchars($_SESSION["event"]); ?></b></li>
    </ol>
</div>

<!--Comment test-->

<div class="w3-container w3-white">
    <a href="https://www.expoexpress.online/exhibit-page/File_upload.php" class="btn btn-default">Create New Exhibit</a>
    <a href="https://www.expoexpress.online/exhibit-page/File_upload.php" class="btn btn-default">Edit Exhibit</a>
    <a href="https://www.expoexpress.online/exhibit-page/Preview_page.php" class="btn btn-default">View Exhibit</a>
</div>
<br>

<footer class="w3-container w3-black">
    <a href="epasswordReset.php" class="btn btn-warning">Reset Your Password</a>
    <a href="elogout.php" class="btn btn-danger">Sign Out of Your Account</a>
    <a href="https://www.expoexpress.online/" class="btn btn-default">Return to Home Page</a>
    <a href="https://www.expoexpress.online/videotest/videotopeer.php" class="btn btn-default">Video Test</a>
    <a href="https://www.expoexpress.online/comments/postdetails.php" class="btn btn-default">Comment Tests</a>
</footer>


</body>
</html>