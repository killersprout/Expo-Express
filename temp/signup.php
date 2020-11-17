<?php
include "../db/config.php";
include "../functions/functions.php";
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
    # $query = "INSERT INTO exhibitors(username,password) ";
    // .= concats
    # $query .= "VALUES ('$username','$password')";

    $query = "SELECT * FROM exhibitors "; // Temp to say hello
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
	        <!--- Changed this to CSS--->
            <link rel="stylesheet" type="text/css" href="/css/register.css" />
</head>
<body>
<div class="container">
	<div class="col-sm-6">
		<form action="signup.php" method="post">
            <h1>Register</h1>
            <p>Please fill in this form to create an account.</p>
			<div class="form-group">
				<label for="username">Username</label>
                <label>
                    <input type="text" placeholder="Enter Username" name="username" class="form-control">
                </label>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <label>
                    <input type="text" placeholder="Enter Email" name="email" class="form-control">
                </label>
            </div>

			<div class="form-group">
				<label for="password" name="password">Password</label>
                <label>
                    <input type="password" placeholder="Enter Password" name="password" class="form-control">
                </label>
            </div>

            <div class="form-group">
                <label for="status" name="status">Status</label>
                <label>
                    <input type="text" placeholder="Enter association" name="status" class="form-control">
                </label>
            </div>

            <input class="btn btn-primary" type="submit" name="submit" value="Submit">

		</form>
	</div>
</div>

</body>
</html>

<?php
while ( $row = mysqli_fetch_assoc( $result ) ) {
?>

    <pre>

    <?php
        print_r($row);
    ?>
    </pre>
<?php
}
?>