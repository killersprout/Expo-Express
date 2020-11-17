<?php
include "../db/config.php";
session_start();

// if the user isnt logged in, go back to login
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: https://www.expoexpress.online/register/login.php");
    exit;
}

$userID = $_SESSION["id"];
echo "ID of Current User: ".$userID;

$query = "SELECT organization FROM organizers WHERE id='" . $userID . "'";
$result = mysqli_query($link, $query);
$row = $result->fetch_assoc();
$orgName =  $row['organization'];
echo $orgName;

$successMessage = "";
$errors = [];
$valueArr = array();
$numQuestions = 0;

if (isset($_POST['submit']))
{
    if (!empty($_POST["question"]))
    {
        $query = "SELECT judging FROM voting_questions WHERE organization = ?";

        if($stmt = mysqli_prepare($link, $query))
        {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $paramOrg);

            // Set parameters
            $paramOrg = $orgName;

            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)) {
                mysqli_stmt_store_result($stmt);
                //if entry exists
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    //update questions
                    $query = "UPDATE voting_questions SET judging = ? WHERE organization = ?";

                    if ($stmt = mysqli_prepare($link, $query)) {
                        foreach ($_POST["question"] as $key => $value) {
                            array_push($valueArr, $value);
                        }

                        mysqli_stmt_bind_param($stmt, "ss", $paramJudging, $paramOrg);
                        $paramJudging = implode(', ', $valueArr);
                        $paramOrg = $orgName;

                        if (mysqli_stmt_execute($stmt)) {
                            $successMessage = "Updated Successfully.";
                        } else {
                            $errors[0] = "Error Occurred;";
                        }
                    } else {
                        echo "Could not Update";
                    }

                    // Close statement
                    mysqli_stmt_close($stmt);
                }
                else
                {
                    $query = "INSERT INTO voting_questions(organization, question, judging) VALUES (?, ?, ?);";

                    if ($stmt = mysqli_prepare($link, $query)) {
                        foreach ($_POST["question"] as $key => $value) {
                            array_push($valueArr, $value);
                        }
                        mysqli_stmt_bind_param($stmt, "sss", $paramOrg, $paramQuestion, $paramJudging);

                        $paramOrg = $orgName;
                        $paramQuestion = "";
                        $paramJudging = implode(', ', $valueArr);

                        if (mysqli_stmt_execute($stmt)) {
                            $successMessage = "Updated Successfully.";
                        } else {
                            $errors[1] = "Error Occurred;";
                        }
                    } else {
                        echo "Could not insert.";
                    }

                    // Close statement
                    mysqli_stmt_close($stmt);
                }
            }
            else
            {
                echo "Couldn't Execute";
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
    <title>Vote for your Favorite Project</title>
    <style type="text/css">
        <?php echo file_get_contents('style.css'); ?>
    </style>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</head>
<body>


<div class="wrapper">
    <h2 align="center">Add Questions for Judging</h2>
    <form name="add_question" id="add_question" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

        <div class="table-responsive">
            <table class="table table-bordered" id="dynamic_field">
                <tr>
                    <td><input type="text" name="question[]" placeholder="Enter Question / Category" class="form-control question_list" required="" /></td>
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
        var postURL = "/add_voting_questions.php";
        var i=1;


        $('#add').click(function(){
            i++;
            $('#dynamic_field').append('<tr id="row'+i+'" class="dynamic-added"><td><input type="text" name="question[]" placeholder="Enter Question / Category" class="form-control question_list" required /></td><td><button type="button" name="remove" id="'+i+'" class="btn btn-danger btn_remove">X</button></td></tr>');
        });


        $(document).on('click', '.btn_remove', function(){
            var button_id = $(this).attr("id");
            $('#row'+button_id+'').remove();
        });


        $('#submit').click(function(){
            $.ajax({
                url:postURL,
                method:"POST",
                data:$('#add_question').serialize(),
                type:'json',
                success:function(data)
                {
                    i=1;
                    $('.dynamic-added').remove();
                    $('#add_question')[0].reset();
                    alert('Record Inserted Successfully.');
                }
            });
        });


    });
</script>
</body>
</html>
