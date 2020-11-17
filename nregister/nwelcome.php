<?php
// Initialize the session
session_start();
include "functions.php";
require_once "../db/config.php"; //DB connection

// if the user isn't logged in, go back to login
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: nlogin.php");
    exit;
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Portal</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; text-align: center; }
        li{font: 18px sans-serif; text-align:left;}
    </style>
</head>
<body>
<div class="page-header">
    <div class="w3-container w3-black">
        <h1><b><?php echo "Hello ". htmlspecialchars($_SESSION["firstname"]) . " " .htmlspecialchars($_SESSION["lastname"]); ?></b>.</h1>
        <p>Welcome to your personal page! Here you can find different exhibits based on the event you want to see, and vote on exhibits!</p>

    </div>
</div>

<div class="w3-container w3-white">
    <h2><b><?php echo "The event you are viewing: " .htmlspecialchars($_SESSION["event"]); //This and organization are backwards ?></b>.</h2>
    <ol class="list-unstyled">
        <li><b><?php echo  "This is your unique ID: " . htmlspecialchars($_SESSION["id"]); ?></b></li>
        <li><b><?php echo "Username: " . htmlspecialchars($_SESSION["username"]); ?></b></li>
    </ol>
</div>

<div class="w3-table">
    <?php
    //Made it so you can show and clear who's in ur exhibit. Placement needs fixed
    if(isset($_POST['show'])){
        displayExhibitors(); // Need to make it so you can visit the pages

    }
    pickDivisionToJudge();//Need to make based on divisions. Divisions are only for judges
    ?>
    <form method="post">
        <button type="submit" name="show" value="show">Show Exhibitors</button>
        <button type="submit" name="hide" value="reset">Hide Exhibitors</button>
    </form>
</div>

    <footer class="w3-container w3-black">
        <a href="npasswordReset.php" class="btn btn-warning">Reset Your Password</a>
        <a href="../register/logout.php" class="btn btn-danger">Sign Out of Your Account</a>
        <a href="https://www.expoexpress.online/" class="btn btn-default">Return to Home Page</a>
        <!--Modify so only judges have this option -->
        <a href="https://www.expoexpress.online/voting/judging.php" target="_blank" class="btn btn-default">Judge Page</a>
    </footer>

</body>
</html>