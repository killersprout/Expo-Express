<?php
// Initialize the session
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once "../db/config.php"; //DB connection

// if the user isn't logged in, go back to login
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
$event = $_SESSION['organization']; //I hate that this is backwards ><
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Organizer Portal</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; text-align: center; }
        li{font: 18px sans-serif; text-align:left;}
    </style>
</head>
<body>
<!-- Header-->
<div>
    <div class="w3-container w3-black">
        <h1><b><?php echo "Hello ". htmlspecialchars($_SESSION["firstname"]) . " " .htmlspecialchars($_SESSION["lastname"]); ?></b>.</h1>
        <p>Welcome to your organizer page! We are still setting things up. Please standby while we update the site.</p>
    </div>
</div>

<!--List-->
<div class="w3-container w3-white">
    <h2><b><?php echo "The organization you are with: " .htmlspecialchars($_SESSION["event"]); //This and organization are backwards ?></b>.</h2>
    <ol class="list-unstyled">
        <li><b><?php echo  "This is your unique ID: " . htmlspecialchars($_SESSION["id"]); ?></b></li>
        <li><b><?php echo "Event that you are hosting: " . htmlspecialchars($_SESSION["organization"]); ?></b></li>
        <li><b><?php echo "Username: " . htmlspecialchars($_SESSION["username"]); ?></b></li>
        <li><b><?php echo "Number of exhibits: " . htmlspecialchars($_SESSION["headcount"]); ?></b></li>
        <li><b><?php echo "Account created at: " . htmlspecialchars($_SESSION["created_at"]); ?></b></li>

    </ol>
</div>

<div class="w3-table">
    <?php
    $sql = "SELECT * FROM exhibitors WHERE event = '$event'"; //Gets all exhibits for an organization.

    $result = mysqli_query($link, $sql); //store it all in results
    if(mysqli_num_rows($result) > 0){ //if the row exists
        while($row = mysqli_fetch_array($result)){
            // Makes it linkable
            echo "<a href='details.php?exhibitname={$row['exhibitname']}'> {$row['exhibitname']}</a><br>\n ";
        }
        }else{
        echo "Something happened";
    }





    ?>
    <form method="post">
        <button type="submit" name="show" value="show">Show Exhibitors</button>
        <button type="submit" name="hide" value="reset">Hide Exhibitors</button>
    </form>
</div>


<footer class="w3-container w3-black">
    <a href="passwordReset.php" class="btn btn-warning">Reset Your Password</a>
    <a href="logout.php" class="btn btn-danger">Sign Out of Your Account</a>
    <a href="https://www.expoexpress.online/" class="btn btn-default">Return to Home Page</a>
    <a href="https://www.expoexpress.online/voting/add_voting_questions.php" target="_blank" class="btn btn-default">Add Questions for Voting</a>
</footer>

</body>
</html>