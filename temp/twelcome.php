<?php
// Initialize the session
session_start();

require_once "../db/config.php"; //DB connection

// if the user isnt logged in, go back to login
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: https://www.expoexpress.online/register/login.php");
    exit;
}

//WHY IS THIS BACKWARDS!!!!!!!!!!
$event = $_SESSION["organization"]; //Store the event name inside event variable to query from
$organization = $_SESSION["event"];

if (isset($_POST['submit']))
{
    $string = $_POST['judge'];
    $username_arr = array();

    foreach ($string as $judge) {
        $pieces = explode(" - ", $judge);
        $username = $pieces[1];
        array_push($username_arr, $username);
    }

    $query = "SELECT username FROM community";
    $result = mysqli_query($link,$query);

    while ($row = mysqli_fetch_assoc($result))
    {
        $cur_username = $row['username'];
        $role = "attendee";
        if (in_array($cur_username, $username_arr))
        {
            $role = "judge";

            if ($_POST[$cur_username] == "Select...") {
                echo 'Please select an option.';
            } else {
                $div_name = $_POST[$cur_username];
                $query = "UPDATE community SET division = ? WHERE username = ?";

                if($stmt = mysqli_prepare($link, $query))
                {
                    // Bind variables to the prepared statement as parameters
                    mysqli_stmt_bind_param($stmt, "ss", $paramDiv, $paramUsername);

                    // Set parameters
                    $paramDiv = $div_name;
                    $paramUsername = $cur_username;

                    // Attempt to execute the prepared statement
                    if(mysqli_stmt_execute($stmt)) {
                        $message = "Updated Successfully.";
                    } else {
                        $message = "Error Occurred;";
                    }

                    // Close statement
                    mysqli_stmt_close($stmt);
                }
            }
        }

        $query = "UPDATE community SET role = ? WHERE username = ?";

        if($stmt = mysqli_prepare($link, $query))
        {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ss", $paramRole, $paramUsername);

            // Set parameters
            $paramRole = $role;
            $paramUsername = $cur_username;

            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)) {
                $message = "Updated Successfully.";
            } else {
                $message = "Error Occurred;";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }

    }

}

//need to make this dynamic to specific person

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Temp Organizer Portal</title>

    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        <?php echo file_get_contents('style.css'); ?>
        body{ font: 14px sans-serif; text-align: center; }
        li{font: 18px sans-serif; text-align:left;}
    </style>
</head>
<body>
<div class="page-header">
    <div class="w3-container w3-black">
        <h1><b><?php echo "Hello ". htmlspecialchars($_SESSION["firstname"]) . " " .htmlspecialchars($_SESSION["lastname"]); ?></b>.</h1>
        <p>Welcome to your organizer page! We are still setting things up. Please standby while we update the site.</p>
    </div>
</div>


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

<?php

    $sql = "SELECT * FROM community";
    $result = mysqli_query($link,$sql);

    echo '<h3>Select Judges</h3>';
    echo '<form class="judge_form" action="twelcome.php" method="post">';
        while ($row = mysqli_fetch_assoc($result))
        {
            $name = $row["firstname"]." ".$row["lastname"]." - ".$row["username"];
            echo $name." ".'<input type="checkbox" name="judge[]" value="'.$name.'"> <br>';

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
        }

        echo '<div class="form-group">';
            echo '<input type="submit" name="submit" class="btn btn-primary" value="Submit">';
        echo '</div>';
    echo '</form>';

?>

<footer class="w3-container w3-black">
    <a href="passwordReset.php" class="btn btn-warning">Reset Your Password</a>
    <a href="logout.php" class="btn btn-danger">Sign Out of Your Account</a>
    <a href="https://www.expoexpress.online/" class="btn btn-default">Return to Home Page</a>
</footer>

</body>
</html>