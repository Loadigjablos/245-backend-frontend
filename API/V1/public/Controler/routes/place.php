<?php
    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;

    $app->get("/Place/{place_name}", function (Request $request, Response $response, $args) {
  
        validate_token(); // unotherized pepole will get rejected

		$place_name = $args["place_name"];

		$place = get_room($place_name);

		if ($place) {
            echo json_encode($place);
		}
		else if (is_int($place)) {
			error($place, 500);
		}
		else {
			error("The ID "  . $place_name . " was not found.", 404);
		}

        return $response;
    });

    $app->get("/Places", function (Request $request, Response $response, $args) {
        //everyone
        validate_token(); // unotherized pepole will get rejected

        $places = get_all_places();

        if ($places) {
            echo json_encode($places);
        }
        else if (is_string($places)) {
            error($places, 500);
        }
        else {
            error_funciton(400, "There is no place");
        }

        return $response;
    });


    $app->post("/Place", function (Request $request, Response $response, $args) {
        $id = user_validation("A");
        validate_token();

        $request_body_string = file_get_contents("php://input");
        $request_data = json_decode($request_body_string, true);
        $position = trim($request_data["position"]);
        $name = trim($request_data["name"]);
        $type = trim($request_data["type"]);
    
        //The position field cannot be empty and must not exceed 2048 characters
        if (empty($position)) {
            error_function(400, "The (position) field must not be empty.");
        } 
        elseif (strlen($position) > 2048) {
            error_function(400, "The (position) field must be less than 2048 characters.");
        }
    
        //The name field cannot be empty and must not exceed 255 characters
        if (empty($name)) {
            error_function(400, "The (name) field must not be empty.");
        } 
        elseif (strlen($name) > 255) {
            error_function(400, "The (name) field must be less than 255 characters.");
        }
    
        //The type field must be an uppercase alphabetic character
        if (empty($type)) {
            error_function(400, "Please provide the (type) field.");
        } 
        elseif (!ctype_alpha($type)) {
            error_function(400, "The (type) field must contain only alphabetic characters.");
        } 
        elseif (!ctype_upper($type)) {
            error_function(400, "The (type) field must be an uppercase alphabetic character.");
        } 
        elseif ($type !== 'R' && $type !== 'P') {
            error_function(400, "The (type) field must be either 'R' or 'P'.");
        }
    
        //checking if everything was good
        if (create_place($position, $name, $type) === true) {
            message_function(200, "The Place was successfully created.");
        } else {
            error_function(500, "An error occurred while saving the place.");
        }
        return $response;        
    });

    $app->delete("/Place/{place_name}", function (Request $request, Response $response, $args) {
		$id = user_validation("A");
        validate_token();
		
		$place_name = $args["place_name"];
		
		$result = delete_place($place_name);
		
		if (!$result) {
			error_function(404, "No place found for the Name " . $place_name . ".");
		}
		else {
			message_function(200, "The place was succsessfuly deleted.");
		}
		
		return $response;
	});
?>
