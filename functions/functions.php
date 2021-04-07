<?php
include "../register/config.php";

function showAllData() {
	global $link;
	$query = "SELECT * FROM register ";
//Takes in the database connection variable and what we are doing
	$result = mysqli_query( $link, $query );

//check if query succeeded
	if ( ! $result ) {
		die( "Query Failed" . mysqli_error( $link ) );
	}

	while ( $row = mysqli_fetch_assoc( $result ) ) {
		$id = $row['id'];

		print_r($row);

		echo "<option value='$id'>$id</option>";
	}
}

function validateMember()
{
    global $link;
    $valid = true;
    $errorMessage = array();
    foreach ($_POST as $key => $value) {
        if (empty($_POST[$key])) {
            $valid = false;
        }
    }

    if($valid == true) {
        if ($_POST['password'] != $_POST['confirm_password']) {
            $errorMessage[] = 'Passwords should be same.';
            $valid = false;
        }

        if (! isset($error_message)) {
            if (! filter_var($_POST["userEmail"], FILTER_VALIDATE_EMAIL)) {
                $errorMessage[] = "Invalid email address.";
                $valid = false;
            }
        }
    }
    else {
        $errorMessage[] = "All fields are required.";
    }

    if ($valid == false) {
        return $errorMessage;
    }
    return;
}

function isLoggedIn(){
    //used for only if we want people to access a page while logged in
    if (isset($_SESSION['user'])) {
        return true;
    }else{
        return false;
    }

    /*<?php
include('functions.php');
if (!isLoggedIn()) {
	$_SESSION['msg'] = "You must log in first";
	header('location: login.php');
}
*/
}

function emailVerify($username,$firstName,$lastName,$organization,$email,$hash){

    $to      = " " ; // Send email to our user
    $subject = 'Verification'; // Give the email a subject
    $message = '
 
A new user is requesting to sign up to host an event.
 
------------------------
Username:     '.$username.'
First Name:   '.$firstName.'
Last Name:    '.$lastName.'
Organization: '.$organization.'
------------------------
 
Please click this link to activate your account:
http://www.expoexpress.online/register/verify.php?email='.$email.'&hash='.$hash.'
 
';
    // Our message above including the link

    $headers = 'From:noreply@expoexpress.online' . "\r\n"; // Set from headers
    mail($to, $subject, $message, $headers); // Send our email
}

function getUserById($id){
    global $link; //establish db connection

    //Hopefully this only allows for people who are approved
    $query = "SELECT * FROM organizers WHERE (active = 1) AND id =" .$id; //get user by id, might change to active variable
    $result = mysqli_query($link,$query);

    $user = mysqli_fetch_assoc($result);
    return $firstname . $lastname;
}