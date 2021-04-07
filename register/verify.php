<?php
require_once "../db/config.php";
$role = "";
if(isset($_GET['email']) && !empty($_GET['email']) AND isset($_GET['hash']) && !empty($_GET['hash'])){
    // Verify data
    $email = mysqli_real_escape_string($link,$_GET['email']); // Set email variable
    $hash = mysqli_real_escape_string($link,$_GET['hash']); // Set hash variable
    //Checks if stuff matches
    $query = "SELECT user_email, hash, active FROM users WHERE user_email='".$email."' AND hash='".$hash."' AND active='0'";
    $result = mysqli_query($link,$query) or die(mysqli_error());
    $match  = mysqli_fetch_row($result);

    if($match > 0){
        // We have a match, activate the account
        $query = "UPDATE users SET active='1' WHERE user_email='".$email."' AND hash='".$hash."' AND active='0'";
        mysqli_query($link,$query) or die(mysqli_error());
        echo '<div class="statusmsg">Your account has been activated, you can now login</div>';
        header("location: index.php");


    }else{
        // No match -> invalid url or account has already been activated.
        echo "You are already activated or an error has occurred. Please try logging in or contact support @ expoexpressonline@gmail.com";
    }
}else{
    die("ERROR: ". mysqli_error($link));
    // Invalid approach
}




?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Verification</title>
    <style type="text/css">
        <?php echo file_get_contents('../css/style.css'); ?>
    </style>
</head>
<body>
<!-- start header div -->
<div id="header">
    <h3>If you are not redirected please login here:<a href="index.php">Login here</a>.</h3>
</div>


</div>

</body>
</html>