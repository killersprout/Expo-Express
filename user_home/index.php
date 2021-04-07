<?php
ob_start();
session_start();
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

include "includes/user_header.php";
// if the user isn't logged in, go back to login
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ./register");
    exit;
}

$userid = $_SESSION["user_id"];
$role = $_SESSION['user_role'];

?>
<?php //This breaks the dropdown menu stuff
//<script src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
//<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
//<script src="//code.jquery.com/jquery-1.12.0.min.js"></script>
?>

<style>
    .optionGroup {
        font-weight: bold;
        font-style: italic;
    }

    .optionChild {
        padding-left: 15px;
    }


</style>

<div id="wrapper">

    <!-- Navigation -->
    <?php include "includes/user_navigation.php";?>

    <div id="page-wrapper">

            <!-- Page Heading -->
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">
                        Welcome <?php echo " " . $role . ", " .$_SESSION['user_firstname'] . " " .$_SESSION['user_lastname'];?>
                    </h1>

                    <?php if ($_SESSION['user_role'] == "Attendee") { ?>

                    <div  class="wrapper" style="background-color: rgba(0, 0, 0, 0.05); padding: 5px;">
                        <form class="organ_form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <?php

                            if ($_SESSION['user_role'] == "Attendee") {
                                echo '<div class="custom-select" style="width:500px; margin-top:20px; margin-bottom: 20px;">';
                                $query = "SELECT DISTINCT organization FROM users";
                                $result = mysqli_query($link, $query);

                                // Show the organizations as options for dropdown menu
                                echo '<h3 style="margin-left: 7px">Attendee Change Event:</h3>';
                                echo '<select name="change_event" class="form-control" style="margin-left: 7px; width: 30%" required>';
                                echo '<option>'."Select...".'</option>';
                                while ($row = mysqli_fetch_assoc($result))
                                {
                                    $organ = $row['organization'];
                                    //echo $organ;
                                    echo "<option>$organ</option>";
                                }
                                echo '</select>';

                                echo '</div>';

                            ?>
                            <div class="form-group">
                                <input type="submit" name="submit_org" class="btn btn-primary" value="Change Event" style="margin-left: 7px">
                            </div>
                        </form>
                    </div>
                    <?php
                            }

                    if (isset($_POST['submit_org']))
                    {
                        if ($_POST['change_event'] != "Select...")
                        {
                            $_SESSION['organization'] = $_POST['change_event'];
                        }
                    }
                    }
                            ?>

                    <?php if ($_SESSION['user_role'] == "Exhibitor" || $_SESSION['user_role'] == "Organizer") { ?>
                    <div class="wrapper" style="background-color: rgba(0, 0, 0, 0.05); padding: 5px;">
                        <form class="vote_form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <?php
                            // function to create array with table contents
                            function getCatData ($orgName, $eventName, $link)
                            {
                                //multi-dimensional array
                                $data = array();

                                $query = "SELECT * FROM categories WHERE organization='" . $orgName . "' AND event_name='" . $eventName . "'";
                                $result = mysqli_query($link, $query);
                                // for every category in the event
                                while ($row = $result->fetch_assoc())
                                {
                                    // create array with information about category to add to data array
                                    $catInfo = array();
                                    array_push($catInfo, $row['index_cat']);
                                    array_push($catInfo, $row['parent_id']);
                                    array_push($catInfo, $row['category']);
                                    array_push($catInfo, $row['has_children']);

                                    array_push($data, $catInfo);
                                }
                                return $data;
                            }

                            // recursive function to generate category and subcategory tree
                            function generateTree ($data, $depth, $parent_id)
                            {
                                if (empty($data))
                                {
                                    return "";
                                }
                                $tree = "";
                                for ($i=0, $ni=count($data); $i < $ni; $i++) {
                                    if ($data[$i][1] == $parent_id)
                                    {
                                        if ($data[$i][3] > 0)
                                        {
                                            if ($data[$i][1] == 0)
                                            {
                                                $tree .= '<option class="optionGroup" value="'.$data[$i][2]."?_?".$data[$i][0]."?_?".$data[$i][1].'">'.$data[$i][2].'</option>';
                                            }
                                            else
                                            {
                                                $tree .= '<option class="optionChild" value="'.$data[$i][2]."?_?".$data[$i][0]."?_?".$data[$i][1].'">' . '&nbsp;&nbsp;'.$data[$i][2] .'</option>';
                                            }
                                            //$tree .= '<optgroup label="'.$data[$i][2].'">';
                                            $tree .= generateTree($data, $depth+1, $data[$i][0]);
                                        }
                                        else
                                        {
                                            if ($data[$i][1] == 0)
                                            {
                                                $tree .= '<option class="optionGroup" value="'.$data[$i][2]."?_?".$data[$i][0]."?_?".$data[$i][1].'">'.$data[$i][2].'</option>';
                                            }
                                            else
                                            {
                                                $tree .= '<option class="optionChild" value="'.$data[$i][2]."?_?".$data[$i][0]."?_?".$data[$i][1].'">' . '&nbsp;&nbsp;'.$data[$i][2] .'</option>';
                                            }
                                        }
                                    }
                                }
                                return $tree;
                            }


                            //echo $_SESSION['division_name'];
                            $query = 'SELECT division_name FROM users WHERE user_id = ' . $_SESSION['user_id'];
                            $result = mysqli_query($link, $query);
                            $row = $result->fetch_assoc();

                            $divName = $row['division_name'];
                            if ($divName !== '') {
                                $data = getCatData ($_SESSION['organization'], $_SESSION['event_name'], $link);

                                $query = 'SELECT category FROM categories WHERE index_cat = ' . $divName;
                                $result = mysqli_query($link, $query);
                                $divName = $result->fetch_assoc()['category'];

                            } else {
                                $divName = "None";
                            }

                            echo '<h3 style="margin-left: 7px">Current Category: ' . $divName . '</h3>';
                            echo '<h4 style="margin-left: 7px">Change Category:</h4>';
                            echo '<select  name="subSelect" id="subSelect" class="form-control" style="margin-left: 7px; width: 20%" required>';
                            echo '<option value="">Select...</option>';
                            $tree = generateTree ($data, 0, 0);
                            echo $tree;
                            echo '</select>';

                            echo '<div class="form-group">';
                                echo '<input type="submit" name="submit_cat" class="btn btn-primary" value="Submit" style="margin: 7px">';
                            echo '</div>';

                            if (isset($_POST['submit_cat']))
                            {
                                if (isset($_POST['subSelect']))
                                {
                                    $value = explode("?_?", $_POST['subSelect']);
                                    $query = "UPDATE users SET division_name = '" . $value[1] . "' WHERE user_id = '" . $_SESSION["user_id"] . "' ";
                                    $result = mysqli_query($link, $query);

                                    echo "<h3 style='margin-left: 7px'>Category Updated</h3>";
                                }

                                header("location: .");
                            }

                            ?>
                        </form>
                    </div>

                    <?php } ?>

                    <style>
                        .grid-container {
                            display: grid;
                            grid-template-columns: 1fr 1fr 1fr;
                            grid-gap: 50px;
                            padding: 10px;
                        }

                        h1 {
                            border-radius: 7px;
                            background-color: rgba(0, 0, 0, 0.05);
                            text-align: center;
                            padding: 10px 0;
                        }

                        .grid-container > div {
                            transition: .5s ease;
                            background-color: white;
                            text-align: center;
                            padding: 20px 0;
                            font-size: 40px;
                            border-radius: 10px;
                            border: 1px solid black;
                            height: 200px;
                            overflow: auto;
                            cursor: pointer;
                            filter: grayscale(0%);
                            background-size: cover;
                            background-position: center;
                        }

                        .grid-container > div:hover {
                            box-shadow:
                                    1px 1px #373737,
                                    2px 2px #373737,
                                    3px 3px #373737,
                                    4px 4px #373737,
                                    5px 5px #373737,
                                    6px 6px #373737;
                            -webkit-transform: translateX(-3px);
                            transform: translateX(-3px);
                            transition: .5s ease;
                        }
                    </style>

                    <!--Construct the tiles for the different sections-->
                    <?php

                    $colors = array('#169EDB', '#F2670C', '#DB3600', '#DB7C0B', '#16DB3E',);
                    //$colors = array('linear-gradient(to right, #169EDB, #0CE6F2)', '#DB3600', '#16DB3E', '#DB7C0B');

                    $index = 0;

                    foreach ($items as $i => $item) {

                        if ($item[1]($_SESSION)) {
                            echo "<h1>$item[0]<br>";
                            echo "<div class='grid-container' id='grid'>";

                            foreach ($item[2] as $sub_item) {

                                if ($sub_item[2]($_SESSION)) {
                                    echo "<div name='sub_buttons' onclick=location.href='$sub_item[1]'; style='background-color: $colors[$index]; display: flex; justify-content: center; align-items: center;'>$sub_item[0]</div>";
                                }
                            }

                            $index++;

                            echo "</div>";
                            echo "</h1>";
                        }
                    }
                    ?>

                </div>
            </div>
            <!-- /.row -->

    </div>
        <!-- /.container-fluid -->
</div>
    <!-- /#page-wrapper -->
    <?php include "includes/user_footer.php"; ?>

<script>

    /*document.onload = init();

    function init() {
        var main_background = document.getElementById('wrapper').style.backgroundColor;
        window.alert(main_background);
        var buttons = document.body.getElementsByClassName('sub_buttons');
        window.alert(buttons.toString());
        for (var i = 0; i < buttons.length; i++) {
            window.alert(buttons[i].style.backgroundColor);
            //buttons[i].style.backgroundColor = ;
        }
    }*/

</script>