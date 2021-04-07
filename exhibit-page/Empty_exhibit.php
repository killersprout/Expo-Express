<?php
//Check if user has draft already created other create new
session_start();

//if the user isn't logged in, go back to login
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ../user_home");
    exit;
}

//Check already saved data & load it
$user = $_SESSION["username"];
$event = $_SESSION["event"];
$id = $_SESSION["user_id"];

// Connect to Database
$link = mysqli_connect('mysql.expoexpress.online','expoexpressonlin','uUAkiKn5','expoexpress_online');

// Check connection
if (!$link) {
    die("Connection failed: " . mysqli_connect_error());
}

//Fetch user exhibit from database
$sql = "DELETE FROM exhibits WHERE exhibitor = '$user' AND is_published = 0";

//Delete draft .txt file
if (file_exists(getcwd() . '/exhibit_texts/' . $id . "_draft.txt")) {
    unlink(getcwd() . '/exhibit_texts/' . $id . "_draft.txt");
}

$result = mysqli_query($link, $sql);

mysqli_close($link);

header("Location: File_upload.php");
?>