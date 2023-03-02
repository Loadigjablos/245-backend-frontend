<?php
    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;

    $app->get("/Reservations", function (Request $request, Response $response, $args) {
        validate_token(); // unotherized pepole will get rejected

        $reservations = get_all_reservations();

        if ($reservations) {
            echo json_encode($reservations);
        }
        else if (is_string($reservations)) {
            error_function(500, $reservations);
        }
        else {
            error(400, "Error");
        }

        return $response;
    });
    
    
    $app->get("/Reservation/{place_name}", function (Request $request, Response $response, $args) {
        validate_token(); // unotherized pepole will get rejected

        $place_name = $args["place_name"];

        $reservation = get_reservation_by_name($place_name);

        if ($reservation) {
            echo json_encode($reservation);
        }
        else if (is_string($reservation)) {
            error($reservation, 500);
        }
        else {
            error("The Name "  . $place_name . " was not found.", 404);
        }

        return $response;
    });

    $app->post("/Reservation", function (Request $request, Response $response, $args) {
        validate_token();

        $request_body_string = file_get_contents("php://input");
        $request_data = json_decode($request_body_string, true);

        $date_time = trim($request_data["date_time"]);
        $place_name = trim($request_data["place_name"]);
        $host = trim($request_data["host"]);
        $description = trim($request_data["description"]);
    
        //The position field cannot be empty and must not exceed 2048 characters
        if (empty($date_time)) {
            error_function(400, "The (date_time) field must not be empty.");
        } 
        elseif (strlen($date_time) > 2048) {
            error_function(400, "The (date_time) field must be less than 2048 characters.");
        }

        $place_name = "NULL";
		if (isset($request_data["place_name"])) {
			$place_name = $request_data["place_name"];
		}

        $host = "NULL";
        if (isset($request_data["host"])) {
			$host = $request_data["host"];
		}

        $description = "NULL";
        if (isset($request_data["description"])) {
			$description = "'" . $request_data["description"] . "'";
		}

        if (strlen($description) > 2048) {
            error_function(400, "The (host) field must be less than 255 characters.");
        }        
    
        //checking if everything was good
        if (create_reservation($date_time, $place_name, $host, $description) === true) {
            message_function(200, "The reservation was successfully created.");
        } 
        else {
            error_function(500, "An error occurred while saving the reservation.");
        }
        return $response;        
    });

    $app->delete("/Reservation/{place_name}", function (Request $request, Response $response, $args) {
        
        validate_token();
        
        $place_name = $args["place_name"];
        
        $result = delete_reservation($place_name);
        
        if (!$result) {
            error_function(404, "No reservation found for the place_name " . $place_name . ".");
        }
        else {
            message_function(200, "The reservation was succsessfuly deleted.");
        }
        
        return $response;
    });

?>