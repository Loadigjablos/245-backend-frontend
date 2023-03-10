<?php
    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;
    
    // GET request to retrieve all reservations
    $app->get("/Reservations", function (Request $request, Response $response, $args) {
        // validate user token
        validate_token();
    
        // retrieve all reservations
        $reservations = get_all_reservations();
    
        // if there are reservations, return them as JSON
        if ($reservations) {
            echo json_encode($reservations);
        }
        // if there's an error message, return a 500 error
        else if (is_string($reservations)) {
            error_function(500, $reservations);
        }
        // if there are no reservations, return a 400 error
        else {
            error(400, "Error");
        }
    
        // return the response object
        return $response;
    });
    
    // GET request to retrieve a reservation by its ID
    $app->get("/Reservation/{id}", function (Request $request, Response $response, $args) {
        // validate user token
        validate_token();
    
        // retrieve the ID from the URL parameters and validate it as a number
        $id = validate_number(intval($args["id"]));
    
        // retrieve the reservation with the given ID
        $reservation = get_reservation_by_id($id);
    
        // if there is a reservation, return it as JSON
        if ($reservation) {
            echo json_encode($reservation);
        }
        // if there's an error message, return a 500 error
        else if (is_string($reservation)) {
            error($reservation, 500);
        }
        // if there is no reservation with the given ID, return a 404 error
        else {
            error("The Name "  . $id . " was not found.", 404);
        }
    
        // return the response object
        return $response;
    });
    
    // POST request to create a new reservation
    $app->post("/Reservation", function (Request $request, Response $response, $args) {
        // validate user token
        validate_token();
    
        // validate the user making the request
        $id = user_validation();
    
        // retrieve the email address of the user making the request
        $email = get_user_email($id);
    
        // join the email addresses (if there are multiple)
        $email = implode(':', $email);
    
        // retrieve the request body and decode it as JSON
        $request_body_string = file_get_contents("php://input");
        $request_data = json_decode($request_body_string, true);
    
        // retrieve and sanitize the reservation details from the request body
        $from_date = trim($request_data["from_date"]);
        $to_date = trim($request_data["to_date"]);
        $place_name = validate_string(trim($request_data["place_name"])); // sanitize the place_name field
        $host = validate_string(trim($request_data["host"])); // sanitize the host field
        $description = validate_string(trim($request_data["description"])); // sanitize the description field
    
        // validate the 'to_date' field
        if (empty($to_date)) {
            error_function(400, "The (to date) field must not be empty.");
        } elseif (strlen($to_date) > 2048) {
            error_function(400, "The (date_time) field must be less than 2048 characters.");
        }
    
        // validate the 'host' field
        if (strlen($host) > 255) {
            error_function(400, "The (host) field must be less than 255 characters.");
        }    
        if (strlen($host) > 255) {
            error_function(400, "The (host) field must be less than 255 characters.");
        }
    
        //checking if everything was good
        if (create_reservation($from_date, $to_date, $place_name, $host, $description, $email) === true) {
            message_function(200, "The reservation was successfully created.");
        } else {
            error_function(500, "An error occurred while saving the reservation.");
        }
        return $response;
    });    

    $app->put("/Reservation/{id}", function (Request $request, Response $response, $args) {
	
        // Get the user ID and validate their access level
        $id = user_validation("A");
        
        // Validate the token
        validate_token();
    
        // Get the email of the user making the request
        $email = get_user_email($id);
        $email = implode(':', $email);
    
        // Get the reservation ID from the URL and validate it
        $id = validate_number($args["id"]);
        
        // Get the reservation data by ID
        $reservation = get_reservation_by_id($id);
        
        // If no reservation is found, return an error
        if (!$reservation) {
            error_function(404, "No reservation found for the id ( " . $id . " ).");
        }
        
        // Get the request body as a string
        $request_body_string = file_get_contents("php://input");
        
        // Decode the JSON request data into an array
        $request_data = json_decode($request_body_string, true);
    
        // Update the reservation data if any updates were included in the request data
        if (isset($request_data["from_date"])) {
            $from_date = strip_tags(addslashes(validate_string($request_data["from_date"])));
        
            // Validate the length of the from_date string
            if (strlen($from_date) > 255) {
                error_function(400, "The from_date is too long. Please enter less than 255 letters.");
            }
        
            // Update the from_date value in the reservation data
            $reservation["from_date"] = $from_date;
        }
    
        if (isset($request_data["to_date"])) {
            $to_date = strip_tags(addslashes(validate_string($request_data["to_date"])));
        
            // Validate the length of the to_date string
            if (strlen($to_date) > 500) {
                error_function(400, "The to_date is too long. Please enter less than 500 letters.");
            }
        
            // Update the to_date value in the reservation data
            $reservation["to_date"] = $to_date;
        }
    
        if (isset($request_data["place_name"])) {
            $place_name = strip_tags(addslashes(validate_string($request_data["place_name"])));
        
            // Validate the length of the place_name string
            if (strlen($place_name) > 1000) {
                error_funciton(400, "The place_name is too long. Please enter less than 1000 letters.");
            }
        
            // Update the place_name value in the reservation data
            $reservation["place_name"] = $place_name;
        }
    
        if (isset($request_data["host"])) {
            $host = strip_tags(addslashes(validate_string($request_data["host"])));
        
            // Validate the length of the host string
            if (strlen($host) > 1000) {
                error_funciton(400, "The host is too long. Please enter less than 1000 letters.");
            }
        
            // Update the host value in the reservation data
            $reservation["host"] = $host;
        }
        
        //// Validate the length of the host string and the description feld
        if (isset($request_data["description"])) {
            $description = strip_tags(addslashes(validate_string($request_data["description"])));  
		
			if (strlen($description) > 1000) {
				error_function(400, "The description is too long. Please enter less than 1000 letters.");
			}
		
			$reservation["description"] = $description;
		}
        
        //Send the data if all feld are true, else false
		if (update_reservation($id, $reservation["from_date"], $reservation["to_date"], $reservation["place_name"], $reservation["host"], $reservation["description"], $email)) {
			message_function(200, "The reservation data were successfully updated");
		}
		else {
			error_function(500, "An error occurred while saving the reservation data.");
		}
		
		return $response;
	});


    $app->delete("/Reservation/{id}", function (Request $request, Response $response, $args) {
        // Validate the token to make sure the user is authorized to access this route.
        validate_token();
    
        // Get the ID of the reservation to be deleted from the route parameters and validate it as a number.
        $id = validate_number($args["id"]);
    
        // Attempt to delete the reservation from the database.
        $result = delete_reservation($id);
    
        // If the deletion was not successful, return a 404 error.
        if (!$result) {
            error_function(404, "No reservation found for the id " . $id . ".");
        }
        // If the deletion was successful, return a 200 status code with a success message.
        else {
            message_function(200, "The reservation was successfully deleted.");
        }
    
        // Return the response.
        return $response;
    });    
?>
