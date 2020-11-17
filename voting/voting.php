<?php
session_start();

// if the user isnt logged in, go back to login
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: https://www.expoexpress.online/register/login.php");
    exit;
}

$userID = $_SESSION["id"];
echo "ID of Current User: ".$userID;

include "../db/config.php";


$votesArr = array();


$query = "SELECT event FROM community WHERE id='" . $userID . "'";
$result = mysqli_query($link, $query);
$row = $result->fetch_assoc();
$eventName = $row["event"];

if (empty($eventName))
{
    echo "No Event Found for User";
}
else
{
    echo $eventName;
}


$query = "SELECT organization FROM organizers WHERE event='" . $eventName . "'";
$result = mysqli_query($link, $query);
$row = $result->fetch_assoc();
$orgName =  $row['organization'];
echo $orgName;

// Get the array of voting catergories from voting
$query = "SELECT voting FROM voting_questions WHERE organization='" . $orgName . "'";
$result = mysqli_query($link, $query);
$row = $result->fetch_assoc();

// Store into array (categories)
$questions =  $row['voting'];
$categories = (explode(",",$questions));

// Get number of categories
$num_cat = count($categories);

if (isset($_POST['submit']))
{
    // if there are votes from same id then you may not vote again
    $query = "SELECT id FROM voting WHERE id = '" . $userID . "' AND event = '" . $eventName . "'";
    $result = mysqli_query($link, $query);
    $num_result = mysqli_num_rows($result);

    if ($num_result == 0)
    {
        for ($i = 0; $i < $num_cat; $i++) {
            if ($_POST[$i] == "Select...") {
                echo 'Please select an option.';
            } else {
                $exhibitName = $_POST[$i];

                $query = "SELECT votes FROM voting WHERE exhibitname = '" . $exhibitName . "' AND event = '" . $eventName . "'";

                $result = mysqli_query($link, $query);
                $num_result = mysqli_num_rows($result);

                if ($num_result == 0) {
                    // if this is the first vote for exhibit
                    $query = "INSERT INTO voting(id, exhibitname, event, votes) VALUES (?, ?, ?, ?);";

                    if($stmt = mysqli_prepare($link, $query)) {
                        // Bind variables to the prepared statement as parameters
                        mysqli_stmt_bind_param($stmt, "isss", $paramId, $paramName, $paramEvent, $paramVote);

                        // Set parameters
                        $paramId = $userID;
                        $paramEvent = $eventName;
                        $paramName = $exhibitName;

                        // initialize array for votes,  with one vote for the current category
                        for ($v = 0; $v < $num_cat; $v++)
                        {
                            $votesArr[$v] = 0;
                        }
                        $votesArr[$i] = 1;

                        $paramVote = implode(', ', $votesArr);

                        if (mysqli_stmt_execute($stmt)) {
                            $successMessage = "Updated Successfully.";
                        } else {
                            $errorMessage = "Error Occurred;";
                        }
                    }
                    // if entry exists then update votes
                } else {
                    $votes = $result->fetch_assoc();
                    $curVote = $votes["votes"];

                    $curVote = explode(", ", $curVote);

                    $curVote[$i] = $curVote[$i] + 1;

                    $query = "UPDATE voting SET votes = ? WHERE exhibitname = ? AND event = ?";

                    if ($stmt = mysqli_prepare($link, $query)) {
                        // Bind variables to the prepared statement as parameters
                        mysqli_stmt_bind_param($stmt, "sss", $paramVote, $paramName, $paramEvent);

                        // Set parameters
                        $paramVote = implode(', ', $curVote);
                        $paramName = $exhibitName;
                        $paramEvent = $eventName;

                        if (mysqli_stmt_execute($stmt)) {
                            $successMessage = "Updated Successfully.";
                        } else {
                            $errorMessage = "Error Occurred;";
                        }
                    }
                }
            }
        }
    }
    else
    {
        echo "You have already voted!";
    }
}

?>

<!doctype html>
<html lang="en">
<head>
    <title>Vote for your Favorite Project</title>
    <style type="text/css">
        <?php echo file_get_contents('style.css'); ?>
    </style>
</head>
<body>
<header>
    <h1>Voting</h1>
</header>
<div id="container">
    <p>
        <div  class="wrapper">
        <form class="vote_form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <?php

            $i = 0;
            while ($i < $num_cat)
            {
                echo '<div class="custom-select" style="width:500px; margin-top:20px; margin-bottom: 20px;">' . $categories[$i];
                $query = "SELECT exhibitname FROM exhibitors WHERE event = '" . $eventName . "'";
                $result = mysqli_query($link, $query);

                // Show the exhibits as options for dropdown menu
                echo '<select class="select" name='.$i.' style="margin-top: 10px;">';
                echo '<option>'."Select...".'</option>';
                while ($exhibitNames = $result->fetch_assoc()) {
                    $name = $exhibitNames['exhibitname'];
                    echo '<option>'.$name.'</option>';
                }
                echo '</select>';

                echo '</div>';
                $i = $i + 1;
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


