<?php
	global $database;

	$data = json_decode(file_get_contents("php://input"), true);

	//Return a 400 response if no category information was provided in the request body.
	if (!$data) {
		http_response_code(400);
		die("Please provide the category information as a correct JSON object in the request body.");
	}

	//Make sure the required fields are provided.
	if (!isset($data["name"]) || !isset($data["password"])) {
		http_response_code(400);
		die("You must provide the attributes \"name\" and \"password\".");
	}

	//Insert the data into the database.
	$result = $database->query("INSERT INTO user(name, password) VALUES('" . $data["name"] . "', " . $data["password"] . ")");

	//Return a 500 response if there was an error with the query.
	if (!$result) {
		http_response_code(500);
		die("Error.");
	}

	//Return a 201 response if the entry was successfully created.
	http_response_code(201);
	die();
?>