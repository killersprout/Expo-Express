<?php
// Include the db file
include "../db/config.php";
#include "..functions/functions.php"; //require once broke the page
// Define variables
$username = $password = $confirmPassword = $firstName = $lastName = $organization = $email  = $event = "";
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
        $query = "SELECT id FROM community WHERE username = ?";

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
                echo "Oops! Something went wrong. Please try again later.";
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
        $event = $_POST["event"]; //Dont want to trim off whitespace, in case event has multiply names
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
        empty($firstNameErr) && empty($lastNameErr) && empty($organizationErr) && empty($emailErr) && empty($eventErr)){

        // Prepare an insert statement
        $query = "INSERT INTO community (username, password, firstname, lastname, email, event) VALUES (?, ?, ?, ?, ?, ?)";

        if($stmt = mysqli_prepare($link, $query)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssssss", $paramUserName, $paramPassword, $paramFirst, $paramLast, $paramEmail, $paramEvent);

            // Set parameters
            $paramEvent = $event;
            $paramEmail = $email;
            $paramFirst = $firstName;
            $paramLast = $lastName;
            $paramUserName = $username;

            $paramPassword = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash

            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                //Email to go in here if the query is execute
                //emailVerify($username,$firstName,$lastName,$organization,$hash,$email); //caused error 404
                //Need better way to align the email. not important right now
                /*
                $to      = "expoexpressonline@gmail.com" ; // Send email to our user
                $subject = 'Verification'; // Give the email a subject
                $message = '
 
                            A new user is requesting to sign up to host an event.
                             
                            -----------------------------------------
                            Username:            '.$username.'
                            First Name:          '.$firstName.'
                            Last Name:           '.$lastName.'
                            Organization:        '.$organization.'
                            Event:               '.$event.'
                            Number of exhibits:  '.$headCount.'
                            -----------------------------------------
                             
                            Please click this link to activate your account:
                            http://www.expoexpress.online/register/verify.php?email='.$email.'&hash='.$hash.'
                             
                            ';
                // Our message above including the link
                $headers = 'From:noreply@expoexpress.online' . "\r\n"; // Set from headers
                mail($to, $subject, $message, $headers); // Send our email
                */
                //I want to redirect to the main page with a message that says we'll be in touch shortly
                //Leaving on register page for easibility in testing
                header("location: nlogin.php");
            } else{
                echo "Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // Close connection
    mysqli_close($link);
}
?>

<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="UTF-8">
    <title>Attendee Sign Up</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
<div class="wrapper">
    <h2>Sign Up</h2>
    <p>Please fill this form to create an account.</p>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

        <!--first Name-->
        <div class="form-group <?php echo (!empty($firstNameErr)) ? 'has-error' : ''; ?>">
            <label>First Name</label>
            <input type="text" name="firstName" class="form-control" value="<?php echo $firstName; ?>">
            <span class="help-block"><?php echo $firstNameErr; ?></span>
        </div>

        <!--Last Name-->
        <div class="form-group <?php echo (!empty($lastNameErr)) ? 'has-error' : ''; ?>">
            <label>Last Name</label>
            <input type="text" name="lastName" class="form-control" value="<?php echo $lastName; ?>">
            <span class="help-block"><?php echo $lastNameErr; ?></span>
        </div>


        <!--event-->
        <div class="form-group <?php echo (!empty($eventErr)) ? 'has-error' : ''; ?>">
            <label>Event</label>
            <input type="text" name="event" class="form-control" value="<?php echo $event; ?>">
            <span class="help-block"><?php echo $eventErr; ?></span>
        </div>

        <!--email-->
        <div class="form-group <?php echo (!empty($emailErr)) ? 'has-error' : ''; ?>">
            <label>Email</label>
            <input type="text" name="email" class="form-control" value="<?php echo $email; ?>">
            <span class="help-block"><?php echo $emailErr; ?></span>
        </div>

        <!--Username-->
        <div class="form-group <?php echo (!empty($userNameErr)) ? 'has-error' : ''; ?>">
            <label>Username</label>
            <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
            <span class="help-block"><?php echo $userNameErr; ?></span>
        </div>


        <!--password-->
        <div class="form-group <?php echo (!empty($passwordErr)) ? 'has-error' : ''; ?>">
            <label>Password</label>
            <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
            <span class="help-block"><?php echo $passwordErr; ?></span>
        </div>

        <!--password Confirmation-->
        <div class="form-group <?php echo (!empty($confirmPassword_err)) ? 'has-error' : ''; ?>">
            <label>Confirm Password</label>
            <input type="password" name="confirmPassword" class="form-control" value="<?php echo $confirmPassword; ?>">
            <span class="help-block"><?php echo $confirmPassword_err; ?></span>
        </div>


        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Submit">
            <input type="reset" class="btn btn-default" value="Reset">
        </div>


        <p>Already have an account? <a href="nlogin.php">Login here</a>.</p>


    </form>

</div>
</body>
</html>