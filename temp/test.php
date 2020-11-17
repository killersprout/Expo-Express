<?php

require_once "../db/config.php"; //DB connection
session_start();


//variables
$paramUsername = $paramFirstname = $paramLastname = $paramId = $paramActive = "";
$username = $firstname = $lastname = $id = $active = "";
$event = $_SESSION["organization"]; //Store the event name inside event variable to query from

/*
so i want an organizer. who is in charge of the event. In this demo. I want ivan.
Ivan needs to be able to see a list of people that are signed up for his event. Technology showcase
*/
$sql = "
SELECT id, exhibitname, firstname, lastname FROM exhibitors WHERE event = '$event'
UNION
SELECT id, role, firstname, lastname FROM community
WHERE event = '$event' ";  //seems to automatically convert upper and lower

$result = mysqli_query($link,$sql);


mysqli_close($link);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; text-align: center; }
        li{font: 18px sans-serif; text-align:left;}
    </style>
</head>
<body>
<div class="wrapper">
<h1>Testing database relation</h1>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

        <div class="w3-container w3-white">
            <ol class="list-unstyled">
                <li><b><?php echo "The organization you are with: " .htmlspecialchars($_SESSION["event"]); //This and organization are backwards ?></b>.</li>
                <li><b><?php echo  "This is your unique ID: " . htmlspecialchars($_SESSION["id"]); ?></b></li>
                <li><b><?php echo "Event that you are hosting: " . htmlspecialchars($_SESSION["organization"]); ?></b></li>
                <li><b><?php echo "Username: " . htmlspecialchars($_SESSION["username"]); ?></b></li>
                <li><b><?php echo "Number of exhibits: " . htmlspecialchars($_SESSION["headcount"]); ?></b></li>

            </ol>
        </div>

        <tbody>
        <?php
        if (mysqli_num_rows($result) > 0) {
            // Display the exhibitors and attendees within the event that is queried at the top
            while($row = mysqli_fetch_assoc($result)) {
                ?>
                <div>
                    <td><b><?php echo "ID: " . $row["id"]; ?></b></td>
                    <td><b><?php echo "First Name: " . $row["firstname"]; ?></b></td>
                    <td><b><?php echo "Last Name: " . $row["lastname"]; ?></b></td>
                    <td><b><?php echo "Exhibit Name: " . $row["exhibitname"]; ?></b></td><br><br>
                </div>

                <?php
            }
        } else {
            echo "0 results";
        }
        ?>
        </tbody>

        <h2>Update Role Test</h2>

        <?php //Need to allow organizer to pick from the database, and update the persons role
            $sql = "SELECT id, role, firstname, lastname FROM community WHERE event = '$event'";
            $$result = mysqli_query($link,$sql);

        
        ?>

</body>
</html>
