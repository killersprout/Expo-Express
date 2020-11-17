<?php
// Initialize the session
session_start();

//default
require_once "../db/config.php";

function displayAll(){
    global $link;
    $event = $_SESSION["organization"]; //Store the event name inside event variable to query from

    $sql = "
SELECT id, exhibitname, firstname, lastname FROM exhibitors WHERE event = '$event'";
    //seems to automatically convert upper and lower

    $result = mysqli_query($link,$sql);

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
                  <td>'.$id .'</td> 
                  <td>'.$exhibit .'</td> 
                  <td>'.$firstName .'</td> 
                  <td>'.$lastName .'</td> 
              </tr>';
        }

        /* free result set */
        mysqli_free_result($result);
}



}

if($_SERVER['REQUEST_METHOD'] == "POST"){
    displayAll();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Display Page</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; text-align: left }
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, td, th {
            border: 1px solid black;
            padding: 5px;
        }


    </style>
    <script>
    </script>
</head>
<body>

<div class="table">
    <h1>Table Test</h1>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div id="displayAll">
            <h2>Test to let press button to make table appear</h2>
            <button type="submit">Show Exhibitors</button>
        </div>
    </form>

</div>


</body>
</html>