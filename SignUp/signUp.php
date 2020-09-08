<?php
include "../mysql/db.php";
//https://getbootstrap.com/docs/4.5/getting-started/download/ this was used for the style
//markup file
//This script to sign up. It will take whatever you put in the fields and save it to the DB
//This is basic sign up.
//checks from submit button being pressed with post method

if(isset($_POST['submit'])){

    $username = $_POST['username']; // aligns with HTML name=username
    $password = $_POST['password']; // aligns with HTML name=password


    //***Need to watch out for correct syntax***
    //Commands are the same as if you were to do then manually into SQL
    //Inserts inputted username and password expoexpress->exhibitors->username | password
    $query = "INSERT INTO exhibitors(username,password) ";
    // .= concats
    $query .= "VALUES ('$username','$password')";

    //Takes in the database connection variable and the query we want to perform
    $result = mysqli_query($connection,$query);

    //check if query succeeded
    if(!$result){
        die("Query Failed" . mysqli_error($connection));
    }

}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Signin</title>
	<!--- This is a frame work makes it look better--->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

</head>
<body>
<div class="container">
	<div class="col-sm-6">
		<form action="signUp.php" method="post">
			<div class="form-group">
				<label for="username">Username</label>
                <label>
                    <input type="text" name="username" class="form-control">
                </label>
            </div>

			<div class="form-group">
				<label for="password" name="password">Password</label>
                <label>
                    <input type="password" name="password" class="form-control">
                </label>
            </div>
            <!--- btn btn-primary makes button blue--->
            <input class="btn btn-primary" type="submit" name="submit" value="Submit">
		</form>
	</div>
</div>

</body>
</html>