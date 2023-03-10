<?php
    // Importing necessary interfaces
    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;

    // Creating an endpoint to get all users
    $app->get("/Users", function (Request $request, Response $response, $args) {
        // Validating user access level
        $id = user_validation("A");

        // Validating user token
        validate_token(); // Unauthorized people will get rejected

        // Getting all users from the database
        $users = get_all_users();

        // If there are users in the database, return them as a JSON string
        if ($users) {
            echo json_encode($users);
        }
        // If there was an error while getting users, return a 500 error with an error message
        else if (is_string($users)) {
            error($users, 500);
        }
        // If the requested user ID was not found, return a 404 error with an error message
        else {
            error("The ID "  . $id . " was not found.", 404);
        }

        // Return the response
        return $response;
    });

    // Creating an endpoint to create a new user
    $app->post("/User", function (Request $request, Response $response, $args) {
        // Validating user access level
        $id = user_validation("A");

        // Validating user token
        validate_token();

        // Getting the request body and decoding it from JSON
        $request_body_string = file_get_contents("php://input");
        $request_data = json_decode($request_body_string, true);

        // Validating and sanitizing input fields
        $name = trim(validate_string($request_data["name"]));
        $email = trim(validate_string($request_data["email"]));
        $password = trim(validate_string($request_data["password"]));
        $type = trim(validate_string($request_data["type"]));
        $add_date = trim(validate_string($request_data["add_date"]));

        // Checking for errors in the input fields
        if (empty($name)) {
            error_function(400, "The (name) field must not be empty.");
        } 
        elseif (strlen($name) > 255) {
            error_function(400, "The (name) field must be less than 2048 characters.");
        }

        if (empty($email)) {
            error_function(400, "The (email) field must not be empty.");
        } 
        elseif (strlen($email) > 255) {
            error_function(400, "The (email) field must be less than 255 characters.");
        }

        if (empty($password)) {
            error_function(400, "Please provide the (password) field.");
        } 

        if (empty($type)) {
            error_function(400, "Please provide the (type) field.");
        } 

        if (empty($add_date)) {
            error_function(400, "Please provide the (add_date) field.");
        } 

        // Hashing the password
        $password = hash("sha256", $password);

        // Creating the new user in the database
        if (create_user($name, $email, $password, $type, $add_date) === true) {
            message_function(200, "The user was successfully created.");
        } else {
            error_function(500, "An error occurred while saving the userdata.");
        }

        // Return the response
        return $response;        
    });

    // This is a route for updating a user's data using the PUT method and the user ID as a parameter
    $app->put("/User/{id}", function (Request $request, Response $response, $args) {

        // Validate the user session
        $id = user_validation("A");
        validate_token();

        // Get the user ID from the parameter and validate it
        $user_id = validate_number($args["id"]);

        // Get the user data by ID
        $user = get_user_by_id($id);

        // If the user does not exist, return a 404 error
        if (!$user) {
            error_function(404, "No user found for the id ( " . $user_id . " ).");
        }

        // Get the request body as a string
        $request_body_string = file_get_contents("php://input");

        // Decode the request body as JSON
        $request_data = json_decode($request_body_string, true);

        // If the "name" field is present in the request, update the user's name
        if (isset($request_data["name"])) {
            $name = validate_string($request_data["name"], 255);
            $user["name"] = $name;
        }

        // If the "email" field is present in the request, update the user's email
        if (isset($request_data["email"])) {
            $email = validate_string($request_data["email"], 500);
            $user["email"] = $email;
        }

        // If the "password_hash" field is present in the request, update the user's password hash
        if (isset($request_data["password_hash"])) {
            $password = validate_string($request_data["password_hash"], 1000);
            $user["password_hash"] = hash("sha256", $password);
        }

        // If the "type" field is present in the request, update the user's type
        if (isset($request_data["type"])) {
            $type = validate_string($request_data["type"], 1000);
            $user["type"] = $type;
        }

        // If the "add_date" field is present in the request, update the user's add date
        if (isset($request_data["add_date"])) {
            $add_date = validate_string($request_data["add_date"], 1000);
            $user["add_date"] = $add_date;
        }

        // Update the user's data in the database
        if (update_user($user_id, $user["name"], $user["email"], $user["password_hash"], $user["type"], $user["add_date"])) {
            // If the update was successful, return a success message
            message_function(200, "The userdata were successfully updated");
        }
        else {
            // If the update was not successful, return a 500 error
            error_function(500, "An error occurred while saving the user data.");
        }

        // Return the response
        return $response;
	});

    
    // Define a route for deleting a user by name
    $app->delete("/User/{name}", function (Request $request, Response $response, $args) {
        // Validate user permissions and token
        $id = user_validation("A");
        validate_token();

        // Get the name of the user to delete from the request parameters
        $name = validate_string($args["name"]);
        
        // Attempt to delete the user
        $result = delete_user($name);
        
        // If the user wasn't found, return a 404 error
        if (!$result) {
            error_function(404, "No user found for the Name ( " . $name . " ).");
        }
        // Otherwise, return a success message
        else {
            message_function(200, "The user was succsessfuly deleted.");
        }
        
        // Return the response
        return $response;
    });

?>