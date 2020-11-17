<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
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

$query = "SELECT judging FROM voting_questions WHERE organization='" . $orgName . "'";
$result = mysqli_query($link, $query);
$row = $result->fetch_assoc();
$questions =  $row['judging'];
$categories = (explode(",",$questions));
$num_cat = count($categories);

// to show what the rating is out of
$max_rating = 5;
$max_result = $num_cat * $max_rating;

$query = "SELECT * FROM exhibitors WHERE event = '".$eventName."' AND organization = '".$orgName."'";
$result = mysqli_query($link, $query);
$divisions = array();
while ($div_row = $result->fetch_assoc())
{
    $div =  $div_row['divisionname'];
    array_push($divisions, $div);
}
$divisions = array_unique($divisions);
$num_div = count($divisions);


?>

<!doctype html>
<html lang="en">

<head>
    <title>Judging Results</title>
    <style type="text/css">
        <?php echo file_get_contents('style.css'); ?>
    </style>
</head>

<body>
<header>
    <h1>Judging Results</h1>
</header>
<?php

foreach ($divisions as $div)
{
    echo '<table style="margin-left:auto;margin-right:auto;" >';

    echo '<tr>';
    echo '<th>Division</th>';
    echo '<th>Exhibit Name</th>';
    echo '<th>Final Judging Result</th>';
    echo '</tr>';

    $query = "SELECT exhibitname FROM exhibitors WHERE divisionname = '" .$div. "'";
    $result_exh = mysqli_query($link, $query);

    $ordered_votes = array();
    // for each exhibit
    $exhibits = array();
    while ($exhibit = $result_exh->fetch_assoc()) {
        $num_judges = 0;
        $name = $exhibit['exhibitname'];
        $name = trim($name);
        // to avoid repetitions of exhibits
        if (!(in_array($name, $exhibits)))
        {
            array_push($exhibits, $name);

            // get the judging results from all judges for this exhibit
            $query = "SELECT * FROM judging WHERE event = '" . $eventName . "'  AND exhibitname = '" . $name . "'";
            $result = mysqli_query($link, $query);

            // if no votes then the assign name of exhibit to 0
            if (mysqli_num_rows($result)==0)
            {
                $error = "No Rating Exists";
            }
            else
            {
                $total_vote = 0;
                while ($row = $result->fetch_assoc())
                {
                    $num_judges++;
                    $votes = $row['rating'];
                    $votes = (explode(", ",$votes));
                    // the rating will increase with every judge
                    $max_result *= $num_judges;
                    // total the votes for the exhibit across all categories
                    for ($j = 0; $j < $num_cat; $j++)
                    {
                        $total_vote += $votes[$j];
                    }
                }

                $total = strval($total_vote);
                // if there exists an exhibit with the same number of votes
                if (array_key_exists($total, $ordered_votes) == true)
                {
                    // join two exhibit names with &
                    $saved = $ordered_votes[$total];
                    $ordered_votes[$total] = $saved." <br> ".$name;
                }
                else
                {
                    $ordered_votes[$total] = $name;
                }
            }
        }
    }

    // sort by key (votes)
    krsort($ordered_votes);

    // need to add divisions
    foreach ($ordered_votes as $key => $value) {
        echo '<tr>';
        echo '<td>'.$div.'</td>';
        echo '<td>'.$value.'</td>';
        if ($key == 0)
        {
            echo '<td>No Judging Yet</td>';
        }
        else
        {
            echo '<td>'.$key.' / '.$max_result.'</td>';
        }
        echo '</tr>';
    }

    echo '</table>';
}

?>


</body>
</html>