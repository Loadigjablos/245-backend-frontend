<?php
    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;

    $app->get("/Users", function (Request $request, Response $response, $args) {
        $id = user_validation("A");
        validate_token(); // unotherized pepole will get rejected

        $users = get_all_users();

        if ($users) {
            echo json_encode($users);
        }
        else if (is_string($users)) {
            error($users, 500);
        }
        else {
            error("The ID "  . $id . " was not found.", 404);
        }

        return $response;
    });

    $app->post("/User", function (Request $request, Response $response, $args) {
        $id = user_validation("A");
        validate_token();

        $request_body_string = file_get_contents("php://input");
        $request_data = json_decode($request_body_string, true);
        $name = trim($request_data["name"]);
        $email = trim($request_data["email"]);
        $password = trim($request_data["password"]);
        $type = trim($request_data["type"]);
        $add_date = trim($request_data["add_date"]);
    
        //The position field cannot be empty and must not exceed 2048 characters
        if (empty($name)) {
            error_function(400, "The (name) field must not be empty.");
        } 
        elseif (strlen($name) > 255) {
            error_function(400, "The (name) field must be less than 2048 characters.");
        }
    
        //The name field cannot be empty and must not exceed 255 characters
        if (empty($email)) {
            error_function(400, "The (email) field must not be empty.");
        } 
        elseif (strlen($email) > 255) {
            error_function(400, "The (email) field must be less than 255 characters.");
        }
    
        //The type field must be an uppercase alphabetic character
        if (empty($password)) {
            error_function(400, "Please provide the (password) field.");
        } 

        //The type field must be an uppercase alphabetic character
        if (empty($type)) {
            error_function(400, "Please provide the (type) field.");
        } 

        //The type field must be an uppercase alphabetic character
        if (empty($add_date)) {
            error_function(400, "Please provide the (add_date) field.");
        } 

        $password = hash("sha256", $password);
    
        //checking if everything was good
        if (create_user($name, $email, $password, $type, $add_date) === true) {
            message_function(200, "The user was successfully created.");
        } else {
            error_function(500, "An error occurred while saving the userdata.");
        }
        return $response;        
    });

    $app->put("/User/{id}", function (Request $request, Response $response, $args) {

		$id = user_validation("A");
        validate_token();
		
		$user_id = $args["id"];
		
		$user = get_user_by_id($id);
		
		if (!$user) {
			error_function(404, "No user found for the id ( " . $user_id . " ).");
		}
		
		$request_body_string = file_get_contents("php://input");
		
		$request_data = json_decode($request_body_string, true);

		if (isset($request_data["name"])) {
			$name = strip_tags(addslashes($request_data["name"]));
		
			if (strlen($name) > 255) {
				error_function(400, "The name is too long. Please enter less than 255 letters.");
			}
		
			$user["name"] = $name;
		}

        if (isset($request_data["email"])) {
			$email = strip_tags(addslashes($request_data["email"]));
		
			if (strlen($email) > 500) {
				error_function(400, "The email is too long. Please enter less than 500 letters.");
			}
		
			$user["email"] = $email;
		}

        if (isset($request_data["password_hash"])) {
			$password = strip_tags(addslashes($request_data["password_hash"]));
		
			if (strlen($password) > 1000) {
				error_funciton(400, "The password is too long. Please enter less than 1000 letters.");
			}
		
			$user["password_hash"] = $password;

            $user["password_hash"] = hash("sha256", $password);

		}

        if (isset($request_data["type"])) {
			$type = strip_tags(addslashes($request_data["type"]));
		
			if (strlen($type) > 1000) {
				error_funciton(400, "The type is too long. Please enter less than 1000 letters.");
			}
		
			$user["type"] = $type;
		}

        if (isset($request_data["add_date"])) {
			$add_date = strip_tags(addslashes($request_data["add_date"]));
		
			if (strlen($add_date) > 1000) {
				error_function(400, "The type is too long. Please enter less than 1000 letters.");
			}
		
			$user["add_date"] = $add_date;
		}
		
		if (update_user($user_id, $user["name"], $user["email"], $user["password_hash"], $user["type"], $user["add_date"])) {
			message_function(200, "The userdata were successfully updated");
		}
		else {
			error_function(500, "An error occurred while saving the user data.");
		}
		
		return $response;
	});
    
    $app->delete("/User/{name}", function (Request $request, Response $response, $args) {
		$id = user_validation("A");
        validate_token();
		
		$name = $args["name"];
		
		$result = delete_user($name);
		
		if (!$result) {
			error_function(404, "No user found for the Name ( " . $name . " ).");
		}
		else {
			message_function(200, "The user was succsessfuly deleted.");
		}
		
		return $response;
	});
?>