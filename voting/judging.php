<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// if the user isnt logged in, go back to login
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: https://www.expoexpress.online/register/login.php");
    exit;
}

$userid = $_SESSION['id'];
echo $_SESSION['username'];
include "../db/config.php";
include "../nregister/functions.php";
$ratingArr = array();


$query = "SELECT event FROM community WHERE id='" . $userid . "'";
$result = mysqli_query($link, $query);
$row = $result->fetch_assoc();
$eventName = $row["event"];

$query = "SELECT organization FROM organizers WHERE event='" . $eventName . "'";
$result = mysqli_query($link, $query);
$row = $result->fetch_assoc();
$orgName =  $row['organization'];

if (empty($eventName))
{
    echo "No Event Found for User";
}
else
{
    echo $eventName;
}

// get judging questions and store into categories
$query = "SELECT judging FROM voting_questions WHERE organization='" . $orgName . "'";

$result = mysqli_query($link, $query);
$row = $result->fetch_assoc();
$questions =  $row['judging'];
$categories = (explode(",",$questions));
$num_cat = count($categories);

//update so judge picks who he wants to judge
$exhibitor_id = $_SESSION['exhibit_id'];

echo $exhibitor_id;
// check if judge
$query = "SELECT role FROM community WHERE id='" . $userid . "'";
$result = mysqli_query($link, $query);
$row = $result->fetch_assoc();
$role =  $row['role'];

if ($role == "judge") {
    // the id of the exhibitor who exhibit is being judged
    $query = "SELECT exhibitname FROM exhibitors WHERE id = '" . $exhibitor_id . "'";
    $result = mysqli_query($link, $query);
    $num_result = mysqli_num_rows($result);
    $row = $result->fetch_assoc();
    $exhibitName =  $row['exhibitname'];
    global $exhibitName;// =  $_SESSION['exhibit_name'];

    if (isset($_POST["submit"])) {
        // Check if user already filled out a judging form for this exhibit
        $query = "SELECT id FROM judging WHERE id = '" . $userid . "' AND exhibitname = '" . $exhibitName . "' AND event = '" . $eventName . "'";
        $result = mysqli_query($link, $query);
        $num_result = mysqli_num_rows($result);
        if ($num_result == 0) {

            for ($i = 0; $i < $num_cat; $i++) {
                if (!isset($_POST[$i])) {
                    echo "Please Fill out All Fields";
                } else {
                    $ratingArr[$i] = $_POST[$i];
                }
            }

            $query = "INSERT INTO judging(id, exhibitname, rating, event) VALUES (?, ?, ?, ?);";

            if ($stmt = mysqli_prepare($link, $query)) {
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "isss", $paramId, $paramName, $paramRate, $paramEvent);

                // Set parameters
                $paramId = $userid;
                $paramName = $exhibitName;
                $paramRate = implode(', ', $ratingArr);
                $paramEvent = $eventName;

                if (mysqli_stmt_execute($stmt)) {
                   echo "Updated Successfully.";
                } else {
                    echo "Error Occurred;";
                }
            }
        } else {
            echo "You already filled out this Judging Form!";
        }
    }
}
else {
    echo "You're Not a Judge";
}

?>



<!doctype html>
<html lang="en">
<head>
    <title>Vote for your Favorite Project</title>
    <style type="text/css">
        <?php echo file_get_contents('style.css'); ?>
    </style>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</head>
<body>
<div id="container">
    <p>
    <div  class="wrapper">
        <form class="judge_form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <?php

            $query = "SELECT exhibitname FROM exhibitors WHERE id = '" . $exhibitor_id . "'";
            $result = mysqli_query($link, $query);
            $num_result = mysqli_num_rows($result);
            $row = $result->fetch_assoc();
            $exhibitName =  $row['exhibitname'];

            if ($num_result == 0){
                echo "No Exhibit of that ID Exists";
            }
            else{
                //Name of Exhibit that is being Judged
                echo '<h1 class="exhibitName" style="margin-top: 10px;">'."Exhibit: ".$exhibitName.'</h1>';
            }
            $i = 0;
            foreach ($categories as $value) {
                echo '<fieldset id='.$i.'>';
                echo '<p>'.$value.'</p>';

                echo '<label for="1">1';
                echo '<input type="radio" name='.$i.' value="1">';
                echo '</label>';

                echo '<label for="2">2';
                echo '<input type="radio" name='.$i.' value="2">';
                echo '</label>';

                echo '<label for="3">3';
                echo '<input type="radio" name='.$i.' value="3">';
                echo '</label>';

                echo '<label for="4">4';
                echo '<input type="radio" name='.$i.' value="4">';
                echo '</label>';

                echo '<label for="5">5';
                echo '<input type="radio" name='.$i.' value="5">';
                echo '</label>';

                echo '</fieldset>';

                $i++;
            }
            ?>
            <div class="form-group">
                <input type="submit" name="submit" class="btn btn-primary" value="Submit">
            </div>
        </form>
    </div>
    </p>
</div>

</body>
</html>


