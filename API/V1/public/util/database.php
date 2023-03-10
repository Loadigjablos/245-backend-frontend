<?php
    // Include the configuration file with the database connection details.
	require_once "util/config.php";

	// Create a new mysqli object with the database connection details.
	// The parameters are, in order: the hostname of the database server, the username for the database, 
    // the password for the database, and the name of the database to be used.
	$database = new mysqli($db_hostname, $db_username, $db_password, $db_database);
?>
