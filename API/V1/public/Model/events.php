<?php
    // Database conection string
    require "util/database.php";

<<<<<<< HEAD
    //get reservationdata using place name from database
=======
>>>>>>> d90ee1b5d59cabd298a3044f520b353f53884c19
    function get_reservation_by_name($place_name) {
        global $database;

        $result = $database->query("SELECT * FROM events WHERE place_name = '$place_name';");

        if ($result == false) {
            error_function(500, "Error");
		} else if ($result !== true) {
			if ($result->num_rows > 0) {
                return $result->fetch_assoc();
			} else {
                error_function(404, "not Found");
            }
		} else {
            error_function(404, "not Found");
        }
    }

<<<<<<< HEAD
    //get reservationdata using id from database
=======
>>>>>>> d90ee1b5d59cabd298a3044f520b353f53884c19
    function get_reservation_by_id($id) {
        global $database;

        $result = $database->query("SELECT * FROM events WHERE id = '$id';");

        if ($result == false) {
            error_function(500, "Error");
		} else if ($result !== true) {
			if ($result->num_rows > 0) {
                return $result->fetch_assoc();
			} else {
                error_function(404, "not Found");
            }
		} else {
            error_function(404, "not Found");
        }
    }

<<<<<<< HEAD
    //get all reservationsdata
=======
>>>>>>> d90ee1b5d59cabd298a3044f520b353f53884c19
    function get_all_reservations() {
        global $database;

        $result = $database->query("SELECT * FROM events;");

        if ($result == false) {
            error_function(500, "Error");
        } else if ($result !== true) {
            if ($result->num_rows > 0) {
                $result_array = array();
                while ($user = $result->fetch_assoc()) {
                    $result_array[] = $user;
                }
                return $result_array;
            } else {
                error_function(404, "not Found");
            }
        } else {
            error_function(404, "not Found");
        }
    }

<<<<<<< HEAD
    //create new reservation and send mail
    function create_reservation($from_date, $to_date, $place_name, $host, $description, $email) {
=======
    function create_reservation($from_date, $to_date, $place_name, $host, $description) {
>>>>>>> d90ee1b5d59cabd298a3044f520b353f53884c19
        global $database;
        
        // Check if place_name already exists
        $result = $database->prepare("SELECT COUNT(*) FROM `events` WHERE `place_name` = ? AND `to_date` > ?");
        $result->bind_param("ss", $place_name, $from_date);
        $result->execute();
        $result = $result->get_result()->fetch_row()[0];
        if ($result > 0) {
            // place_name already exists, return false
            error_function(400, "It's look like someone booked ( " . $place_name . " ) before you.");        
        }
        
        // Insert new reservation
        $result = $database->prepare("INSERT INTO `events` (`from_date`, `to_date`, `place_name`, `host`, `description`) VALUES (?, ?, ?, ?, ?)");
        $result->bind_param("sssss", $from_date, $to_date, $place_name, $host, $description);
        $result = $result->execute();
        
        if (!$result) {
            // handle error
            return false;
        }
                
        // Convert date and time to UTC format
        $from_date_utc = gmdate('Ymd\THis\Z', strtotime($from_date));
        $to_date_utc = gmdate('Ymd\THis\Z', strtotime($to_date));
        
        // Generate unique ID for the event
        $uid = uniqid();
        
        // Generate .ics file contents
<<<<<<< HEAD
        $ical = "BEGIN:VCALENDAR
VERSION:2.0
=======
        $ical = "BEGIN:VCALENDAR\nVERSION:2.0
>>>>>>> d90ee1b5d59cabd298a3044f520b353f53884c19
PRODID:-//hacksw/handcal//NONSGML v1.0//EN
BEGIN:VEVENT
UID:" . $uid . "
DTSTAMP:" . $from_date_utc . "
DTSTART:" . $from_date_utc . "
DTEND:" . $to_date_utc . "
SUMMARY:" . $place_name . "
END:VEVENT
END:VCALENDAR";

<<<<<<< HEAD
        // Generate reservation details for email body
        $reservation_details = "Reservation Details:\r\n\r\n";
        $reservation_details .= "Place Name: " . $place_name . "\r\n\n";
        $reservation_details .= "From Date: " . $from_date . "\r\n\n";
        $reservation_details .= "To Date: " . $to_date . "\r\n\n";

        // Generate email body with reservation details and .ics file attachment
        $boundary = md5(time());
=======
        // Send email with .ics file contents as the email body
        $to = "mouayad.alnhlawe@ict.csbe.ch";
        $subject = "Event Reservation";
        $message = "Please find below the event reservation details in iCalendar format:\r\n\r\n";
>>>>>>> d90ee1b5d59cabd298a3044f520b353f53884c19
        $headers = "From: morhaf.mouayad@gmail.com\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/calendar; charset=utf-8; method=REQUEST\r\n";
        $body = $message . $ical;
        
        if (!mail($to, $subject, $body, $headers, "-r morhaf.mouayad@gmail.com")) {
            error_function(400, "Email sending failed, but the reservation was seccussfully created.");
        }
<<<<<<< HEAD

        // Send email with reservation details and .ics file attachment to sekretariat
        $to = "dominic.streit@ict.csbe.ch";
        $subject = "New Reservation";
        if (!mail($to, $subject, $body, $headers, "-r morhaf.mouayad@gmail.com")) {
            error_function(400, "Email sending failed, but the reservation was successfully created.");
        }

=======
        
>>>>>>> d90ee1b5d59cabd298a3044f520b353f53884c19
        return true;
    }
    
    
        
    function update_reservation($id, $from_date, $to_date, $place_name, $host, $description) {
        global $database;

        $result = $database->query("UPDATE `events` SET from_date = '$from_date', to_date = '$to_date', place_name = '$place_name', host = '$host', description = '$description' WHERE id = '$id';");

        if (!$result) {
            return false;
        }
<<<<<<< HEAD

        // Convert date and time to UTC format
        $from_date_utc = gmdate('Ymd\THis\Z', strtotime($from_date));
        $to_date_utc = gmdate('Ymd\THis\Z', strtotime($to_date));

        // Generate unique ID for the event
        $uid = uniqid();

        // Generate .ics file contents
        $ical = "BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//hacksw/handcal//NONSGML v1.0//EN
BEGIN:VEVENT
UID:" . $uid . "
DTSTAMP:" . $from_date_utc . "
DTSTART:" . $from_date_utc . "
DTEND:" . $to_date_utc . "
SUMMARY:" . $place_name . "
END:VEVENT
END:VCALENDAR";

        // Generate reservation details for email body
        $reservation_details = "Reservation Details:\r\n\r\n";
        $reservation_details .= "Place Name: " . $place_name . "\r\n\n";
        $reservation_details .= "From Date: " . $from_date . "\r\n\n";
        $reservation_details .= "To Date: " . $to_date . "\r\n\n";

        // Generate email body with reservation details and .ics file attachment
        $boundary = md5(time());
        $headers = "From: morhaf.mouayad@gmail.com\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: multipart/mixed; boundary=" . $boundary . "\r\n\r\n";
        $body = "--" . $boundary . "\r\n";
        $body .= "Content-Type: text/plain; charset=ISO-8859-1\r\n";
        $body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
        $body .= $reservation_details . "\r\n\r\n";
        $body .= "--" . $boundary . "\r\n";
        $body .= "Content-Type: text/calendar; charset=utf-8; method=REQUEST; name=reservation.ics\r\n";
        $body .= "Content-Disposition: attachment; filename=reservation.ics\r\n";
        $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $body .= chunk_split(base64_encode($ical)) . "\r\n\r\n";
        $body .= "--" . $boundary . "--";

        // Send email with reservation details and .ics file attachment
        $to = $email;
        $subject = "Your Reservation";
        if (!mail($to, $subject, $body, $headers, "-r morhaf.mouayad@gmail.com")) {
            error_function(400, "Email sending failed, but the reservation was successfully updated.");
        }

        // Send email with reservation details and .ics file attachment to sekretariat
        $to = "dominic.streit@ict.csbe.ch";
        $subject = "New Reservation";
        if (!mail($to, $subject, $body, $headers, "-r morhaf.mouayad@gmail.com")) {
            error_function(400, "Email sending failed, but the reservation was successfully created.");
        }
=======
>>>>>>> d90ee1b5d59cabd298a3044f520b353f53884c19
        
        return true;
    }
    
<<<<<<< HEAD
    //delete a reservation using id
=======
>>>>>>> d90ee1b5d59cabd298a3044f520b353f53884c19
    function delete_reservation($id) {
        global $database;
    
        $result = $database->query("DELETE FROM `events` WHERE id = '$id';");
            
        if (!$result) {
            return false;
        }
        else if ($database->affected_rows == 0) {
            return null;
        }
        else {
            return true;
        }
    }
    


?>