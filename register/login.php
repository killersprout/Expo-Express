<?php
// Initialize the session
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// if they are already logged in
//if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
 //   header("location: ../user_home");
 //   exit;
//}
//default
require_once "../db/config.php";


// Define variables and initialize with empty values
$username = $password = $session_err ="";
$username_err = $password_err = $activeOrNotErr = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){

    // Check if username is empty
    if (empty(trim($_POST["username"]))) {
        $username_err = "Please enter username.";
    } else {
        $username = trim($_POST["username"]);
    }

    // Check if password is empty
    if (empty(trim($_POST["password"]))) {
        $password_err = "Please enter your password.";
    } else {
        $password = trim($_POST["password"]);
    }


    // Validate credentials
    if (empty($username_err) && empty($password_err)) {
        // Prepare a select statement
        //New query so we get the information that we need. Query, looking for the username that was inputted
        $sql = "SELECT user_id, username,user_password,active,user_firstname,user_lastname,organization,event_name,head_count,user_role,exhibit_name,division_name FROM users WHERE username = ?";
        if ($stmt = mysqli_prepare($link, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "s", $param_username);

            // Set parameters
            $param_username = $username;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Store result
                mysqli_stmt_store_result($stmt);

                // Check if username exists, if yes then verify password and active status
                if (mysqli_stmt_num_rows($stmt) == 1) {
                    // Bind result variables

                    //New result statemnt
                    mysqli_stmt_bind_result($stmt, $id,$username,$hashed_password,$active,$firstname,$lastname,$organization,$event,$headcount,$role,$exhibit,$division);
                    if (mysqli_stmt_fetch($stmt)) {
                        if (password_verify($password, $hashed_password)) {
                            // Password is correct
                            $_SESSION["active"] = $active; //Set active variable
                            if($_SESSION["active"] == 1) { //If they are active, then they can sign in

                                // Store data in session variables
                                $_SESSION["loggedin"] = true; //creates variable loggedin for current session
                                $_SESSION["user_id"] = $id; //getting from db
                                $_SESSION["username"] = $username; //getting from db
                                $_SESSION["user_firstname"] = $firstname;
                                $_SESSION["user_lastname"] = $lastname;
                                $_SESSION["event_name"] = $event;
                                $_SESSION["organization"] = $organization;
                                $_SESSION["head_count"] = $headcount;
                                $_SESSION["division_name"] = $division;
                                $_SESSION["user_role"] = $role;
                                $_SESSION["exhibit_name"] = $exhibit;
                                // You can't have text displaying before this for some reason
                                // Redirect user to welcome page

                                header("Location: ../user_home");
                            }else if($_SESSION["active"] == 0){
                                echo "<h2>You have not been approved yet</h2>"; //Error if their status is 0
                            }
                        } else {
                            // Display an error message if password is not valid
                            $password_err = "The password you entered was not valid.";
                        }
                    }
                } else {
                    // Display an error message if username doesn't exist
                    $username_err = "No account found with that username.";
                }
            } else {
                die("ERROR " . mysqli_error($link) );
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }
    if (!empty($username_err) || !empty($password_err))
    {
        header("Location: https://www.expoexpress.online/register/index.php");
    }
    // Close connection
    mysqli_close($link);
}

/*
 <!--
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Organizer Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        <?php //echo file_get_contents('../css/style.css'); ?>
    </style>
</head>
<body>
<div class="wrapper">
    <h2>Login</h2>
    <p>Please fill in your credentials to login.</p>
    <form action="<?php //echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

        <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">
            <label>Username</label>
            <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
            <span class="help-block"><?php echo $username_err; ?></span>
        </div>

        <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">
            <label>Password</label>
            <input type="password" name="password" class="form-control">
            <span class="help-block"><?php echo $password_err; ?></span>
        </div>

        <div class="form-group">
            <input type="submit" class="btn btn-primary" value="Login">
            <input type="reset" class="btn btn-default" value="Reset">
        </div>

        <p>Don't have an account? <a href="register.php">Sign up now</a>.</p>
        <p>Return to the home page. <a href="https://www.expoexpress.online/register">Home</a>.</p>

    </form>
</div>
</body>
</html>
-->
 */
?>
