<?php

// Initialize the session
session_start();

// if they are already logged in
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: ewelcome.php");
    exit;
    //Whatever you put in the header if where you're going to be redirected too
}
//default
require_once "../db/config.php";


// Define variables and initialize with empty values
$username = $password = $session_err ="";
$username_err = $password_err = "";

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

    //Login broke when i was adding more to the sql statement.

    // Validate credentials
    if (empty($username_err) && empty($password_err)) {
        // Prepare a select statement
        //New query so we get the information that we need
        $sql = "SELECT id, username, firstname, lastname, password, exhibitname, organization, event FROM exhibitors WHERE username = ?"; //Query, looking for the username that was inputted

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

                    //mysqli_stmt_bind_result($stmt, $id, $username, $hashed_password);

                    //New result statemnt
                    mysqli_stmt_bind_result($stmt, $id, $username, $firstname, $lastname,$hashed_password,
                        $exhibitname, $organization, $event);

                    if (mysqli_stmt_fetch($stmt)) {
                        if (password_verify($password, $hashed_password)) {
                            // Password is correct
                            session_start();
                            // Store data in session variables
                            $_SESSION["loggedin"] = true; //creates variable loggedin for current session
                            $_SESSION["id"] = $id; //getting from db
                            $_SESSION["username"] = $username; //getting from db
                            $_SESSION["firstname"] = $firstname;
                            $_SESSION["lastname"] = $lastname;

                            $_SESSION["exhibitname"] = $exhibitname; //exhibit
                            $_SESSION["organization"] = $organization; //organization
                            $_SESSION["event"] = $event; //event
                            // You cant have text displaying before this for some reason
                            // Redirect user to welcome page
                            header("location: ewelcome.php");

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
                echo "Oops! Something went wrong. Please try again later.";
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
    <title>Exhibitor Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        .wrapper{ width: 350px; padding: 20px; }
    </style>
</head>
<body>
<div class="wrapper">
    <h2>Login</h2>
    <p>Please fill in your credentials to login.</p>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

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
        </div>

        <p>Don't have an account? <a href="eregister.php">Sign up now</a>.</p>
        <p><a href="https://www.expoexpress.online/">Home</a>.</p>
    </form>
</div>
</body>
</html>