<?php
require_once "../db/config.php";

// https://www.w3schools.com/js/js_ajax_database.asp give this a try on monday


$sql = "SELECT id, firstname, lastname, exhibitname, organization, event  FROM exhibitors";
$result = mysqli_query($link,$sql);

if (mysqli_num_rows($result) > 0) {
    // output data of each row
    while($row = mysqli_fetch_assoc($result)) {
        echo "id: " . $row["id"]. " - Name: " . $row["firstname"]. " " . $row["lastname"]. "<br>";
        echo "exhibitname: " . $row["exhibitname"]. " - organization: " . $row["organization"]. "  - event: " . $row["event"]. "<br><br>";
    }
} else {
    echo "0 results";
}

mysqli_close($link);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Visiting Pages Test</title>
</head>
<body>


</body>
</html>