<?php
ob_start();
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "includes/user_header.php";
include "../db/config.php";
// if the user isn't logged in, go back to login
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ./register");
    exit;
}

$userid = $_SESSION["user_id"];
$role = $_SESSION['user_role'];
$num_visible_exh = 0;


?>
<style>
    table, th, td {
        border: 1px solid black;
        width:50%;
        text-align: center;
        cellpadding=5px;
        word-wrap: break-word;
    }
    th, td {
        padding: 30px;
    }
</style>
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

                <?php

                $query = "SELECT * FROM users WHERE user_id='" . $userid . "'";
                $result = mysqli_query($link, $query);
                $row = $result->fetch_assoc();
                $eventName = trim($row["event_name"]);
                $orgName = trim($row["organization"]);
                $role = trim($row["user_role"]);

                $query = "SELECT * FROM voting_questions WHERE organization='" . $orgName . "' AND event = '" . $eventName . "'";
                $result = mysqli_query($link, $query);
                $row = $result->fetch_assoc();
                $questions =  $row['voting'];
                $num_visible_exh = $row['voting_visible'];
                $categories = (explode(",",$questions));
                $num_cat = count($categories);
                //echo $num_cat;

                if ($num_visible_exh == 0)
                {
                    $num_visible_exh = 10;
                }

                if (isset($_POST['submit']))
                {
                    $num_visible_exh = $_POST['num_results'];
                    $query = "UPDATE voting_questions SET voting_visible='" . $num_visible_exh . "' WHERE organization='" . $orgName . "' AND event = '" . $eventName . "'";
                    $stmt = mysqli_prepare($link, $query);
                    if (mysqli_stmt_execute($stmt)) {
                        $successMessage = "Updated Successfully.";
                    } else {
                        $errorMessage = "Error Occurred;";
                    }
                }


                ?>


                <h2>Voting Results</h2>
                <?php
                if ($role == "Organizer")
                {
                    echo '<form method="post" action="https://www.expoexpress.online/user_home/voting_results.php">';
                    echo '<label for="num_results">How Many Results Should Be Viewable?</label><br>';
                    echo '<input type="text" id="num_results" name="num_results" placeholder='.$num_visible_exh.'><br>';
                    echo '<input type="submit" id="submit" name="submit" value="Submit">';
                    echo '</form>';
                }

                for ($i = 0; $i < $num_cat; $i++)
                {
                    $query = "SELECT exhibit_name FROM users WHERE event_name = '" . $eventName . "' AND organization = '" . $orgName . "' AND user_role = '" . "Exhibitor" . "'";
                    $result_exh = mysqli_query($link, $query);

                    $ordered_votes = array();
                    $repeat = array();
                    // for each exhibit
                    while ($exhibitNames = $result_exh->fetch_assoc()) {
                        $name = trim($exhibitNames['exhibit_name']);
                        //echo $name;
                        if (!in_array($name, $repeat) && !empty($name)){
                            array_push($repeat, $name);
                            $query = "SELECT * FROM voting WHERE exhibitname = '" . $name . "' AND event = '" . $eventName . "' AND organization = '" . $orgName . "'";
                            $result = mysqli_query($link, $query);

                            // if no votes then the assign name of exhibit to 0
                                $total_vote = 0;
                                if ($result)
                                {
                                    while ($row = $result->fetch_assoc()) {
                                        $empty = 0;
                                        // get the vote for this category
                                        $votes = $row['votes'];
                                        $votes = (explode(", ", $votes));
                                        $cat_vote = $votes[$i];
                                        $total_vote = $total_vote + $cat_vote;
                                    }

                                        $total = strval($total_vote);
                                        $ordered_votes[$name] = $total;

                                }
                                else
                                {
                                    $ordered_votes[$name] = 0;
                                }


                        }
                    }
                    // sort by key (votes)
                    arsort($ordered_votes);

                    $ordered_votes = array_slice($ordered_votes, 0, $num_visible_exh);

                    if (!empty($ordered_votes))
                    {
                        echo '<table style="margin-left:auto;margin-right:auto;" >';

                        echo '<caption>'.$categories[$i].'</caption>';

                        echo '<tr>';
                        echo '<th style="text-align: center;">Exhibit Name</th>';
                        echo '<th style="text-align: center;">Final Votes</th>';
                        echo '</tr>';

                        foreach ($ordered_votes as $key => $value) {
                            echo '<tr>';
                            echo '<td>'.$key.'</td>';
                            echo '<td>'.$value.'</td>';
                            echo '</tr>';
                        }

                        echo '</table>';
                    }
                    else
                    {
                        echo '<h3>No Votes</h3>';
                    }
                }
                ?>


            </div>

        </div>
        <!-- /.row -->

    </div>
    <!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->
<?php include "includes/user_footer.php"; ?>
