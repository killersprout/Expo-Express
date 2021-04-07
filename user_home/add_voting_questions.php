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
$orgName = $_SESSION['organization'];
$eventName = $_SESSION['event_name'];

$voting_cat = array();
$num_cat = 0;

$query = "SELECT voting FROM voting_questions WHERE organization ='".$orgName."' AND event = '".$eventName."'";
$result = mysqli_query($link, $query);
$row = $result->fetch_assoc();
if (empty($row))
{
    $query = "INSERT INTO voting_questions(organization, event, voting, judging, voting_visible) VALUES (?, ?, ?, ?, ?);";

    if($stmt = mysqli_prepare($link, $query)) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "sssss", $paramOrg, $paramEvent, $paramVoting, $paramJudging, $paramVisible);

        // Set parameters
        $paramOrg = $orgName;
        $paramEvent = $eventName;
        $paramVoting = "";
        $paramJudging = "";
        $paramVisible = 0;

        if (mysqli_stmt_execute($stmt)) {
            $successMessage = "Updated Successfully.";
        } else {
            $errorMessage = "Error Occurred;";
        }
    }
}
else
{
    if ($row["voting"] !== "")
    {
        $voting_cat = $row["voting"];
        $voting_cat = explode(',', $voting_cat);
        $num_cat = count($voting_cat);
    }
}

?>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<!-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script> -->

<style>
    /* Customize the label (the container) */
    .container {
        position: relative;
        padding-left: 35px;
        margin-left: 20px;
        margin-bottom: 20px;
        cursor: pointer;
        font-size: 18px;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        width:30em;
        column-count: auto;
    }

    /* Hide the browser's default checkbox */
    .container input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        height: 0;
        width: 0;
    }

    /* Create a custom checkbox */
    .checkmark {
        position: absolute;
        top: 0;
        left: 0;
        height: 25px;
        width: 25px;
        background-color: #eee;
    }

    /* On mouse-over, add a grey background color */
    .container:hover input ~ .checkmark {
        background-color: #ccc;
    }

    /* When the checkbox is checked, add a blue background */
    .container input:checked ~ .checkmark {
        background-color: #031828;
    }

    /* Create the checkmark/indicator (hidden when not checked) */
    .checkmark:after {
        content: "";
        position: absolute;
        display: none;
    }

    /* Show the checkmark when checked */
    .container input:checked ~ .checkmark:after {
        display: block;
    }

    /* Style the checkmark/indicator */
    .container .checkmark:after {
        left: 9px;
        top: 5px;
        width: 5px;
        height: 10px;
        border: solid white;
        border-width: 0 3px 3px 0;
        -webkit-transform: rotate(45deg);
        -ms-transform: rotate(45deg);
        transform: rotate(45deg);
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

<div id="wrapper">

    <!-- Navigation -->
    <?php include "includes/user_navigation.php"; ?>
    <script type="text/javascript" src="js/script.js"></script>

    <div id="page-wrapper">

        <!-- Page Heading -->
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">
                    Welcome <?php echo " " . $role . ", " .$_SESSION['user_firstname'] . " " .$_SESSION['user_lastname'];?>
                </h1>
                <?php
                //echo "ID of Current User: ".$userid;

                // Get event and organization name using user id
                $query = "SELECT * FROM users WHERE user_id='" . $userid . "'";
                $result = mysqli_query($link, $query);
                $row = $result->fetch_assoc();
                $eventName = $row["event_name"];
                $orgName = $row["organization"];

                $successMessage =  "";
                $errors = [];
                $valueArr = array();
                $numQuestions = 0;
                $judging = "";

                if (isset($_POST['add'])){
                    if (!empty($_POST["division"])) {
                        foreach ($_POST["division"] as $key => $value) {
                            array_push($voting_cat, $value);
                        }
                    }

                    $query = "UPDATE voting_questions SET voting = ? WHERE organization = ? AND event = ?";

                    if ($stmt = mysqli_prepare($link, $query)) {
                        mysqli_stmt_bind_param($stmt, "sss", $paramQuestion, $paramOrg, $paramEvent);
                        $paramOrg = $orgName;
                        $paramEvent = $eventName;
                        $paramQuestion = implode(', ', $voting_cat);

                        if (mysqli_stmt_execute($stmt)) {
                            $successMessage = "Updated Successfully.";
                        } else {
                            $errors[$key] = "Error Occurred;";
                        }
                    } else {
                        echo "Could not Update";
                    }

                    // Close statement
                    mysqli_stmt_close($stmt);

                }
                elseif (isset($_POST['remove']))
                {

                    if (!empty($_POST["removed_judges"])) {
                        foreach ($_POST["removed_judges"] as $key => $value) {
                            if (($key = array_search($value, $voting_cat)) !== false) {
                                unset($voting_cat[$key]);
                            }
                        }
                    }

                    $query = "UPDATE voting_questions SET voting = ? WHERE organization = ? AND event = ?";

                    if ($stmt = mysqli_prepare($link, $query)) {
                        mysqli_stmt_bind_param($stmt, "sss", $paramQuestion, $paramOrg, $paramEvent);
                        $paramOrg = $orgName;
                        $paramEvent = $eventName;
                        $paramQuestion = implode(', ', $voting_cat);

                        if (mysqli_stmt_execute($stmt)) {
                            $successMessage = "Updated Successfully.";
                        } else {
                            $errors[$key] = "Error Occurred;";
                        }
                    } else {
                        echo "Could not Update";
                    }

                    // Close statement
                    mysqli_stmt_close($stmt);
                }

                ?>

                <div class="wrapper">
                    <?php
                    echo '<h3 align="left" style="margin-left:20px;">Current Voting Questions for Event: '.$eventName.'</h3>';
                    ?>
                    <br>
                    <form name="remove_vote_cat" id="remove_vote_cat" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <?php

                        $query = "SELECT voting FROM voting_questions WHERE organization ='".$orgName."' AND event = '".$eventName."'";
                        $result = mysqli_query($link, $query);
                        $row = $result->fetch_assoc();
                        $voting_cat = array();
                        if (!empty($row["voting"]))
                        {
                            $voting_cat = $row["voting"];
                            $voting_cat = explode(',', $voting_cat);
                        }
                        $num_cat = count($voting_cat);
                        $empty = 0;
                        for ($i = 0; $i < $num_cat; $i++)
                        {
                            $empty = 1;
                            echo '<label class="container" style=font-weight:normal !important;">';
                            echo '<input type="checkbox" name="removed_judges[]" value="'.$voting_cat[$i].'"/>'.$voting_cat[$i];
                            echo'<span class="checkmark"></span>';
                            echo '</label>';
                        }
                        if ($empty == 0)
                        {
                            echo "No Questions Added";
                        }
                        ?>
                        <div class="form-group">
                            <input type="submit" name="remove" class="button" value="Remove"/>
                        </div>
                    </form>
                    <h3 align="left" style="margin-left:20px;">Add Voting Categories </h3>
                    <form align='center' name="add_division" id="add_division" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

                        <div class="table-responsive">
                            <table class="table table-bordered" id="dynamic_field">
                                <tr>
                                    <td><input type="text" name="division[]" placeholder="Enter Voting Category" class="form-control division_list" required="" /></td>
                                    <td><button type="button" name="add" id="add" class="btn btn-success">Add Another</button></td>
                                </tr>
                            </table>
                        </div>

                        <div class="form-group">
                            <input type="submit" name="add" class="button" value="Add">
                        </div>

                    </form>
                </div>

                <script type="text/javascript">
                    $(document).ready(function(){
                        var postURL = "/add_expo_divisions.php";
                        var i=1;


                        $('#add').click(function(){
                            i++;
                            $('#dynamic_field').append('<tr id="row'+i+'" class="dynamic-added"><td><input type="text" name="division[]" placeholder="Enter New Division" class="form-control division_list" required /></td><td><button type="button" name="remove" id="'+i+'" class="btn btn-danger btn_remove">X</button></td></tr>');
                        });


                        $(document).on('click', '.btn_remove', function(){
                            var button_id = $(this).attr("id");
                            $('#row'+button_id+'').remove();
                        });


                        $('#submit').click(function(){
                            $.ajax({
                                url:postURL,
                                method:"POST",
                                data:$('#add_division').serialize(),
                                type:'json',
                                success:function(data)
                                {
                                    i=1;
                                    $('.dynamic-added').remove();
                                    $('#add_division')[0].reset();
                                    alert('Record Inserted Successfully.');
                                }
                            });
                        });


                    });
                </script>
                </body>
                </html>

            </div>



        </div>

        </div>
        <!-- /.row -->

    </div>
    <!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->
<?php include "includes/user_footer.php"; ?>
