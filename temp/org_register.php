<?php
// Include config file
include "../db/config.php";
// Define variables and initialize with empty values
$orgName = $username = $password = $userEmail = $confirm_password = "";
$orgName_err = $username_err = $password_err = $confirm_password_err = $email_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    $orgName = filter_var($_POST["orgName"], FILTER_SANITIZE_STRING);
    $userEmail = filter_var($_POST["userEmail"], FILTER_SANITIZE_STRING);
    $username = filter_var($_POST["username"], FILTER_SANITIZE_STRING);
    $password = filter_var($_POST["password"], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST["userEmail"], FILTER_SANITIZE_STRING);

    $errorMessage = array();
    $valid = true;

    if (empty($_POST["username"])){
        $errorMessage[] = "Please enter a username";
    }
    /*
    if(empty(($_POST["username"]))){
        $username_err = "Please enter a username.";
    } else{
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE username = ?";


        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);

            // Set parameters
            $param_username = trim($_POST["username"]);

            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // store result
                mysqli_stmt_store_result($stmt);

                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "This username is already taken.";
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

    // Validate password
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password must have atleast 6 characters.";
    } else{
        $password = trim($_POST["password"]);
    }

    // Validate confirm password
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm password.";
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }

    // Check input errors before inserting in database
    if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){

        // Prepare an insert statement
        $sql = "INSERT INTO users (username, password) VALUES (?, ?)";

        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "ss", $param_username, $param_password);

            // Set parameters
            $param_username = $username;
            $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash

            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Redirect to login page
                header("location: login.php");
            } else{
                echo "Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // Close connection
    mysqli_close($link);
    */

}

?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Organization Registration</title>
    <link rel="stylesheet" type="text/css" href="/css/register.css" />
</head>
<body>
<form id='orgRegister' action='org_register.php' method='post'
      accept-charset='UTF-8'>
    <div class="container">
        <h1>Organization Registration</h1>
        <p>Please fill in this form to start creating events.</p>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <hr>

            </div>
            <?php
            if (! empty($errorMessage) && is_array($errorMessage)) {
                ?>
                <div class="alert ">
                    <strong>
                    <?php
                    foreach($errorMessage as $message) {
                        echo $message . "<br/>";
                    }
                    ?>
                    </strong>
                </div>
                <br>
                <?php
            }
            ?>
            <div class="form-group <?php echo (!empty($orgName_err)) ? 'has-error' : ''; ?>">
                <label>Organization Name</label>
                <input type="text" name="orgName" class="form-control" value="<?php echo $orgName; ?>">
                <span class="help-block"><?php echo $orgName_err; ?></span>
            </div>

            <div class="form-group <?php echo (!empty($email_err)) ? 'has-error' : ''; ?>">
                <label>Email Address</label>
                <input type="text" name="email" class="form-control" value="<?php echo $userEmail; ?>">
                <span class="help-block"><?php echo $email_err; ?></span>
            </div>

            <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
                <label>Username</label>
                <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
                <span class="help-block"><?php echo $username_err; ?></span>
            </div>

            <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
                <label>Password</label>
                <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">
                <span class="help-block"><?php echo $password_err; ?></span>
            </div>

            <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>">
                <span class="help-block"><?php echo $confirm_password_err; ?></span>
            </div>

            <hr>
    </div>

    <div class="form-group">
        <input type="submit" class="btn btn-primary" value="Register">
        <input type="reset" class="btn btn-default" value="Reset">
    </div>

    <p>Already have an account? <a href="login.php">Login here</a>.</p>
</form>
</body>
</html>