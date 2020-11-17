<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "../db/config.php"; //DB connection


if(isset($_GET['exhibitname'])){
    $exhibitname = mysqli_real_escape_string($link,$_GET['exhibitname']);

    $sql = "SELECT * FROM exhibitors WHERE exhibitname ='$exhibitname'";
    $result = mysqli_query($link,$sql);
    $row = mysqli_fetch_array($result);
    echo $exhibitname;
}
//This page needs to display the information that is posted from the exhibitors.
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Exhibit Viewer Test</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; text-align: center; }
        li{font: 18px sans-serif; text-align:left;}
    </style>
</head>
<body>




<footer class="w3-container w3-black">

</footer>

</body>
</html>