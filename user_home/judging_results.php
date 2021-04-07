<?php
ob_start();
session_start();
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

include "includes/user_header.php";
// if the user isn't logged in, go back to login
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ./register");
    exit;
}

$userid = $_SESSION["user_id"];
$role = $_SESSION['user_role'];

?>

<style>
    table, th, td {
        border: 1px solid black;
    }
    th, td {
        padding: 15px;
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


                $divName = explode("?_?", $_GET['link']);
                $divID = $divName[0];
                $divName = $divName[1];

                // Get event and organization name using user id
                $query = "SELECT * FROM users WHERE user_id='" . $userid . "'";
                $result = mysqli_query($link, $query);
                $row = $result->fetch_assoc();
                $eventName = $row["event_name"];
                $orgName = $row["organization"];
                $max_rating = 5;

                $query = "SELECT * FROM voting_questions WHERE organization='" . $orgName . "' AND event ='" . $eventName . "' ";
                $result = mysqli_query($link, $query);
                $row = $result->fetch_assoc();
                $questions =  $row['judging'];
                //$max_rating = $row['max_rating'];
                $categories = (explode(",",$questions));
                $num_cat = count($categories);
                $max_possible = $max_rating * $num_cat;


                ?>


                <?php
                $div = $divID;

                echo '<table style="margin-left:auto;margin-right:auto; width: 100%;border-bottom: 1px solid black;" >';

                echo '<caption>'."Division: ".$divName.'</caption>';

                echo '<tr style="border-bottom: 1px solid black;">';
                echo '<th >Exhibit Name</th>';

                // what if "judge"
                $query = "SELECT * FROM users WHERE user_role = '" . "Judge" . "'  AND organization = '" .$orgName. "' AND event_name = '" .$eventName. "' AND division_name = '" .$div. "'";
                $result = mysqli_query($link, $query);
                $num_judges = mysqli_num_rows($result);
                $judge_id = array();
                while ($row = $result->fetch_assoc())
                {
                    $name = $row["user_firstname"] . " " . $row["user_lastname"];
                    array_push($judge_id, $row["user_id"]);
                    echo '<th>'."Judge: ".$name.'</th>';
                }

                echo '<th>Averaged Result</th>';
                echo '</tr>';

                $query = "SELECT exhibit_name FROM users WHERE user_role = '" . "Exhibitor" . "' AND event_name = '" . $eventName . "' AND organization = '" . $orgName . "' AND division_name = '" . $div . "'";
                $result_exh = mysqli_query($link, $query);

                $ordered_votes = array();
                // for each exhibit
                $exhibits = array();
                $scores = array();
                $exhibit_order = array();
                $num_exh = 0;
                while ($exhibit = $result_exh->fetch_assoc())
                {
                    $name_exh = $exhibit['exhibit_name'];
                    $name_exh = trim($name_exh);
                    if (!in_array($name_exh, $exhibits))
                    {
                        array_push($exhibits, $name_exh);

                        $totalScore = 0;
                        $judgesVoted = 0;
                        for ($i = 0; $i < $num_judges; $i++)
                        {
                            $query = "SELECT * FROM judging WHERE event = '" . $eventName . "'  AND exhibitname = '" . $name_exh . "' AND id = '" . $judge_id[$i] . "'";
                            $result_judge = mysqli_query($link, $query);
                            if ($ratings = $result_judge->fetch_assoc())
                            {
                                $comments = $ratings['comments'];
                                $ratingArr = (explode(", ", $ratings["rating"]));
                                $totalRating = 0;
                                for ($j = 0; $j < $num_cat; $j++)
                                {
                                    $totalRating += $ratingArr[$j];
                                }

                                // to get average score
                                $totalScore = $totalScore + $totalRating;
                                $judgesVoted += 1;
                            }
                        }
                        if ($judgesVoted != 0)
                        {
                            $avgScore = $totalScore/$judgesVoted;
                            $scores[$name_exh] = $avgScore;
                        }
                        else
                        {
                            $scores[$name_exh] = 0;
                        }
                        $num_exh++;
                    }
                }
                arsort($scores);
                foreach ($scores as $exh => $rate){
                    echo '<tr>';
                    echo '<td>' . $exh . '</td>';
                    $judgesVoted = 0;
                    for ($i = 0; $i < $num_judges; $i++) {
                        $query = "SELECT * FROM judging WHERE event = '" . $eventName . "'  AND exhibitname = '" . $exh . "' AND id = '" . $judge_id[$i] . "'";
                        $result_judge = mysqli_query($link, $query);
                        if ($ratings = $result_judge->fetch_assoc())
                        {
                            $comments = $ratings['comments'];
                            $ratingArr = (explode(", ", $ratings["rating"]));
                            $totalRating = 0;
                            for ($j = 0; $j < $num_cat; $j++)
                            {
                                $totalRating += $ratingArr[$j];
                            }
                            echo '<td>' . $totalRating . " / " . $max_possible .
                                '<br> <b>'."Comments:".'</b> <br>'.$comments. '<br>'.'</td>';
                            $judgesVoted += 1;
                        }
                        else {
                            echo '<td>' . "No Rating Available." . '</td>';
                        }
                    }
                    if ($judgesVoted != 0)
                    {
                        $rate = number_format($rate, 2);
                        echo '<td>' . $rate . " / " . $max_possible . '</td>';
                    }
                    else{
                        echo '<td>' . "Not Available." . '</td>';
                    }
                }

                echo '</table>';
                echo '<br>';

                ?>
                <form action="https://www.expoexpress.online/user_home/div_judging_results.php" method="post">
                    <!-- All your input fields here -->
                    <input style="position:relative; bottom:5px;left:5px;" type="submit" name="submit2" value="Choose Another Division">
                </form>

            </div>

        </div>
        <!-- /.row -->

    </div>
    <!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->
<?php include "includes/user_footer.php"; ?>
