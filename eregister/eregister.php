<?php
// Include the db file
include "../db/config.php";
// Define variables
$username = $password = $confirmPassword = $firstName = $lastName = $organization = $email  = $event = $exhibitname = "";
$userNameErr = $passwordErr = $confirmPassword_err = $emailErr = $firstNameErr = $lastNameErr = $organizationErr = "";
$eventErr = $exhibitnameErr ="" ;


// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Validate username
    if(empty(trim($_POST["username"]))){
        $userNameErr = "Please enter a username.";
    } else{
        // Prepare a select statement
        $query = "SELECT id FROM exhibitors WHERE username = ?";

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
                echo "Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }


    //validate firstName, lastName, email and organizer, if its empty enter in the name
    if(empty(trim($_POST["firstName"])) || empty(trim($_POST["lastName"])) ||
        empty(trim($_POST["organization"])) || empty(($_POST["event"])) ||
        empty(($_POST["exhibitname"]))) {

        $firstNameErr = "Please enter first name.";
        $lastNameErr = "Please enter last name.";
        $organizationErr = "Please enter your organization that is hosting the event.";
        $emailErr = "Please enter your email.";
        $eventErr = "Please tell us what event you are signing up under.";
        $exhibitnameErr = "Please tell us the name of your exhibit.";
    }else{
        $firstName = trim($_POST["firstName"]);
        $lastName = trim($_POST["lastName"]);
        $organization = trim($_POST["organization"]);
        $email = trim($_POST["email"]);
        $event = $_POST["event"]; //Dont want to trim off whitespace, in case event has multiply names
        $exhibitname = $_POST["exhibitname"];
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
    //$hash = md5(rand(0,1000));

    // Check input errors before inserting in database its empty if its false
    if(empty($userNameErr) && empty($passwordErr) && empty($confirmPassword_err) &&
        empty($firstNameErr) && empty($lastNameErr) && empty($organizationErr) && empty($emailErr) && empty($eventErr)
        && empty($exhibitnameErr)){

        // Prepare an insert statement
        $query = "INSERT INTO exhibitors (username, password, firstname, lastname, organization, email, event, exhibitname) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        if($stmt = mysqli_prepare($link, $query)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ssssssss", $paramUserName, $paramPassword, $paramFirst,
                $paramLast, $paramOrganization, $paramEmail, $paramEvent, $paramExhibitname);

            // Set parameters
            $paramExhibitname = $exhibitname;
            $paramEvent = $event;
            //$paramHash = $hash;
            $paramEmail = $email;
            $paramFirst = $firstName;
            $paramLast = $lastName;
            $paramOrganization = $organization;
            $paramUserName = $username;
            $paramPassword = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash

            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                //Need better way to align the email. not important right now
                /*$to      = "expoexpressonline@gmail.com" ; // Send email to our user
                $subject = 'Verification'; // Give the email a subject
                $message = '
 
                            A new user is requesting to sign up to host an event.
                             
                            ------------------------
                            Username:    '.$username.'
                            First Name:   '.$firstName.'
                            Last Name:   '.$lastName.'
                            Organization: '.$organization.'
                            Event:          '.$event.'
                            ------------------------
                             
                            Please click this link to activate your account:
                            http://www.expoexpress.online/register/verify.php?email='.$email.'&hash='.$hash.'
                             
                            ';
                // Our message above including the link
                $headers = 'From:noreply@expoexpress.online' . "\r\n"; // Set from headers
                mail($to, $subject, $message, $headers); // Send our email */

                header("location: eregister.php");
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
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

        <!--organization-->
        <div class="form-group <?php echo (!empty($organizationErr)) ? 'has-error' : ''; ?>">
            <label>Organization</label>
            <input type="text" name="organization" class="form-control" value="<?php echo $organization; ?>">
            <span class="help-block"><?php echo $organizationErr; ?></span>
        </div>

        <!--event-->
        <div class="form-group <?php echo (!empty($eventErr)) ? 'has-error' : ''; ?>">
            <label>Event</label>
            <input type="text" name="event" class="form-control" value="<?php echo $event; ?>">
            <span class="help-block"><?php echo $eventErr; ?></span>
        </div>

        <!--exhibit name-->
        <div class="form-group <?php echo (!empty($exhibitnameErr)) ? 'has-error' : ''; ?>">
            <label>Exhibit Name</label>
            <input type="text" name="exhibitname" class="form-control" value="<?php echo $exhibitname; ?>">
            <span class="help-block"><?php echo $exhibitnameErr; ?></span>
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

        <!--Standby for confirmation-->

        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Submit">
            <input type="reset" class="btn btn-default" value="Reset">
        </div>


        <p>Already have an account? <a href="elogin.php">Login here</a>.</p>


    </form>

</div>
</body>
</html>