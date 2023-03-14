<?php
	//require the database information
	require_once "util/config.php";

	//connect to database
	$database = new mysqli($db_hostname, $db_username, $db_password, $db_database);
?>
