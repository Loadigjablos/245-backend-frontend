<?php

    //error handler function
    function customError($errno, $errstr) {
        echo " ";
    }
    
    //set error handler
    set_error_handler("customError");

    // this handel the request and response.
    use Psr\Http\Message\ResponseInterface as Response; 
    use Psr\Http\Message\ServerRequestInterface as Request;

    // This allows to Using Slim and build our application.
    use Slim\Factory\AppFactory;
    use ReallySimpletoken\Token;

    // all the libraries we need.
    require __DIR__ . "/../vendor/autoload.php";
    // self made functions
    require_once "Controler/validation.php";
    require "Model/users.php";
    require "Model/place.php";
    require_once "Controler/error-and-info-messages.php";

    // all response data will be in the Json Fromat
    header("Content-Type: application/json");

    $app = AppFactory::create();

    $app->setBasePath("/API/V1");

    /**
     * This will work
     * @param args 
     * @param request_body 
     * @return response 
     */
    $app->post("/Login", function (Request $request, Response $response, $args) {

        // reads the requested JSON body
        $body_content = file_get_contents("php://input");
        $JSON_data = json_decode($body_content, true);

        // if JSON data doesn't have these then there is an error
        if (isset($JSON_data["username"]) && isset($JSON_data["password"])) {
        } else {
            error_function(400, "Empty request");
        }

        // Prepares the data to prevent bad data, SQL injection andCross site scripting
        $name = validate_string($JSON_data["username"]);
        $password = validate_string($JSON_data["password"]);

        if (!$password) {
            error_function(400, "password is invalid, must contain at least 5 characters");
        }
        if (!$name) {
            error_function(400, "username is invalid, must contain at least 5 characters");
        }

        $password = hash("sha256", $password);

        $user = get_user_by_username($name);

        if ($user["password_hash"] !==  $password) {
            error_function(404, "not Found");
        }

        $token = create_token($name, $password, $user["id"]);

        setcookie("token", $token, time() + 3600);

        message_function(200, "Successfully logged in");
        
        return $response;
    });

    function user_validation(){
        $current_user = validate_token();
        if ($current_user === false) {
            error_function(403, "unauthenticated");
        }
        return $current_user;
    };

    $app->get("/WhoAmI", function (Request $request, Response $response, $args) {
        $id =  user_validation(); // unotherized pepole will get rejected

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


    require "Controler/routes/users.php";
    require "Controler/routes/events.php";
    require "Controler/routes/place.php";

    $app->run();
?>
