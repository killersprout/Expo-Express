<?php
ob_start();
session_start();
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

include "includes/user_header.php";
// if the user isn't logged in, go back to login
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../register/index.php");
    exit;
}
//if(isset($_GET['p_id'])){
    //$userid = $_GET['judge'];
//}else {
    $userid = $_SESSION["user_id"];
    $role = $_SESSION['user_role'];
//}
?>

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

                if(isset($_GET['p_id'])){
                    $exhibitor_id = $_GET['p_id'];
                    $query = "SELECT * FROM users WHERE user_id = $exhibitor_id";
                    $edit_users = mysqli_query($link,$query);
                    $_SESSION['cur_exh'] = $exhibitor_id;

                }

                //include "includes/user_header.php";


                //$exhibitor_id = 27;
                //$userid = $_SESSION['user_id'];
                $num_rating = 5;
                $exhibitor_id = $_SESSION['cur_exh'];
                //include "../nregister/functions.php";
                $ratingArr = array();

                // Get event and organization name using user id
                //Dis is judge
                $query = "SELECT * FROM users WHERE user_id='" . $userid . "'";
                $result = mysqli_query($link, $query);
                $row = $result->fetch_assoc();
                $eventName = $row["event_name"];
                $orgName = $row["organization"];
                $role = $row["user_role"];

                $comments = "";
                $check = 0;
                if (empty($eventName))
                {
                    echo "No Event Found for User";
                }
                else
                {
                    // get judging questions and store into categories
                    $query = "SELECT * FROM voting_questions WHERE organization='" . $orgName . "' AND event ='" . $eventName . "'";

                    $result = mysqli_query($link, $query);
                    $row = $result->fetch_assoc();
                    $questions =  $row['judging'];
                    //$num_rating = $row['max_rating'];
                    //echo $num_rating;
                    $categories = (explode(",",$questions));
                    $num_cat = count($categories);

                    //update so judge picks who he wants to judge
                    //$exhibitor_id = $_SESSION['exhibit_id'];
                    // check if judge
                    if ($role == "judge" || $role == "Judge") {
                        $filled_out = 0;
                        // the id of the exhibitor who exhibit is being judged
                        $query = "SELECT exhibit_name FROM users WHERE user_id = '" . $exhibitor_id . "'";
                        $result = mysqli_query($link, $query);
                        $num_result = mysqli_num_rows($result);
                        $row = $result->fetch_assoc();
                        $exhibitName =  $row['exhibit_name'];
                        global $exhibitName;// =  $_SESSION['exhibit_name'];

                        if (isset($_POST["submit"])) {

                            if (!empty($_POST['comments']))
                            {
                                $comments = $_POST['comments'];
                            }
                            else
                            {
                                $comments = "";
                            }

                            // Check if user already filled out a judging form for this exhibit
                            $query = "SELECT id FROM judging WHERE id = '" . $userid . "' AND exhibitname = '" . $exhibitName . "' AND event = '" . $eventName . "' AND organization = '" . $orgName . "'";
                            $result_judge = mysqli_query($link, $query);
                            $num_result = mysqli_num_rows($result_judge);
                            for ($i = 0; $i < $num_cat; $i++) {
                                if (!isset($_POST[$i])) {
                                    if ($check == 0)
                                    {
                                        echo "Please Fill out All Fields";
                                        $check = -1;
                                    }
                                } else {
                                    $ratingArr[$i] = $_POST[$i];
                                }
                            }

                            if ($num_result == 0) {
                                $page_message = "Submitted";

                                $query = "INSERT INTO judging(id, exhibitname, rating, comments, event, organization) VALUES (?, ?, ?, ?, ?, ?);";

                                if ($stmt = mysqli_prepare($link, $query)) {
                                    // Bind variables to the prepared statement as parameters
                                    mysqli_stmt_bind_param($stmt, "isssss", $paramId, $paramName, $paramRate, $paramComm, $paramEvent, $paramOrg);

                                    // Set parameters
                                    $paramId = $userid;
                                    $paramName = $exhibitName;
                                    $paramRate = implode(', ', $ratingArr);
                                    $paramComm = $comments;
                                    $paramEvent = $eventName;
                                    $paramOrg = $orgName;

                                    if (mysqli_stmt_execute($stmt)) {
                                        $page_message =  "Updated Successfully.";
                                    } else {
                                        $page_message =  "Error Occurred;";
                                    }
                                }
                            } else if ($check == 0){
                                $page_message = "Updated";
                                //update ratings
                                $paramRate = implode(', ', $ratingArr);

                                $query = "UPDATE judging SET rating = ?, comments = ? WHERE id = ? AND exhibitname = ? AND event = ? AND organization = ?;";

                                if ($stmt = mysqli_prepare($link, $query)) {
                                    // Bind variables to the prepared statement as parameters
                                    mysqli_stmt_bind_param($stmt, "ssisss", $paramRate, $paramComm, $paramId, $paramName, $paramEvent, $paramOrg);

                                    // Set parameters
                                    $paramRate = implode(', ', $ratingArr);
                                    $paramComm = $comments;
                                    $paramId = $userid;
                                    $paramName = $exhibitName;
                                    $paramEvent = $eventName;
                                    $paramOrg = $orgName;

                                    if (mysqli_stmt_execute($stmt)) {
                                        $page_message =  "Updated Successfully.";
                                    } else {
                                        $page_message =  "Error Occurred;";
                                    }
                                }
                            }
                        }

                    }
                    else {
                        $page_message = "You're Not a Judge";
                    }
                }



                ?>
                <style>
                    input[type="radio"]{
                        margin: 0 10px 0 10px;
                        display: inline-block;
                    }
                </style>

                <div id="container">
                    <p>
                    <div  class="wrapper">
                        <form class="judge_form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <?php

                            $query = "SELECT exhibit_name FROM users WHERE user_id = '" . $exhibitor_id . "'";
                            $result = mysqli_query($link, $query);
                            $num_result = mysqli_num_rows($result);
                            $row = $result->fetch_assoc();
                            $exhibitName =  $row['exhibit_name'];//Will need to be changed, cause the division and exhibit are backwards

                            if ($num_result == 0){
                                echo "No Exhibit of that ID Exists";
                            }
                            else{
                                //Name of Exhibit that is being Judged
                                echo '<h1 class="exhibitName" style="margin-top: 10px;">'."Exhibit: ".$exhibitName.'</h1>';
                            }

                            $query = "SELECT * FROM judging WHERE id = '" . $userid . "' AND exhibitname = '" . $exhibitName . "' AND event = '" . $eventName . "' AND organization = '" . $orgName . "'";
                            $result_judge = mysqli_query($link, $query);
                            $row = $result_judge->fetch_assoc();
                            // depends on number of ratings possible
                            $num_rating = 5;
                            //echo $num_rating;
                            $prev_comments = "";
                            if (!empty($row) && $check == 0)
                            {
                                $prev_comments = $row['comments'];
                                $votes = $row['rating'];
                                $votes = (explode(", ",$votes));
                                $i = 0;
                                foreach ($categories as $value) {
                                    echo '<fieldset id='.$i.'>';
                                    echo '<p>'.$value.'</p>';
                                    for ($j = 1; $j <= $num_rating; $j++)
                                    {
                                        echo '<label for='.$j.'>'.$j;
                                        if ($votes[$i] == $j)
                                        {
                                            echo '<input type="radio" name='.$i.' value='.$j.' checked="checked">';
                                        }
                                        else
                                        {
                                            echo '<input type="radio" name='.$i.' value='.$j.'>';
                                        }
                                        echo '</label>';
                                    }

                                    echo '</fieldset>';

                                    $i++;
                                }
                            }
                            else {
                                $i = 0;
                                foreach ($categories as $value) {
                                    echo '<fieldset id=' . $i . '>';
                                    echo '<p>' . $value . '</p>';

                                    for ($j = 1; $j <= $num_rating; $j++) {
                                        echo '<label for=' . $j . '>' . $j;

                                        echo '<input type="radio" name=' . $i . ' value=' . $j . '>';

                                        echo '</label>';
                                    }

                                    echo '</fieldset>';
                                    $i++;
                                }
                            }

                            ?>
                            <div>
                                <label for="comment_label">Comments :</label>
                                <br>
                                <textarea style="width: 500px; height: 200px;word-wrap: break-word; word-break: break-all;" name=comments><?php echo $prev_comments; ?></textarea>
                            </div>
                            <br>
                            <div class="form-group" method="post">
                                <input type="submit" name="submit" class="btn btn-primary" value="Submit">
                            </div>
                        </form>

                    </div>
                    </p>
                </div>
                <form action="../exhibit-page/Display_exhibit.php?p_id=<?php echo $exhibitor_id;?>" method="post">
                    <!-- All your input fields here
                    <input style="position:absolute; bottom:5px;left:5px;" type="submit" name="submit" value="Back to Exhibit">
                    -->
                </form>


            </div>

        </div>
        <!-- /.row -->

    </div>
    <!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->
<?php include "includes/user_footer.php"; ?>
