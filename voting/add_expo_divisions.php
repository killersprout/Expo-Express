<?php
include "../db/config.php";

$paramId = 0;
$successMessage = "";
$errors = [];
$valueArr = array();
$numQuestions = 0;

if (isset($_POST['submit']))
{
    if (!empty($_POST["division"])) {
        $id = 0;

        $query = "INSERT INTO divisions(divisionname, exhibits) VALUES (?, ?);";
        foreach ($_POST["division"] as $key => $value) {
            if ($stmt = mysqli_prepare($link, $query)) {
                mysqli_stmt_bind_param($stmt, "ss", $paramName, $paramExhibit);

                $paramName = $value;
                $paramExhibit = "";

                if (mysqli_stmt_execute($stmt)) {
                    $successMessage = "Updated Successfully.";
                } else {
                    $errors[$key] = "Error Occurred;";
                }
            } else {
                echo "Could not insert.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }

    }

}

?>
<!doctype html>
<html lang="en">
<head>
    <title>Add Divisions</title>
    <style type="text/css">
        <?php echo file_get_contents('style.css'); ?>
    </style>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</head>
<body>


<div class="wrapper">
    <h2 align="center">Add Divisions in Exposition</h2>
    <form name="add_division" id="add_division" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

        <div class="table-responsive">
            <table class="table table-bordered" id="dynamic_field">
                <tr>
                    <td><input type="text" name="division[]" placeholder="Enter Division" class="form-control division_list" required="" /></td>
                    <td><button type="button" name="add" id="add" class="btn btn-success">Add Another</button></td>
                </tr>
            </table>
        </div>

        <div class="form-group">
            <input type="submit" name="submit" class="btn btn-primary" value="Submit">
        </div>

    </form>
</div>


<script type="text/javascript">
    $(document).ready(function(){
        var postURL = "/add_expo_divisions.php";
        var i=1;


        $('#add').click(function(){
            i++;
            $('#dynamic_field').append('<tr id="row'+i+'" class="dynamic-added"><td><input type="text" name="division[]" placeholder="Enter New Division" class="form-control division_list" required /></td><td><button type="button" name="remove" id="'+i+'" class="btn btn-danger btn_remove">X</button></td></tr>');
        });


        $(document).on('click', '.btn_remove', function(){
            var button_id = $(this).attr("id");
            $('#row'+button_id+'').remove();
        });


        $('#submit').click(function(){
            $.ajax({
                url:postURL,
                method:"POST",
                data:$('#add_division').serialize(),
                type:'json',
                success:function(data)
                {
                    i=1;
                    $('.dynamic-added').remove();
                    $('#add_division')[0].reset();
                    alert('Record Inserted Successfully.');
                }
            });
        });


    });
</script>
</body>
</html>
