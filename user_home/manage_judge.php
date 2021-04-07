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
                $eventName = $row["event_name"];
                $orgName = $row["organization"];
                $role = $row["user_role"];


                if(isset($_GET['link'])){
                    $divInfo = $_GET['link'];
                    $_SESSION['cur_div'] = $divInfo;
                }
                $divInfo = $_SESSION['cur_div'];

                $divInfo = explode("?_?", $divInfo);
                $divIndex = $divInfo[0];
                $divName = $divInfo[1];

                $selected_judges = array();

                if(isset($_POST['add'])){

                    if(!empty($_POST['selected_judges'])) {

                        foreach($_POST['selected_judges'] as $value){
                            $query = "UPDATE users SET user_role = '" . "Judge" . "', division_name = '" . $divIndex . "' WHERE event_name='" . $eventName . "' AND organization='" . $orgName . "' AND user_id='" . $value . "'";
                            $result = mysqli_query($link, $query);
                        }

                    }

                }
                if(isset($_POST['remove'])){

                    if(!empty($_POST['removed_judges'])) {

                        foreach($_POST['removed_judges'] as $value){
                            $query = "UPDATE users SET user_role = '" . "Attendee" . "', division_name = '" . "" . "' WHERE event_name='" . $eventName . "' AND organization='" . $orgName . "' AND user_id='" . $value . "'";
                            $result = mysqli_query($link, $query);
                            /*
                            $query = "DELETE FROM MyGuests WHERE id=3";
                            $result = mysqli_query($link, $query);
                            */
                        }

                    }

                }
                ?>
                    <?php
                    echo '<h3 align="left" style="margin-left:20px;"> Current Judges for Category: '.$divName.'</h3>';
                    echo '<br>';
                    ?>
                    <form name="remove_judge" id="remove_judge" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <?php
                        $query = "SELECT * FROM users WHERE user_role='" . "Judge" . "' AND organization='" . $orgName . "'  AND event_name='" . $eventName . "' AND division_name='" . $divIndex . "'";
                        $result = mysqli_query($link, $query);
                        $empty = 0;
                        while ($row = $result->fetch_assoc())
                        {
                            $empty = 1;
                            $judge_name = $row['user_firstname']." ".$row['user_lastname'];
                            $judge_id = $row['user_id'];

                            echo '<label class="container" style=font-weight:normal !important;">';
                            echo '<input type="checkbox" name="removed_judges[]" value="'.$judge_id.'"/>'.$judge_name;
                            echo'<span class="checkmark"></span>';
                            echo '</label>';
                        }
                        if ($empty == 0)
                        {
                            echo '<p align="left" style="margin-left:20px; font-size: 20px">No Judges Added</p>';
                        }
                        ?>
                        <br>
                        <div class="form-group">
                            <input type="submit" name="remove" class="button" value="Remove">
                        </div>
                    </form>
                    <h3 align="left" style="margin-left:20px;">Add Judges</h3>
                    <br>
                </header>
                <div id="container">
                    <form class="vote_form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <?php
                        $query = "SELECT * FROM users WHERE event_name='" . $eventName . "' AND organization='" . $orgName . "' AND user_role='" . "Attendee" . "'";
                        $result = mysqli_query($link, $query);
                        while ($row = $result->fetch_assoc())
                        {
                            $judge_name = $row['user_firstname']." ".$row['user_lastname'];
                            $judge_id = $row['user_id'];

                            echo '<label class="container" style=font-weight:normal !important;">';
                            echo '<input type="checkbox" name="selected_judges[]" value="'.$judge_id.'" />'.$judge_name;
                            echo'<span class="checkmark"></span>';
                            echo '</label>';
                        }
                        ?>
                        <br>
                        <div class="form-group">
                            <input type="submit" name="add" class="button" value="Add">
                        </div>
                    </form>
                </div>
                <form action="https://www.expoexpress.online/user_home/div_manage_judge.php" method="post">
                    <!-- All your input fields here
                    <input style="position:absolute; bottom:5px;left:5px;" type="submit" name="submit" value="Last Page: Change Divisions">
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
