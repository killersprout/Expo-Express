<?php

require_once "../db/config.php"; //DB connection

//Show all the exhibitors for for that event
function displayExhibitors(){

    global $link;
    //This is backwards and I dont know why
    $event = $_SESSION["organization"]; //Store the event name inside event variable to query from

    $sql = "
    SELECT id, exhibitname, divisionname FROM exhibitors WHERE event = '$event'";
    //seems to automatically convert upper and lower

    $result = mysqli_query($link, $sql);

    echo '<table border="0" cellspacing="2" cellpadding="2">
        <tr>
            <td> <span style="font-family: Arial; ">ID</span> </td>
            <td> <span style="font-family: Arial; ">Exhibit</span> </td>
            <td> <span style="font-family: Arial; ">Division</span> </td>
        </tr>';

    if ($result) {
        /* fetch associative array */
        while ($row = mysqli_fetch_assoc($result)) {
            $id = $row["id"];
            $exhibit = $row["exhibitname"];
            $division = $row["divisionname"];

            echo '<tr>
        <td>' . $id . '</td>
        <td>' . $exhibit . '</td>
        <td>' . $division . '</td>
    </tr>';
        }
        /* free result set */
        mysqli_free_result($result);
    }
}

function pickJudge()
{
    global $link;
    $string = $_POST['judge'];
    $username_arr = array();

    foreach ($string as $judge) {
        $pieces = explode(" - ", $judge);
        $username = $pieces[1];
        array_push($username_arr, $username);
    }

    $query = "SELECT username FROM community";
    $result = mysqli_query($link, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $cur_username = $row['username'];
        if (in_array($cur_username, $username_arr)) {
            $role = "attendee";
            if (in_array($cur_username, $username_arr)) {
                $role = "judge";

                if ($_POST[$cur_username] == "Select...") {
                    // prints for each judge
                    echo 'Please select division for judge';
                } else {
                    $div_name = $_POST[$cur_username];
                    $query = "UPDATE community SET division = ? WHERE username = ?";

                    if($stmt = mysqli_prepare($link, $query))
                    {
                        // Bind variables to the prepared statement as parameters
                        mysqli_stmt_bind_param($stmt, "ss", $paramDiv, $paramUsername);

                        // Set parameters
                        $paramDiv = $div_name;
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
                }
            }
            $query = "UPDATE community SET role = ? WHERE username = ?";

            if ($stmt = mysqli_prepare($link, $query)) {
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "ss", $paramRole, $paramUsername);

                // Set parameters
                $paramRole = $role;
                $paramUsername = $cur_username;

                // Attempt to execute the prepared statement
                if (mysqli_stmt_execute($stmt)) {
                    $message = "Updated Successfully.";
                } else {
                    $message = "Error Occurred;";
                }
                // Close statement
                mysqli_stmt_close($stmt);
            }
        }
    }
}

function deselectJudge(){
    global $link;
    $string = $_POST['judge'];
    $username_arr = array();

    foreach ($string as $judge) {
        $pieces = explode(" - ", $judge);
        $username = $pieces[1];
        array_push($username_arr, $username);
    }

    $query = "SELECT username FROM community";
    $result = mysqli_query($link, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        $cur_username = $row['username'];
        if (in_array($cur_username, $username_arr)) {
            $role = "attendee";
            if (in_array($cur_username, $username_arr)) {
                $role = "attendee";
            }
            $query = "UPDATE community SET role = ?, division = ? WHERE username = ?";

            if ($stmt = mysqli_prepare($link, $query)) {
                // Bind variables to the prepared statement as parameters
                mysqli_stmt_bind_param($stmt, "sss", $paramRole, $paramDiv, $paramUsername);

                // Set parameters
                $paramRole = $role;
                $paramDiv = "";
                $paramUsername = $cur_username;

                // Attempt to execute the prepared statement
                if (mysqli_stmt_execute($stmt)) {
                    $message = "Updated Successfully.";
                } else {
                    $message = "Error Occurred;";
                }
                // Close statement
                mysqli_stmt_close($stmt);
            }
        }
    }



}