<?php

    // Define custom error handler function
    function customError($errno, $errstr) {
        echo " "; // Error handler does nothing
    }

    // Set custom error handler
    set_error_handler("customError");

    // Import required PSR HTTP message interfaces for request/response handling
    use Psr\Http\Message\ResponseInterface as Response; 
    use Psr\Http\Message\ServerRequestInterface as Request;

    // Import Slim framework and token generation library
    use Slim\Factory\AppFactory;
    use ReallySimpletoken\Token;

    // Import all required project libraries
    require __DIR__ . "/../vendor/autoload.php";
    require_once "Controler/validation.php";
    require "Model/users.php";
    require "Model/place.php";
    require "Model/events.php";
    require_once "Controler/error-and-info-messages.php";

    // Set content type of all responses to be JSON
    header("Content-Type: application/json");

    // Create a new Slim app instance
    $app = AppFactory::create();

    // Set the base path for all routes
    $app->setBasePath("/API/V1");

    /**
     * Handle user login request
     * @param args Unused
     * @param request_body JSON data containing username and password fields
     * @return response HTTP response indicating success or error message
     */
    $app->post("/Login", function (Request $request, Response $response, $args) {

        // Read the JSON body of the request
        $body_content = file_get_contents("php://input");
        $JSON_data = json_decode($body_content, true);

        // Check that the request contains both a username and password field
        if (isset($JSON_data["username"]) && isset($JSON_data["password"])) {
        } else {
            error_function(400, "Empty request");
        }

        // Validate and sanitize the username and password fields
        $name = validate_string($JSON_data["username"]);
        $password = validate_string($JSON_data["password"]);

        // Check that the password is at least 5 characters
        if (!$password) {
            error_function(400, "password is invalid, must contain at least 5 characters");
        }
        // Check that the username is at least 5 characters
        if (!$name) {
            error_function(400, "username is invalid, must contain at least 5 characters");
        }

        // Hash the password
        $password = hash("sha256", $password);

        // Get the user by their username
        $user = get_user_by_username($name);

        // Check that the provided password matches the hashed password for the user
        if ($user["password_hash"] !==  $password) {
            error_function(404, "not Found");
        }

        // Check that the provided username matches the user's actual username
        if ($user["name"] !==  $name) {
            error_function(404, "not Found");
        }

        // Generate a new token for the user and set it as a cookie
        $token = create_token($name, $password, $user["id"]);
        setcookie("token", $token, time() + 3600);

        // Return a success message
        message_function(200, "Successfully logged in");
        
        return $response;
    });

    function user_validation($required_role = null) {
        $current_user_id = validate_token();
        $current_user_role = get_user_type($current_user_id);
        if ($required_role !== null && $current_user_role !== $required_role) {
            error_function(403, "Access Denied");
        }
        return $current_user_id;
    }
    
    /**
     * Check if you're authticate or not. 
     */
    $app->get("/WhoAmI", function (Request $request, Response $response, $args) {
        // unotherized pepole will get rejected
        $id = user_validation();
		$user = get_user_id($id);

		if ($user) {
	        echo json_encode($user);
		}
		else if (is_string($user)) {
			error($user, 500);
		}
		else {
			error("The ID "  . $id . " was not found.", 404);
		}

        return $response;
    });

    
    $app->get('/Time', function (Request $request, Response $response, $args) {
        // Connect to the database
        global $database;
    
        // Query the database for expired reservations
        //The source of the (< NOW();) peice is from https://chat.openai.com/chat
        $result = $database->query("SELECT id FROM `events` WHERE to_date < NOW();");
    
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $id = $row['id'];
    
            // Delete the expired reservation
            $delete_result = $database->query("DELETE FROM `events` WHERE id = '$id';");
    
            if ($delete_result) {
                echo "Expired reservation with ID $id has been deleted. \n";
            } else {
                echo "Error deleting reservation. \n";
            }
        } else {
            echo "No expired reservations found. \n";
        }
    });

   //Require all API
    require "Controler/routes/users.php";
    require "Controler/routes/events.php";
    require "Controler/routes/place.php";

    $app->run();
?>
