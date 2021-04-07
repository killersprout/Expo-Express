<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

ob_start();
session_start();

// if the user isnt logged in, go back to login
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: https://www.expoexpress.online/register/index.php");
    exit;
}
include "../db/config.php";
include "includes/user_header.php";

$userid = $_SESSION["user_id"];

$query = "SELECT * FROM users WHERE user_id='" . $userid . "'";
$result = mysqli_query($link, $query);
$row = $result->fetch_assoc();
$eventName = $row["event_name"];
$orgName = $row["organization"];
$role = $row["user_role"];


$userid = $_SESSION["user_id"];


$votesArr = array();
$message = "";

if (empty($eventName))
{
    $message = "No Event Found for User";
}
else
{

    // Get the array of voting catergories from voting
    $query = "SELECT voting FROM voting_questions WHERE organization='" . $orgName . "' AND event = '" . $eventName . "'";
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
        $query = "SELECT id FROM voting WHERE id = '" . $userid . "' AND event = '" . $eventName . "' AND organization = '" . $orgName . "' ";
        $result = mysqli_query($link, $query);
        $num_result = mysqli_num_rows($result);

        if ($num_result == 0)
        {
            $selected_names = array();
            $check = 0;
            for ($i = 0; $i < $num_cat; $i++)
            {
                if ($_POST[$i] == "Select...") {
                    $check = -1;
                    break;
                }
                array_push($selected_names, $_POST[$i]);
            }
            for ($i = 0; $i < $num_cat; $i++) {
                if ($check == -1) {
                    $message = 'Please Complete the Voting Form!';
                } else {
                    $exhibitName = $selected_names[$i];

                    $query = "SELECT votes FROM voting WHERE exhibitname = '" . $exhibitName . "' AND event = '" . $eventName . "' AND organization = '" . $orgName . "'";

                    $result = mysqli_query($link, $query);
                    $num_result = mysqli_num_rows($result);

                    if ($num_result == 0) {
                        // if this is the first vote for exhibit
                        $query = "INSERT INTO voting(id, exhibitname, event, organization, votes) VALUES (?, ?, ?, ?, ?);";

                        if($stmt = mysqli_prepare($link, $query)) {
                            // Bind variables to the prepared statement as parameters
                            mysqli_stmt_bind_param($stmt, "issss", $paramId, $paramName, $paramEvent, $paramOrg, $paramVote);

                            // Set parameters
                            $paramId = $userid;
                            $paramEvent = $eventName;
                            $paramOrg = $orgName;
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

                        $query = "UPDATE voting SET votes = ? WHERE exhibitname = ? AND event = ? AND organization = ?";

                        if ($stmt = mysqli_prepare($link, $query)) {
                            // Bind variables to the prepared statement as parameters
                            mysqli_stmt_bind_param($stmt, "ssss", $paramVote, $paramName, $paramEvent, $paramOrg);

                            // Set parameters
                            $paramVote = implode(', ', $curVote);
                            $paramName = $exhibitName;
                            $paramEvent = $eventName;
                            $paramOrg = $orgName;

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
            $message = '<br>'."You Already Voted!";
        }
    }
}


?>

<style>
    /* Reset Select */
    select {
        -webkit-appearance: none;
        -moz-appearance: none;
        -ms-appearance: none;
        appearance: none;
        outline: 0;
        box-shadow: none;
        border: 0 !important;
        background: #62666b;
        background-image: none;
    }
    /* Remove IE arrow */
    select::-ms-expand {
        display: none;
    }
    /* Custom Select */
    .select {
        position: relative;
        display: flex;
        width: 35em;
        height: 3em;
        line-height: 3;
        background: #414447;
        overflow: hidden;
        border-radius: .25em;
    }
    select {
        flex: 1;
        padding: 0 .5em;
        color: #fff;
        cursor: pointer;
    }
    /* Arrow */
    .select::after {
        content: '\25BC';
        position: absolute;
        top: 0;
        right: 0;
        padding: 0 1em;
        background: #282a2d;
        cursor: pointer;
        pointer-events: none;
        -webkit-transition: .25s all ease;
        -o-transition: .25s all ease;
        transition: .25s all ease;
    }
    /* Transition */
    .select:hover::after {
        color: #f39c12;
    }

    h1, h2, h3, h4, h5, h6 {font-family:Tahoma, sans-serif;}
    body {font-family:Tahoma, sans-serif;}
    .button {
        border-radius: 4px;
        height:35px;
        width:100px;
        background-color: #545456;
        color: white;
        border: none;
        box-shadow: none;
        font-size: 16px;
        margin-left: 20px;
    }
</style>


<!doctype html>
<html lang="en">
<head>
    <title>Vote for your Favorite Project</title>
    <style type="text/css">
        <?php// echo file_get_contents('../css/style.css'); ?>
    </style>
</head>
<body>
<div id="wrapper">

    <!-- Navigation -->
    <?php include "includes/user_navigation.php"; ?>

    <div id="page-wrapper">

        <!-- Page Heading -->
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">
                    Welcome <?php echo " " . $role . ", " .$_SESSION['user_firstname'] . " " .$_SESSION['user_lastname'];?>
                </h1>

        <form class="vote_form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <?php
            if ($message != "")
            {
                echo $message;
            }

            $i = 0;
            while ($i < $num_cat)
            {
                if ($categories[$i] != "")
                {
                    $query = "SELECT exhibit_name FROM users WHERE event_name = '" . $eventName . "' AND organization = '" . $orgName . "' AND user_role = '" . "Exhibitor" . "'";
                    $result = mysqli_query($link, $query);
                    $exh_name = array();

                    while ($exhibitNames = $result->fetch_assoc()) {
                        $name = $exhibitNames['exhibit_name'];
                        $name = trim($name);
                        if (!in_array($name, $exh_name) && !empty($name))
                        {
                            //echo '<option>'.$name.'</option>';
                            array_push($exh_name, $name);
                        }
                    }

                    $num_exh = count($exh_name);
                    sort($exh_name);

                    echo '<h4 align="left" style="margin-left:20px;">'. $categories[$i].'</h4>';
                    echo '<div class="select" align="left" style="margin-left:20px;">';
                    echo '<br>';

                    // Show the exhibits as options for dropdown menu
                    echo '<select name='.$i.' id='.$i.'>';
                    echo '<option>'."Select...".'</option>';
                    for ($j = 0; $j < $num_exh; $j++) {
                        echo '<option>'.$exh_name[$j].'</option>';
                    }
                    echo '</select>';

                    echo '</div>';
                    $i = $i + 1;
                }
            }
            ?>
            <br>
        <div class="form-group">
            <input type="submit" name="submit" class="button" value="Submit">
        </div>
        </form>
            </div>

        </div>
        <!-- /.row -->

    </div>
    <!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->
<?php include "includes/user_footer.php"; ?>

<!--
<form action="https://www.expoexpress.online/user_home/index.php" method="post">
    <input style="position:absolute; top:5px;left:5px;" type="submit" name="submit" value="Return to Homepage">
</form>
-->
</body>
</html>


