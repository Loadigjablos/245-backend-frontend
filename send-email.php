<?php
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
        } elseif (strlen($to_date) > 2048) {
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
    
        // create the reservation and get the result
        $reservation_result = create_reservation($from_date, $to_date, $place_name, $host, $description);
    
        // if reservation is created successfully
        if ($reservation_result === true) {
            // create outlook calendar event
            $event = create_outlook_event($from_date, $to_date, $place_name, $host, $description);
    
            // send email with outlook event attachment
            $to_email = $host; // use host email as the recipient
            $subject = "Reservation for " . $place_name;
            $body = "Your reservation for " . $place_name . " has been created.";
            $attachment_filename = "reservation.ics";
            send_email_with_attachment($to_email, $subject, $body, $attachment_filename, $event);
    
            // send success message
            message_function(200, "The reservation was successfully created and emailed to " . $host . ".");
        } else {
            error_function(500, "An error occurred while saving the reservation.");
        }
    
        return $response;
    });
    
?>