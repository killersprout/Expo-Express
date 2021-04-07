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

//Fetch published exhibit from database
$sql = "SELECT * FROM exhibits WHERE exhibitor = '$user' AND is_published=1";

$published = '';
if ($result = mysqli_query($link, $sql)) {
    $published = mysqli_fetch_row($result);
}

$sql = "INSERT INTO exhibits (is_published, exhibitor, event, title, exhibit_id, visits) VALUES (false, '$published[1]', '$published[2]', '$published[3]', '$published[4]', '$published[5]')";
if (mysqli_query($link, $sql)) {
    //echo "Draft created successfully";
}

//Delete existing draft .txt file
if (file_exists(getcwd() . '/exhibit_texts/' . $id . "_draft.txt")) {
    unlink(getcwd() . '/exhibit_texts/' . $id . "_draft.txt");
}

//Create draft using the already published exhibit
copy(getcwd() . '/exhibit_texts/' . $id . "_published.txt", getcwd() . '/exhibit_texts/' . $id . "_draft.txt");

mysqli_close($link);

header("Location: File_upload.php");
?>