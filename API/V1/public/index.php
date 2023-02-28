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

        setcookie("token", $token);

        echo "Successfully logged in";
        

       /* $id = $user["id"];

        $id = get_user_by_id($id);

        echo json_encode($id);*/

        return $response;
    });

    function user_validation(){
        $current_user = validate_token();
        if ($current_user === false) {
            error_function(403, "unauthenticated");
        }
        return $current_user;
    };

    $app->get("/Whoami", function (Request $request, Response $response, $args) {
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

    $app->get("/Users", function (Request $request, Response $response, $args) {
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

    $app->get("/Place/{id}", function (Request $request, Response $response, $args) {
        validate_token(); // unotherized pepole will get rejected

		$id = $args["id"];

		$place = get_room($id);

		if ($place) {
            echo json_encode($place);
		}
		else if (is_string($place)) {
			error($place, 500);
		}
		else {
			error("The ID "  . $id . " was not found.", 404);
		}

        return $response;
    });

    $app->post("/Place", function (Request $request, Response $response, $args) {

		validate_token();

		$request_body_string = file_get_contents("php://input");

		$request_data = json_decode($request_body_string, true);

        $position = strip_tags(addslashes($request_data["position"]));
        $name = strip_tags(addslashes($request_data["name"]));
		$type = strip_tags($request_data["type"]);


        //The name can not be empty
		if (empty($position)) {
			error_function(400, "The (position) field must not be empty.");
		}
		//Limit the length of the name.
		if (strlen($position) > 500) {
			error_function(400, "The name is too long. Please enter less than 500 letters.");
		}

		//The name can not be empty
		if (empty($name)) {
			error_function(400, "The (name) field must not be empty.");
		}
		//Limit the length of the name.
		if (strlen($name) > 500) {
			error_function(400, "The name is too long. Please enter less than 500 letters.");
		}

		//The type have to be an integer
		if (!isset($request_data["type"])) {
			error_function(400, "Please provide the (type) field.");
		}
		//Limit the type nummber
		/*if ($request_data["type"] !== "R" || $request_data["type"] !="P") {
			error_function(400, "The type must either R or P.");
		}*/

        if (!ctype_alpha($request_data["type"])) {
            echo "Error: Input should contain only alphabetic characters."; 
        } 
        elseif (ctype_upper($request_data["type"])) { 
            echo "Input is a capital letter."; 
        }

        if ($request_data["type"] !== ) {
            # code...
        }

		//checking if allthing was good
		if (create_place($position, $name, $type) === true) {
			echo "The Place was successfuly created.";
		}
		//an server error
		else {
			error_function(500, "An error while saving the place.");
		}
		return $response;		
	});


    $app->run();
?>
