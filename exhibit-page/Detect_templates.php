<?php
//Adds all templates to template table

// Connect to Database
$link = mysqli_connect('mysql.expoexpress.online','expoexpressonlin','uUAkiKn5','expoexpress_online');

// Check connection
if (!$link) {
    die("Connection failed: " . mysqli_connect_error());
}

//Clear the table
$sql = "DELETE FROM templates";

if (mysqli_query($link, $sql)) {
    //echo "Table Cleared <br><br>";
}

//Add files to the table
//Get all files in template folder
$dir = getcwd() . "/templates";
$files = scandir($dir);
for ($i = 2; $i < count($files); $i++) {
    $file_link = '/exhibit-page/templates/' . $files[$i];

    $sql = "INSERT INTO templates (title, link) VALUES ('$files[$i]', '$file_link')";
    if (mysqli_query($link, $sql)) {
        //echo $files[$i] . " Added<br>";
    }
}

mysqli_close($link);

header("location: Template_selection.php");

?>