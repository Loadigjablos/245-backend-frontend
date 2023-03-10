<?php
    // Database connection string
    require "util/database.php";

    /**
     * Retrieve a reservation by its place name.
     *
     * @param string $place_name The name of the place to retrieve the reservation for.
     * @return mixed The reservation data if found, or false if an error occurred, or an error message if not found.
     */
    function get_reservation_by_name($place_name) {
        global $database;

        $result = $database->query("SELECT * FROM events WHERE place_name = '$place_name';");

        if ($result == false) {
            // Handle database error
            error_function(500, "Error");
        } else if ($result !== true) {
            if ($result->num_rows > 0) {
                // Reservation found, return data
                return $result->fetch_assoc();
            } else {
                // Reservation not found
                error_function(404, "not Found");
            }
        } else {
            // Reservation not found
            error_function(404, "not Found");
        }
    }

    /**
     * Retrieve a reservation by its ID.
     *
     * @param string $id The ID of the reservation to retrieve.
     * @return mixed The reservation data if found, or false if an error occurred, or an error message if not found.
     */
    function get_reservation_by_id($id) {
        global $database;

        $result = $database->query("SELECT * FROM events WHERE id = '$id';");

        if ($result == false) {
            // Handle database error
            error_function(500, "Error");
        } else if ($result !== true) {
            if ($result->num_rows > 0) {
                // Reservation found, return data
                return $result->fetch_assoc();
            } else {
                // Reservation not found
                error_function(404, "not Found");
            }
        } else {
            // Reservation not found
            error_function(404, "not Found");
        }
    }

    /**
     * Retrieve all reservations.
     *
     * @return mixed An array of reservation data if found, or false if an error occurred, or an error message if not found.
     */
    function get_all_reservations() {
        global $database;

        $result = $database->query("SELECT * FROM events;");

        if ($result == false) {
            // Handle database error
            error_function(500, "Error");
        } else if ($result !== true) {
            if ($result->num_rows > 0) {
                // Reservations found, return array of data
                $result_array = array();
                while ($user = $result->fetch_assoc()) {
                    $result_array[] = $user;
                }
                return $result_array;
            } else {
                // Reservations not found
                error_function(404, "not Found");
            }
        } else {
            // Reservations not found
            error_function(404, "not Found");
        }
    }

    /**
     * Create a new reservation and send an email with details and an .ics file attachment.
     *
     * @param string $from_date The start date and time of the reservation in Y-m-d H:i:s format.
     * @param string $to_date The end date and time of the reservation in Y-m-d H:i:s format.
     * @param string $place_name The name of the place being reserved.
     * @param string $host The name of the person or organization making the reservation.
     * @param string $description A description of the reservation.
     * @param string $email The email address to send the reservation details and .ics file to.
     * @return mixed True if the reservation was created and the email sent successfully, or false
    */
    function create_reservation($from_date, $to_date, $place_name, $host, $description, $email) {
        global $database;
        
        // Check if place_name already exists
        $check_result = $database->prepare("SELECT COUNT(*) FROM `events` WHERE `place_name` = ? AND `to_date` > ?");
        $check_result->bind_param("ss", $place_name, $from_date);
        $check_result->execute();
        $check_result = $check_result->get_result()->fetch_row()[0];
        if ($check_result > 0) {
            // place_name already exists, return false
            error_function(400, "It's look like someone booked ( " . $place_name . " ) before you.");       
        };
        
        // Insert new reservation
        $result = $database->prepare("INSERT INTO `events` (`from_date`, `to_date`, `place_name`, `host`, `description`) VALUES (?, ?, ?, ?, ?)");
        $result->bind_param("sssss", $from_date, $to_date, $place_name, $host, $description);
        $result = $result->execute();
        
        if (!$result) {
            // handle error
            return false;
        };
                
        // Convert date and time to UTC format
        $from_date_utc = gmdate('Ymd\THis\Z', strtotime($from_date));
        $to_date_utc = gmdate('Ymd\THis\Z', strtotime($to_date));

        // Generate unique ID for the event
        $uid = uniqid();

        // Generate .ics file contents
        //The source of that is: https://icalendar.org/iCalendar-RFC-5545/3-4-icalendar-object.html
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
        $reservation_details .= "Description: " . $description . "\r\n\n";

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
            error_function(400, "Email sending failed, but the reservation was successfully created.");
        }

        $to = "dominic.streit@ict.csbe.ch";
        $subject = "New Reservation";
        if (!mail($to, $subject, $body, $headers, "-r morhaf.mouayad@gmail.com")) {
            error_function(400, "Email sending failed, but the reservation was successfully created.");
        }

        return true;
    }
    
    function update_reservation($id, $from_date, $to_date, $place_name, $host, $description, $email) {
        global $database;

        $result = $database->query("UPDATE `events` SET from_date = '$from_date', to_date = '$to_date', place_name = '$place_name', host = '$host', description = '$description' WHERE id = '$id';");

        if (!$result) {
            return false;
        }

        // Convert date and time to UTC format
        $from_date_utc = gmdate('Ymd\THis\Z', strtotime($from_date));
        $to_date_utc = gmdate('Ymd\THis\Z', strtotime($to_date));

        // Generate unique ID for the event
        $uid = uniqid();

        // Generate .ics file contents
        //The source is: https://icalendar.org/iCalendar-RFC-5545/3-4-icalendar-object.html
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

        return true;
        
        return true;
    }
    
    /**
     * Deletes a reservation from the `events` table by its ID.
     * @param int $id The ID of the reservation to delete.
     * @return bool|null Returns true if the reservation was deleted, null if no reservation was found with the given ID, or false if there was an error deleting the reservation.
     */
    function delete_reservation($id) {
        global $database; // Access the global database object.

        // Delete the reservation with the given ID from the `events` table.
        $result = $database->query("DELETE FROM `events` WHERE id = '$id';");

        // Check if there was an error deleting the reservation.
        if (!$result) {
            return false; // Return false if there was an error.
        }
        // Check if no rows were affected by the delete query, meaning no reservation was found with the given ID.
        else if ($database->affected_rows == 0) {
            return null; // Return null if no reservation was found with the given ID.
        }
        else {
            return true; // Return true if the reservation was successfully deleted.
        }
    }
?>