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
	#$result = $database->query("INSERT INTO user(name, password) VALUES('" . $data["name"] . "', " . $data["password"] . ")");
    $result = $database->query("SELECT * FROM users WHERE name='" . $data["name"] . "' AND password_hash='" . $data["password"] . "'");

    //Return a 500 response with error message if the query fails or no matching user found.
    if (!$result || mysqli_num_rows($result) == 0) {
        http_response_code(500);
        die(json_encode(array("error" => "Incorrect username or password.")));
    }

    echo 1;
	$user = mysqli_fetch_assoc($result);
	$role = $user["type"];

	//Check the user's role and perform actions accordingly.
	switch ($role) {
		case "a":
			echo "Good morning, admin!";
			break;
		case "b":
			echo "Hello, teacher!";
			break;
		case "s":
			echo "Welcome, guest.";
			break;
		default:
			echo "Unknown role.";
			break;
	}
?>