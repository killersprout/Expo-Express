<?php
// Include the db file
ob_start();
include "../db/config.php";
include "includes/navigation.php";
//Error handiling

//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

// Define variables
$username = $password = $confirmPassword = $firstName = $lastName = $organization = $email = "";
$role = $exhibit = $division =$event ="";
$userNameErr = $passwordErr = $confirmPassword_err = $emailErr = $firstNameErr = $lastNameErr = $organizationErr = "";
$eventErr = $headCount = $headCountErr = "";
$msg = "Standby while an administrator processes your request.";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Validate username
    if(empty(trim($_POST["username"]))){
        $userNameErr = "Please enter a username.";
    } else{
        // Prepare a select statement
        $query = "SELECT user_id FROM users WHERE username = ?";

        if($stmt = mysqli_prepare($link, $query)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $paramUserName);

            // Set parameters
            $paramUserName = trim($_POST["username"]);

            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){

                // store username
                mysqli_stmt_store_result($stmt);

                //If username exist then let them know
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $userNameErr = "This username is already taken.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                die( "Oops! Something went wrong. Please try again later." . mysqli_error($link));
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }


    //validate firstName, lastName, email and organizer, if its empty enter in the name
    if(empty(trim($_POST["firstName"])) || empty(trim($_POST["lastName"])) || empty(($_POST["event"]))) {

        $firstNameErr = "Please enter first name.";
        $lastNameErr = "Please enter last name.";
        $emailErr = "Please enter your email.";
        $eventErr = "Please tell us what event you want to host";
    }else{
        $firstName = trim($_POST["firstName"]);
        $lastName = trim($_POST["lastName"]);
        $email = trim($_POST["email"]);
        $event = $_POST["event"];
        $role = $_POST['role'];
        $organization = $_POST['organization'];
        $division = "";
        $exhibit = $_POST['exhibit'];
    }

    // Validate password
    if(empty(trim($_POST["password"]))){
        $passwordErr = "Please enter a password.";
    } elseif(strlen(trim($_POST["password"])) < 6){
        $passwordErr = "Password must have at least 6 characters.";
    } else{
        $password = trim($_POST["password"]);
    }

    // Validate confirm password
    if(empty(trim($_POST["confirmPassword"]))){
        $confirmPassword_err = "Please confirm password.";
    } else{
        $confirmPassword = trim($_POST["confirmPassword"]);
        if(empty($passwordErr) && ($password != $confirmPassword)){
            $confirmPassword_err = "Password did not match.";
        }
    }

    //Make a hash that will be used for the email verification link
    $hash = md5(rand(0,1000));

    // Check input errors before inserting in database its empty if its false
    if(empty($userNameErr) && empty($passwordErr) && empty($confirmPassword_err) &&
        empty($firstNameErr) && empty($lastNameErr) && empty($emailErr) && empty($eventErr)){

        // Prepare an insert statement
        $query = "INSERT INTO users (username, user_password, user_firstname, user_lastname, organization, user_email, hash, event_name, head_count,user_role,division_name,exhibit_name) ";
        $query .= "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?)";

        if($stmt = mysqli_prepare($link, $query)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssssssssisss", $paramUserName, $paramPassword, $paramFirst,
                $paramLast, $paramOrganization, $paramEmail,$paramHash, $paramEvent, $paramHeadCount,$paramRole,
                $paramDivision,$paramExhibit);

            // Set parameters
            $paramEvent = $event;
            $paramHash = $hash;
            $paramEmail = $email;
            $paramFirst = $firstName;
            $paramLast = $lastName;
            $paramOrganization = $organization;
            $paramUserName = $username;
            $paramHeadCount = $headCount;
            $paramRole = $role;
            $paramDivision = $division;
            $paramExhibit = $exhibit;
            $paramPassword = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash

            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                if($role == "Organizer") {
                    //Email to go in here if the query is execute
                    //Need better way to align the email. not important right now
                    $to = "expoexpressonline@gmail.com"; // Send email to our user
                    $subject = 'Verification'; // Give the email a subject
                    $message = '
 
                            A new user is requesting to sign up to host an event.
                             
                            -----------------------------------------
                            Username:            ' . $username . '
                            First Name:           ' . $firstName . '
                            Last Name:           ' . $lastName . '
                            Organization:        ' . $organization . '
                            Event:                    ' . $event . '
                            -----------------------------------------
                             
                            Please click this link to activate your account:
                            http://www.expoexpress.online/register/verify.php?email=' . $email . '&hash=' . $hash . '
                             
                            ';
                    // Our message above including the link
                    $headers = 'From:noreply@expoexpress.online' . "\r\n"; // Set from headers
                    mail($to, $subject, $message, $headers); // Send our email
                }
            } else{
                die( "Oops! Something went wrong. Please try again later." . mysqli_error($link));
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // Close connection**moved to bottom of page
    //mysqli_close($link);
}
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="">

        <title>Register</title>

        <!-- Bootstrap Core CSS -->
        <link href="css/bootstrap.min.css" rel="stylesheet">

        <!-- Custom CSS -->
        <link href="css/blog-home.css" rel="stylesheet">

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
    <style>
        .content {
            max-width: 500px;
            margin: auto;
        }
        body {
            text-align: center;
        }

        #page-wrap {
            text-align: left;
            width: 300px;
            margin: 0 auto;
        }
    </style>
    </head>

<body>


<h2>Sign Up</h2>
    <p>Please fill this form to create an account.</p>

<div class="wrapper" id="page-wrap">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">

            <table>
            <td>
                <!--first Name-->
                <div  class="form-group <?php echo (!empty($firstNameErr)) ? 'has-error' : ''; ?>">
                    <label for="title" >First Name</label>
                    <input type="text" name="firstName" class="form-control" value="<?php echo $firstName; ?>">
                    <span class="help-block"><?php echo $firstNameErr; ?></span>
                </div>
            </td>

            <td>
                <!--Last Name-->
                <div class="form-group <?php echo (!empty($lastNameErr)) ? 'has-error' : ''; ?>">
                    <label>Last Name</label>
                    <input type="text" name="lastName" class="form-control" value="<?php echo $lastName; ?>">
                    <span class="help-block"><?php echo $lastNameErr; ?></span>
                </div>
            </td>
        </table>

            <table>

            <td>  <!--event
                <div class="form-group <?php echo (!empty($eventErr)) ? 'has-error' : ''; ?>">
                    <label>Event</label>
                    <input type="text" name="event" class="form-control" value="<?php echo $event; ?>">
                    <span class="help-block"><?php echo $eventErr; ?></span>
                </div> -->
            </td>


            </table>

            <table>
            <td><!--email-->
                <div class="form-group <?php echo (!empty($emailErr)) ? 'has-error' : ''; ?>">
                    <label>Email</label>
                    <input type="text" name="email" class="form-control" value="<?php echo $email; ?>">
                    <span class="help-block"><?php echo $emailErr; ?></span>
                </div>
            </td>

            <td><!--Username-->
                <div class="form-group <?php echo (!empty($userNameErr)) ? 'has-error' : ''; ?>">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
                    <span class="help-block"><?php echo $userNameErr; ?></span>
                </div>
            </td>
            </table>


            <table>
            <td> <!--password-->
                <div class="form-group <?php echo (!empty($passwordErr)) ? 'has-error' : ''; ?>">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
                    <span class="help-block"><?php echo $passwordErr; ?></span>
                </div>
            </td>

            <td><!--password Confirmation-->
                <div class="form-group <?php echo (!empty($confirmPassword_err)) ? 'has-error' : ''; ?>">
                    <label>Confirm</label>
                    <input type="password" name="confirmPassword" class="form-control" value="<?php echo $confirmPassword; ?>">
                    <span class="help-block"><?php echo $confirmPassword_err; ?></span>
                </div>
            </td>

        </table>

            <table>
                <!--
                <td>
                    <div class="form-group">
                        <label>Division</label>
                        <input type="text" name="division" class="form-control" value="<?php // echo $division; ?>">
                        <span class=""></span>
                    </div>
                </td>
                -->
                <td> <!--Exhibit Name-->
                    <div class="form-group">
                        <label>Exhibit name if applicable</label>
                        <input type="text" name="exhibit" class="form-control" value="<?php echo $exhibit; ?>">
                        <span class=""></span>
                    </div>
                </td>
            </table>

        <div class="form-group">
            <label for="role">Select Role</label><br>
            <select name="role" id="role">
                <option value="Attendee">Select Options</option>
                <option value="Organizer">Organizer</option>
                <option value="Judge">Judge</option>
                <option value="Attendee">Attendee</option>
                <option value="Exhibitor">Exhibitor</option>
            </select>
        </div>



                <?php

                echo '<div class="form-group">';
                echo '<label for="organization">Organization</label><br>';
                $query = "SELECT DISTINCT organization FROM users";
                $result = mysqli_query($link, $query);

                // Show the organizations as options for dropdown menu
                echo '<select name="organization" id="organization">';
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

            <?php
            echo '<div class="form-group">';
                echo '<label for="Event">Event</label><br>';
                $query = "SELECT DISTINCT event_name FROM users";
                $result = mysqli_query($link, $query);

                // Show the organizations as options for dropdown menu
                echo '<select name="event" id="event">';
                    echo '<option>'."Select...".'</option>';
                    while ($row = mysqli_fetch_assoc($result))
                    {
                    $organ = $row['event_name'];
                    //echo $organ;
                    echo "<option>$organ</option>";
                    }
                    echo '</select>';

                echo '</div>';

            ?>



<?php
/*
if (isset($_POST['submit_org']))
{
    if ($_POST['change_event'] != "Select...")
    {
        $_SESSION['organization'] = $_POST['change_event'];
    }
}
*/
?>


        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Submit">
            <input type="reset" class="btn btn-default" value="Reset">
        </div>

        <p>Already have an account? <a href="index.php">Login here</a>.</p>
        <p>Made a mistake?<br> Email us @ expoexpressonline@gmail.com</p>

    </form>
</div>
    </body>
<?php include "includes/footer.php";
mysqli_close($link);
?>

