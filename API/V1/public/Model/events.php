<?php
    // Database conection string
    require "util/database.php";

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

    function create_reservation($from_date, $to_date, $place_name, $host, $description) {
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
    
        // Generate .ics file for Outlook export
        $start_time = strtotime($from_date);
        $end_time = strtotime($to_date);
        $now_time = time();
        $filename = 'reservation_' . $now_time . '.ics';
        $ics_content = "BEGIN:VCALENDAR
                        VERSION:2.0
                        BEGIN:VEVENT
                        DTSTART:" . gmdate('Ymd\THis\Z', $start_time) . "
                        DTEND:" . gmdate('Ymd\THis\Z', $end_time) . "
                        SUMMARY:Reservation for $place_name
                        DESCRIPTION:$description
                        LOCATION:$place_name
                        END:VEVENT
                        END:VCALENDAR";
    
        // Send email with .ics file attachment
        $to = "morhaf.mouayad@gmail.com";
        $subject = 'Reservation Confirmation';
        $message = 'Your reservation for ' . $place_name . ' from ' . $from_date . ' to ' . $to_date . ' has been confirmed.';
        $headers = 'From: morhaf.mouayad@gmail.com' . "\r\n" .
            'Reply-To: morhaf.mouayad@gmail.com' . "\r\n" .
            'Content-Type: text/calendar; method=REQUEST' . "\r\n" .
            'charset="UTF-8"'."\r\n" .
            'Content-Disposition: attachment; filename=' . $filename;
    
        $mail_sent = mail($to, $subject, $message, $headers, $ics_content);
    
        if (!$mail_sent) {
            error_function(500, "Failed to send email confirmation.");
        }
        
        return true;
    }
    
        
    function update_reservation($id, $from_date, $to_date, $place_name, $host, $description) {
        global $database;

        $result = $database->query("UPDATE `events` SET from_date = '$from_date', to_date = '$to_date', place_name = '$place_name', host = '$host', description = '$description' WHERE id = '$id';");

        if (!$result) {
            return false;
        }
        
        return true;
    }
    
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