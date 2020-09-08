<?php
//Serves as our main database connection script

//Connecting to database
//host,user,pw,database
$connection = mysqli_connect('localhost','root','','expoexpress');

//tests that the connections worked
if(!$connection){
	die("DB Connection failed");
}