<?php
    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;

    $app->get("/Reservations", function (Request $request, Response $response, $args) {
        //everyone
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
    
    
    $app->get("/Reservation/{id}", function (Request $request, Response $response, $args) {
        //everyone
        validate_token(); // unotherized pepole will get rejected

        $id = intval($args["id"]);

        $reservation = get_reservation_by_id($id);

        if ($reservation) {
            echo json_encode($reservation);
        }
        else if (is_string($reservation)) {
            error($reservation, 500);
        }
        else {
            error("The Name "  . $id . " was not found.", 404);
        }

        return $response;
    });

    $app->post("/Reservation", function (Request $request, Response $response, $args) {
        //everyone
        validate_token();

        $request_body_string = file_get_contents("php://input");
        $request_data = json_decode($request_body_string, true);

        $from_date = trim($request_data["from_date"]);
        $to_date = trim($request_data["to_date"]);
        $place_name = trim($request_data["place_name"]);
        $host = trim($request_data["host"]);
        $description = trim($request_data["description"]);
    
        //The position field cannot be empty and must not exceed 2048 characters
        if (empty($to_date)) {
            error_function(400, "The (to date) field must not be empty.");
        } 
        elseif (strlen($to_date) > 2048) {
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
        if (create_reservation($from_date, $to_date, $place_name, $host, $description) === true) {
            message_function(200, "The reservation was successfully created.");
        } 
        else {
            error_function(500, "An error occurred while saving the reservation.");
        }
        return $response;        
    });

    $app->put("/Reservation/{id}", function (Request $request, Response $response, $args) {

		$id = user_validation("A");
        validate_token();
		
		$id = $args["id"];
		
		$reservation = get_reservation_by_id($id);
		
		if (!$reservation) {
			error_function(404, "No reservation found for the id ( " . $id . " ).");
		}
		
		$request_body_string = file_get_contents("php://input");
		
		$request_data = json_decode($request_body_string, true);

		if (isset($request_data["from_date"])) {
			$from_date = strip_tags(addslashes($request_data["from_date"]));
		
			if (strlen($from_date) > 255) {
				error_function(400, "The from_date is too long. Please enter less than 255 letters.");
			}
		
			$reservation["from_date"] = $from_date;
		}

        if (isset($request_data["to_date"])) {
			$to_date = strip_tags(addslashes($request_data["to_date"]));
		
			if (strlen($to_date) > 500) {
				error_function(400, "The to_date is too long. Please enter less than 500 letters.");
			}
		
			$reservation["to_date"] = $to_date;
		}

        if (isset($request_data["place_name"])) {
			$place_name = strip_tags(addslashes($request_data["place_name"]));
		
			if (strlen($place_name) > 1000) {
				error_funciton(400, "The place_name is too long. Please enter less than 1000 letters.");
			}
		
			$reservation["place_name"] = $place_name;
		}

        if (isset($request_data["host"])) {
			$host = strip_tags(addslashes($request_data["host"]));
		
			if (strlen($host) > 1000) {
				error_funciton(400, "The host is too long. Please enter less than 1000 letters.");
			}
		
			$reservation["host"] = $host;
		}

        if (isset($request_data["description"])) {
			$description = strip_tags(addslashes($request_data["description"]));
		
			if (strlen($description) > 1000) {
				error_function(400, "The description is too long. Please enter less than 1000 letters.");
			}
		
			$reservation["description"] = $description;
		}
		
		if (update_reservation($id, $reservation["from_date"], $reservation["to_date"], $reservation["place_name"], $reservation["host"], $reservation["description"])) {
			message_function(200, "The reservation data were successfully updated");
		}
		else {
			error_function(500, "An error occurred while saving the reservation data.");
		}
		
		return $response;
	});

    $app->delete("/Reservation/{id}", function (Request $request, Response $response, $args) {
        //everyone
        validate_token();
        
        $id = $args["id"];
        
        $result = delete_reservation($id);
        
        if (!$result) {
            error_function(404, "No reservation found for the id " . $id . ".");
        }
        else {
            message_function(200, "The reservation was succsessfuly deleted.");
        }
        
        return $response;
    });

?>
