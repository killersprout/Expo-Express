<?php
include "../mysql/db.php";

function showAllData() {
	global $connection;
	$query = "SELECT * FROM exhibitors ";
//Takes in the database connection variable and what we are doing
	$result = mysqli_query( $connection, $query );

//check if query succeeded
	if ( ! $result ) {
		die( "Query Failed" . mysqli_error( $connection ) );
	}

	while ( $row = mysqli_fetch_assoc( $result ) ) {
		$id = $row['id'];
		echo "<option value='$id'>$id</option>";
	}
}