<?php
//session_start();
require_once "../db/config.php"; //DB connection

//Show all the exhibitors for for that event
function displayExhibitors(){

    global $link;
    // Not backwards here. ><
    $event = $_SESSION["event"]; //Store the event name inside event variable to query from

    $sql = "
    SELECT id, exhibitname, firstname, lastname FROM exhibitors WHERE event = '$event'";
    //seems to automatically convert upper and lower

    $result = mysqli_query($link, $sql);

    echo '<table border="0" cellspacing="2" cellpadding="2">
        <tr>
            <td> <span style="font-family: Arial; ">ID</span> </td>
            <td> <span style="font-family: Arial; ">Exhibit</span> </td>
            <td> <span style="font-family: Arial; ">First Name</span> </td>
            <td> <span style="font-family: Arial; ">Last Name</span> </td>
        </tr>';

    if ($result) {
        /* fetch associative array */
        while ($row = mysqli_fetch_assoc($result)) {
            $id = $row["id"];
            $exhibit = $row["exhibitname"];
            $firstName = $row["firstname"];
            $lastName = $row["lastname"];

            echo '<tr>
            <td>' . $id . '</td>
            <td>' . $exhibit . '</td>
            <td>' . $firstName . '</td>
            <td>' . $lastName . '</td>
            </tr>';
        }
        /* free result set */
        mysqli_free_result($result);
    }
}


function pickDivisionToJudge(){
    global $link;
    $event = $_SESSION["event"]; //Store the event name inside event variable to query from

    $userid = $_SESSION["id"];
    $sql = "SELECT division FROM community WHERE id = '$userid'";
    $result = mysqli_query($link, $sql);
    $row = mysqli_fetch_assoc($result);
    $divisionname = $row["division"];

    $sql = "SELECT * FROM exhibitors WHERE event = '$event' AND divisionname='$divisionname'";
    $result = mysqli_query($link, $sql);

    echo '<h3>Select Exhibit</h3>';
    echo '<form class="exhibit_form" action="nwelcome.php" method="post">';
    while ($row = mysqli_fetch_assoc($result)) {
        $name = $row["divisionname"] . " - " . $row["exhibitname"] . " - " . $row["event"];
        echo $name . " " . '<input type="checkbox" name="exhibit" value="' . $name . '"> <br>';

    }

    echo '<div class="form-group">';
    echo '<input type="submit" name="submit_pick" class="btn btn-primary" value="Submit">';
    echo '</div>';
    echo '</form>';
    //mysqli_close($link);

    if (isset($_POST['submit_pick'])) {
        $string = $_POST['exhibit'];
        $pieces = explode(" - ", $string);
        $division_name = $pieces[0];
        //echo $division_name;
        $exhibit_name = $pieces[1];
        //echo $exhibit_name;

        //not working
        $query = "SELECT id FROM exhibitors WHERE exhibitname = '$exhibit_name'";  //."' AND WHERE exhibitname = '" . $exhibit_name . "'";
        if($result = mysqli_query($link,$query)){
            $temp = mysqli_fetch_row($result);
            //echo $temp[0];
            $exhibit_id = $temp[0];
            echo $exhibit_id;
            $_SESSION["exhibit_id"] = $exhibit_id;
            //header('Location: https://www.expoexpress.online/voting/judging.php');

        }else
           echo "Nope";

        /*
        $exhibit_id = $row["id"];
        echo $exhibit_id;
        $_SESSION["exhibit_id"] = $exhibit_id;

        header('Location: https://www.expoexpress.online/voting/judging.php');
        */
        /*
        $query = "SELECT username FROM community";
        $result = mysqli_query($link,$query);

        while ($row = mysqli_fetch_assoc($result))
        {
            $cur_username = $row['username'];
            $role = "attendee";
            if (in_array($cur_username, $username_arr))
            {
                $role = "judge";
            }

            $query = "UPDATE community SET role = ? WHERE username = ?";

            if($stmt = mysqli_prepare($link, $query))
            {
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "ss", $paramRole, $paramUsername);

                // Set parameters
                $paramRole = $role;
                $paramUsername = $cur_username;

                // Attempt to execute the prepared statement
                if(mysqli_stmt_execute($stmt)) {
                    $message = "Updated Successfully.";
                } else {
                    $message = "Error Occurred;";
                }

                // Close statement
                mysqli_stmt_close($stmt);
            }
        */
        }


}