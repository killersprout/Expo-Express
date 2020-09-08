<?php
//Adding the 2 dots makes it so you dont need the full path
include "../mysql/db.php";
include "../functions/functions.php";
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
		<form action="updateUserandPass.php" method="post">
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

			<div>
				<div class="form-group">
					<select name="id" id="">
            <!--Need to create a loop to go thru each ID -->
                        <?php
                            showAllData();
                        ?>

					</select>
			</div>

			<!--- btn btn-primary makes button blue--->
			<input class="btn btn-primary" type="submit" name="submit" value="UPDATE">
		</form>
	</div>
</div>

</body>
</html>