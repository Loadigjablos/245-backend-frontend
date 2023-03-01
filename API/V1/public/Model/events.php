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

    function create_reservation($data_time, $place_name, $host, $description) {
        global $database;

        $result = $database->query("INSERT INTO `events` (`data_time`,`place_name`, `host`, `description`) VALUES ('$data_time', '$place_name', '$host', '$description');");

		return true;
    }

    function delete_reservation($place_name) {
		global $database;

		$place_name = intval($place_name);

		$result = $database->query("DELETE FROM `events` WHERE place_name = $place_name;");
        
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