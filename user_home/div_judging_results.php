<?php
ob_start();
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "includes/user_header.php";
// if the user isn't logged in, go back to login
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ./index.php");
    exit;
}

$userid = $_SESSION["user_id"];
$role = $_SESSION['user_role'];

?>

<style>
    h1, h2, h3, h4, h5, h6 {font-family:Tahoma, sans-serif;}
    body {font-family:Tahoma, sans-serif;}
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

                include "../db/config.php";
                include "includes/user_header.php";

                // Get event and organization name using user id
                $query = "SELECT * FROM users WHERE user_id='" . $userid . "'";
                $result = mysqli_query($link, $query);
                $row = $result->fetch_assoc();
                $eventName = $row["event_name"];
                $orgName = $row["organization"];


                ?>


                <div class="wrapper">
                    <h2 align="left" style="margin-left:20px;">Choose Division to View Judging Results</h2>
                    <?php
                    $query = "SELECT * FROM categories WHERE organization='" . $orgName . "' AND event_name='" . $eventName . "' AND parent_id='" . 0 . "'";
                    $result = mysqli_query($link, $query);
                    $divArr = array();
                    while ($row = $result->fetch_assoc())
                    {
                        $divName = $row["category"];
                        if (!in_array($divName, $divArr))
                        {
                            $divArr[$divName] = $row["index_cat"];
                        }
                    }

                    ksort ($divArr);
                    echo '<div style="margin-left:20px; font-size: 20px">';
                    foreach($divArr as $name => $index)
                    {
                        echo '<a href="https://www.expoexpress.online/user_home/judging_results.php?link=' . $index."?_?".$name . '">'.$name.'</a>';
                        echo '<br>';
                    }
                    echo '</div>';
                    ?>
                </div>

            </div>

        </div>
        <!-- /.row -->

    </div>
    <!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->
<?php include "includes/user_footer.php"; ?>
