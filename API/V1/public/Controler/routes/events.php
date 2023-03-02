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
    
    
    $app->get("/Reservation/{place_name}", function (Request $request, Response $response, $args) {
        //everyone
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
<<<<<<< HEAD
        }   
        
        
    //check if from_date is before to_date
        if (strtotime($from_date) >= strtotime($to_date)) {
            error_function(400, "The (from date) field must be before the (to date) field.");
        }

    
=======
        }
        
        $serch = get_room($place_name);
        if(!$serch){
            error_function(400, "Place Name doesn't exist");
        }
        else if (is_string(!$serch)){
            error_function(400, $serch);
        }

        $serch = get_user_by_username($place_name);
        if(!$serch){
            error_function(400, "name doesn't exist");
        }
        else if (is_string(!$serch)){
            error_function(400, $serch);
        }
>>>>>>> 6810ee7a601b11f5e1163a65a7f4ac9f34c7f925
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

        validate_token();
		
		$place_name = $args["place_name"];
		
		$reservation = get_reservation_by_name($place_name);
		
		if (!$reservation) {
			error_function(404, "No place found for the name ( " . $place_name . " ).");
		}
		
		$request_body_string = file_get_contents("php://input");
		
        $date_time = trim($request_data["date_time"]);
        $place_name = trim($request_data["place_name"]);
        $host = trim($request_data["host"]);
        $description = trim($request_data["description"]);


        if (update_reservation($reservation, $date_time, $place_name, $host, $description)) {
            message_function(200 ,"The reservation data were successfully updated");
        }
        else {
            error_function(500, "An error occurred while saving the reservation data.");
        }
    
        return $response;
    });

    $app->delete("/Reservation/{place_name}", function (Request $request, Response $response, $args) {
        //everyone
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