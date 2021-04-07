<?php
// Initialize the session
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include "functions.php";
require_once "../db/config.php"; //DB connection

// if the user isn't logged in, go back to login
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: index.php");
    exit;
}
$event = $_SESSION['organization']; //I hate that this is backwards ><
$organization = $_SESSION['event'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Organizer Portal</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        <?php echo file_get_contents('../css/style.css'); ?>
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
        //Made it so you can show and clear who's in ur exhibit. Placement needs fixed
        if(isset($_POST['show'])){
            displayExhibitors();  //show all the exhibitors in the organization
        }
        if(isset($_POST['submit'])) {
            pickJudge(); //allows the organizer to pick who they want to be a judge
        }
        if(isset($_POST['deselect'])) {
            deselectJudge(); //allows the organizer to unselect judge
        }
        //Lists the attendees and VIPs that are signed up for specific event.
        //Displaies the button to select and de-select judges
        $sql = "SELECT * FROM users WHERE event = '$event'";
        $result = mysqli_query($link, $sql);

        echo '<h3>Select Judges</h3>';
        echo '<form class="judge_select" action="welcome.php" method="post">';
        while ($row = mysqli_fetch_assoc($result)) {
            $name = $row["firstname"] . " " . $row["lastname"] . " - " . $row["username"];
            echo $name . " " . '<input type="checkbox" name="judge[]" value="' . $name . '"> <br>';

            $query = "SELECT * FROM exhibitors WHERE event = '" . $event . "' AND organization = '".$organization."'";
            $result_div = mysqli_query($link,$query);
            $div_arr = array();
            echo '<select class="select" name='.$row["username"].' style="margin-top: 10px;">';
            echo '<option>'."Select...".'</option>';
            while ($row = mysqli_fetch_assoc($result_div)) {
                $name = $row['divisionname'];
                if (!in_array($name, $div_arr))
                {
                    echo '<option>'.$name.'</option>';
                    array_push($div_arr, $name);
                }
            }
            echo '</select>';
            echo '<br>';
        }

        echo '<div class="form-group">';
        echo '<input type="submit" name="submit" class="btn btn-primary" value="Submit">';
        echo '<input type="submit" name="deselect" class="btn btn-secondary" value="De-select">';
        echo '</div>';
        echo '</form>';
        echo '<br>';
    ?>
    <form method="post">
        <button type="submit" name="show" value="show">Show Exhibitors</button>
        <button type="submit" name="hide" value="reset">Hide Exhibitors</button>
        <br>
    </form>
</div>

<br>

<footer class="w3-container w3-black">
    <a href="passwordReset.php" class="btn btn-warning">Reset Your Password</a>
    <a href="logout.php" class="btn btn-danger">Sign Out of Your Account</a>
    <a href="https://www.expoexpress.online/" class="btn btn-default">Return to Home Page</a>
    <a href="https://www.expoexpress.online/voting/add_voting_questions.php" target="_blank" class="btn btn-default">Add Questions for Voting</a>
    <a href="https://www.expoexpress.online/exhibit-page/Template_selection.php" class="btn btn-default">Select Exhibit Template</a>
</footer>

</body>
</html>