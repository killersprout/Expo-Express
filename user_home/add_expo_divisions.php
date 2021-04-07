<?php
ob_start();
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "includes/user_header.php";
// if the user isn't logged in, go back to login
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: ./register");
    exit;
}

$userid = $_SESSION["user_id"];
$role = $_SESSION['user_role'];

?>

<style>
    /* Customize the label (the container) */
    .container {
        position: relative;
        padding-left: 35px;
        margin-left: 20px;
        margin-bottom: 20px;
        cursor: pointer;
        font-size: 18px;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        width:30em;
        column-count: auto;
    }

    /* Hide the browser's default checkbox */
    .container input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        height: 0;
        width: 0;
    }

    /* Create a custom checkbox */
    .checkmark {
        position: absolute;
        top: 0;
        left: 0;
        height: 25px;
        width: 25px;
        background-color: #eee;
    }

    /* On mouse-over, add a grey background color */
    .container:hover input ~ .checkmark {
        background-color: #ccc;
    }

    /* When the checkbox is checked, add a blue background */
    .container input:checked ~ .checkmark {
        background-color: #031828;
    }

    /* Create the checkmark/indicator (hidden when not checked) */
    .checkmark:after {
        content: "";
        position: absolute;
        display: none;
    }

    /* Show the checkmark when checked */
    .container input:checked ~ .checkmark:after {
        display: block;
    }

    /* Style the checkmark/indicator */
    .container .checkmark:after {
        left: 9px;
        top: 5px;
        width: 5px;
        height: 10px;
        border: solid white;
        border-width: 0 3px 3px 0;
        -webkit-transform: rotate(45deg);
        -ms-transform: rotate(45deg);
        transform: rotate(45deg);
    }

    h1, h2, h3, h4, h5, h6 {font-family:Tahoma, sans-serif;}
    body {font-family:Tahoma, sans-serif;}

    .button {
        border-radius: 4px;
        height:35px;
        width:100px;
        background-color: #545456;
        color: white;
        border: none;
        box-shadow: none;
        font-size: 16px;
        margin-left: 20px;
    }

    .button2 {
        border-radius: 4px;
        height:25px;
        width:50px;
        background-color: #545456;
        color: white;
        border: none;
        box-shadow: none;
        font-size: 13px;
        margin-left: 10px;
    }

    p {
        margin-left: 20px;
    }
</style>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<!--
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
-->
<div id="wrapper">

    <!-- Navigation -->
    <?php include "includes/user_navigation.php"; ?>


    <div id="page-wrapper">

        <!-- Page Heading -->
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">
                    Welcome <?php echo " " . $role . ", " .$_SESSION['user_firstname'] . " " .$_SESSION['user_lastname'];?>
                </h1>

                <?php
                // Get event and organization name using user id
                $query = "SELECT * FROM users WHERE user_id='" . $userid . "'";
                $result = mysqli_query($link, $query);
                $row = $result->fetch_assoc();
                $eventName = $row["event_name"];
                $orgName = $row["organization"];
                $errors = "";
                $successMessage = "";
                $tree = "";

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
                    $tree = '<ul>';
                    for ($i=0, $ni=count($data); $i < $ni; $i++) {
                        if ($data[$i][1] == $parent_id)
                        {
                            $tree .= '<li style="list-style-type: none;">';
                            $tree .= '<label class="container" style="font-weight:normal !important;">';
                            $tree .= '<input type="checkbox" name="remove_div[]" value="'.$data[$i][2]."?_?".$data[$i][0]."?_?".$data[$i][1].'" />'.$data[$i][2];
                            $tree .= '<span class="checkmark"></span></label>';
                            if ($data[$i][1] == 0)
                            {
                                $tree .= '<span id="myform"><button class="button2" type="submit" value="'.$data[$i][2]."?_?".$data[$i][0]."?_?".$data[$i][1].'">Add</button></span>';

                            }

                            $tree .= generateTree($data, $depth+1, $data[$i][0]);
                            $tree .= '</li>';
                        }
                    }
                    $tree .= '</ul>';
                    return $tree;
                }

                if (isset($_POST['add_cat']))
                {
                    // Adding Category without a Parent

                    // the inputted name of category is not empty
                    if (!empty($_POST["division"])) {
                        // for each division name - insert into table
                        foreach ($_POST["division"] as $key => $value) {
                            // prepare statement
                            $query = "INSERT INTO categories(organization, event_name, parent_id, category) VALUES (?, ?, ?, ?);";
                            if ($stmt = mysqli_prepare($link, $query)) {
                                mysqli_stmt_bind_param($stmt, "ssis", $paramOrg, $paramEvent, $paramPI, $paramCat);

                                $paramOrg = $orgName;
                                $paramEvent = $eventName;
                                $paramPI = 0;
                                $paramCat = $value;

                                if (mysqli_stmt_execute($stmt)) {
                                    $successMessage = "Updated Successfully.";
                                } else {
                                    $errors = "Error Occurred;";
                                }
                            } else {
                                echo "Could not insert.";
                            }

                            // Close statement
                            mysqli_stmt_close($stmt);
                        }
                    }
                }
                if (isset($_POST['submit_sub_cat']))
                {
                    if (!empty($_POST["sub_cat"])) {
                        // for each division name - insert into table
                        $dataArr = $_POST['data'];
                        foreach ($_POST["sub_cat"] as $key => $value) {
                            $child = $value;
                            $ret = explode(',', $dataArr[$key]);
                            $parent = trim($ret[0]);
                            $parent_id = (int)trim($ret[1]);

                            //echo "Parent: " . $parent . ", Child: " . $child . ", Level: " . $parent_id . "\n";

                            // Insert into table using the parent_id
                            $query = "INSERT INTO categories(organization, event_name, parent_id, category) VALUES (?, ?, ?, ?);";
                            if ($stmt = mysqli_prepare($link, $query)) {
                                mysqli_stmt_bind_param($stmt, "ssis", $paramOrg, $paramEvent, $paramPI, $paramCat);

                                $paramOrg = $orgName;
                                $paramEvent = $eventName;
                                $paramPI = $parent_id;
                                $paramCat = $child;

                                if (mysqli_stmt_execute($stmt)) {
                                    $successMessage = "Updated Successfully.";
                                } else {
                                    $errors = "Error Occurred;";
                                }
                            } else {
                                echo "Could not insert.";
                            }

                            // Close statement
                            mysqli_stmt_close($stmt);

                            $query = "UPDATE categories SET has_children=has_children+1 WHERE organization='".$orgName."' AND event_name='".$eventName."' AND index_cat='".$parent_id."'";
                            $result = mysqli_query($link, $query);
                        }
                    }
                }
                if (isset($_POST["remove"]))
                {
                    // for each selected division, remove from based on index of child
                    if (!empty($_POST["remove_div"])) {
                        echo '<p>Deleted ';
                        $end = 0;
                        foreach ($_POST["remove_div"] as $key => $value) {
                            $value = explode('?_?', $value);
                            // inform user which division deleted
                            echo $value[0];
                            // for the comma
                            $end++;
                            if ($end != count($_POST["remove_div"]))
                            {
                                echo ", ";
                            }
                            $child_id = $value[1];

                            $query = "DELETE FROM categories WHERE organization='".$orgName."' AND event_name='".$eventName."' AND index_cat='".$child_id."'";
                            $result = mysqli_query($link, $query);

                            $query = "UPDATE categories SET has_children=has_children-1 WHERE organization='".$orgName."' AND event_name='".$eventName."' AND index_cat='".$value[2]."'";
                            $result = mysqli_query($link, $query);
                        }
                        echo '</p>';
                    }
                }

                ?>

                <div class="wrapper">
                    <!-- Textbox for Adding Subcategories -->
                    <form align='center' name="add_sub_cat" id="add_sub_cat" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <p>Add Sub-Categories</p>
                        <div class="table-responsive" >
                            <table class="table table-bordered" id="sub_cat field">
                                <div id="field_div"></div>
                            </table>
                        </div>
                        <div class="form-group">
                            <input type="submit" name="submit_sub_cat" class="button" value="SUBMIT">
                            <input type="submit" id="cancel_sub_cat" class="button" value="CANCEL">
                        </div>
                    </form>

                    <form name="remove_division" id="remove_division" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <?php
                        echo '<h3 align="left" style="margin-left:20px;"> Categories in '.$eventName.' :</h3>';

                        // get data from table into array
                        $data = getCatData ($orgName, $eventName, $link);
                        // create tree
                        $depth = 0;
                        $parent_id = 0;
                        $tree = generateTree ($data, $depth, $parent_id);

                        if (!empty($tree))
                        {
                            echo '<br>';
                            echo $tree;
                            echo '<br>';
                        }
                        else
                        {
                            echo '<p>No Divisions Added</p>';
                        }
                        ?>
                        <div class="form-group">
                            <input type="submit" name="remove" class="button" value="REMOVE">
                        </div>
                    </form>

                    <form align='center' name="add_division" id="add_division" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <h3 align="left" style="margin-left:20px;">Add Categories </h3>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dynamic_field">
                                <tr>
                                    <td><input type="text" name="division[]" placeholder="Enter Category Name" class="form-control division_list" required="" /></td>
                                    <td><button type="button" name="add" id="add" class="btn btn-success">Add Another</button></td>
                                </tr>
                            </table>
                        </div>

                        <div class="form-group">
                            <input type="submit" name="add_cat" class="button" value="ADD">
                        </div>

                    </form>
                </div>

                <script type="text/javascript">

                    $(document).ready(function(){
                        var postURL = "/add_expo_divisions.php";
                        var i=1;
                        var j = 1;

                        $('#add').click(function(){
                            i++;
                            $('#dynamic_field').append('<tr id="row'+i+'" class="dynamic-added"><td><input type="text" name="division[]" placeholder="Enter New Division" class="form-control division_list" required /></td><td><button type="button" name="remove" id="'+i+'" class="btn btn-danger btn_remove">X</button></td></tr>');
                        });


                        $(document).on('click', '.btn_remove', function(){
                            var button_id = $(this).attr("id");
                            $('#row'+button_id+'').remove();
                        });


                        $("#myform button").click(function (ev) {
                            ev.preventDefault();
                            j = 1;
                            var text = $(this).attr("value").split("?_?");
                            var div = text[0];
                            // find the 'test' input element and set its value to the above variable
                            if ($('#field_div').is(':empty')){
                                $('#field_div').append('<p>Enter Name of Sub-category: '+div+'</p><tr id="sub_row'+j+'" ><td><input type="hidden" id=data name="data[]" value="'+text+'" ><input type="text" name="sub_cat[]" placeholder="Sub-Category Name" class="form-control division_list"  required /></td><td><button type="button" class="btn btn-success" name="submit" id="add_sub" >Add Another</button></td></tr>');
                            }else{
                                $('#field_div').empty();
                                $('#field_div').append('<p>Enter Name of Sub-category: '+div+'</p><tr id="sub_row'+j+'" ><td><input type="hidden" id=data name="data[]" value="'+text+'" ><input type="text" name="sub_cat[]" placeholder="Sub-Category Name" class="form-control division_list" required /></td><td><button type="button" class="btn btn-success" name="add_sub" id="add_sub" >Add Another</button></td></tr>');
                            }
                        });

                        $(document).on('click', '#add_sub', function(){
                            j++;
                            var text = $("#field_div #data").attr("value");
                            //alert(text);
                            var div = text[0];
                            $('#field_div').append('<tr id="sub_row'+j+'" ><td><input type="hidden" name="data[]" value="'+text+'" ><input type="text" name="sub_cat[]" placeholder="Sub-Category Name" class="form-control division_list" required/></td><td><input type="button" class="removeButton" value="X" id="'+j+'"></td></tr>');
                        });

                        $(document).on('click', '.removeButton', function(){
                            var button_id = $(this).attr("id");
                            $('#sub_row'+button_id+'').remove();
                        });

                        $('#cancel_sub_cat').click(function(){
                            $('#field_div').empty();
                        });

                    });
                </script>

                </body>
                </html>

            </div>

        </div>
        <!-- /.row -->

    </div>
    <!-- /.container-fluid -->
</div>
<!-- /#page-wrapper -->
<?php include "includes/user_footer.php"; ?>
