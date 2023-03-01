<?php
    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;

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

    $app->get("/Places", function (Request $request, Response $response, $args) {
        validate_token(); // unotherized pepole will get rejected

        $id = intval($args["id"]);
        
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

    $app->put("/Place/{id}", function (Request $request, Response $response, $args) {

        validate_token();
        
        $id = intval($args["id"]);
        
        $place = get_room($id);
        
        if (!$place) {
            error_function(404, "No place found for the ID " . $id . ".");
        }
        
        $request_body_string = file_get_contents("php://input");
        
        $request_data = json_decode($request_body_string, true);
        $position = trim($request_data["position"]);
        $name = trim($request_data["name"]);
        $type = trim($request_data["type"]);

        if (empty($name)) {
            error_function(400, "Please provide the (name) field.");
        } 
        if (empty($position)) {
            error_function(400, "Please provide the (position) field.");
        } 
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
        
        if (update_place($id, $position, $name, $type)) {
            message_function(200 ,"The placedata were successfully updated");
        }
        else {
            error_function(500, "An error occurred while saving the place data.");
        }
        
        return $response;
    });

    $app->delete("/Place/{id}", function (Request $request, Response $response, $args) {
        
        validate_token();
        
        $id = intval($args["id"]);
        
        $result = delete_place($id);
        
        if (!$result) {
            error_function(404, "No place found for the ID " . $id . ".");
        }
        else {
            message_function(200, "The place was succsessfuly deleted.");
        }
        
        return $response;
    });
?>