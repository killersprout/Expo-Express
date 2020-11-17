<?php
session_start();

// if the user isnt logged in, go back to login
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: https://www.expoexpress.online/register/login.php");
    exit;
}

$userid = $_SESSION['id'];

include "../db/config.php";

$query = "SELECT event FROM community WHERE id='" . $userid . "'";
$result = mysqli_query($link, $query);
$row = $result->fetch_assoc();
$eventName = $row["event"];

$query = "SELECT organization FROM organizers WHERE event='" . $eventName . "'";
$result = mysqli_query($link, $query);
$row = $result->fetch_assoc();
$orgName =  $row['organization'];
echo $orgName;

$query = "SELECT voting FROM voting_questions WHERE organization='" . $orgName . "'";
$result = mysqli_query($link, $query);
$row = $result->fetch_assoc();
$questions =  $row['voting'];
$categories = (explode(",",$questions));
$num_cat = count($categories);


?>

<!doctype html>
<html lang="en">

<head>
    <title>Voting Results</title>
    <style type="text/css">
        <?php echo file_get_contents('style.css'); ?>
    </style>
</head>

<body>
<header>
    <h1>Voting Results</h1>
</header>
<?php

for ($i = 0; $i < $num_cat; $i++)
{
    echo '<table style="margin-left:auto;margin-right:auto;" >';

    echo '<caption>'.$categories[$i].'</caption>';

    echo '<tr>';
    echo '<th>Exhibit Name</th>';
    echo '<th>Final Votes</th>';
    echo '</tr>';

    $query = "SELECT exhibitname FROM exhibitors WHERE event = '" . $eventName . "'";
    $result_exh = mysqli_query($link, $query);

    $ordered_votes = array();
    // for each exhibit
    while ($exhibitNames = $result_exh->fetch_assoc()) {
        $name = $exhibitNames['exhibitname'];
        $query = "SELECT * FROM voting WHERE exhibitname = '" . $name . "' AND event = '" . $eventName . "'";
        $result = mysqli_query($link, $query);
        $num_exh = count($result);

        // if no votes then the assign name of exhibit to 0
        if ($num_exh == 0)
        {
            $ordered_votes['0'] = $name;
        }
        else
        {
            $total_vote = 0;

            while ($row = $result->fetch_assoc())
            {
                // get the vote for this category
                $votes = $row['votes'];
                $votes = (explode(", ",$votes));
                $cat_vote = $votes[$i];
                $total_vote = $total_vote + $cat_vote;
            }
            $total = strval($total_vote);
            // if there exists an exhibit with the same number of votes
            if (array_key_exists($total, $ordered_votes) == true)
            {
                // join two exhibit names with &
                $saved = $ordered_votes[$total];
                $ordered_votes[$total] = $saved." & ".$name;
            }
            else
            {
                $ordered_votes[$total] = $name;
            }
        }
    }
    // sort by key (votes)
    krsort($ordered_votes);

    foreach ($ordered_votes as $key => $value) {
        echo '<tr>';
        echo '<td>'.$value.'</td>';
        echo '<td>'.$key.'</td>';
        echo '</tr>';
    }

    echo '</table>';
}
?>


</body>
</html>