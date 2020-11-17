<?php
//Serves as our main database connection script
//Connecting to database
//host,user,pw,database
$link = mysqli_connect('DB_HOST','DB_USER','DB_PW','DB_NAME');

//tests that the connections worked
if(!$link){
	die("DB Connection failed".mysqli_error());
}