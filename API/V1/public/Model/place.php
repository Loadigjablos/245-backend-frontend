<?php
    // Database conection string
    require "util/database.php";

    function get_room($place_name) {
        global $database;

        $result = $database->query("SELECT * FROM places where name = '$place_name';");

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

    function create_place($position, $name, $type) {
        global $database;
    
        // check if a place with the same name already exists
        $existing_place = $database->query("SELECT * FROM `places` WHERE `name` = '$name'")->fetch_assoc();
        if ($existing_place) {
            // handle error
            error_function(400, "A place with the name '$name' already exists.");
            return false;
        }

        // check if a place with the same position already exists
        $existing_place = $database->query("SELECT * FROM `places` WHERE `position` = '$position'")->fetch_assoc();
        if ($existing_place) {
            // handle error
            error_function(400, "A place with the position '$position' already exists.");
            return false;
        }
    
        $result = $database->query("INSERT INTO `places` (`position`,`name`, `type`) VALUES ('$position', '$name', '$type');");
    
        if (!$result) {
            // handle error
            error_function(400, "An error occurred while creating the place.");
            return false;
        }
    
        return true;
    }    

	function get_all_places() {
        global $database;

        $result = $database->query("SELECT * FROM places;");

        if ($result == false) {
            error_function(500, "Error");
        } else if ($result !== true) {
            if ($result->num_rows > 0) {
                $result_array = array();
                while ($places = $result->fetch_assoc()) {
                    $result_array[] = $places;
                }
                return $result_array;
            } else {
                error_function(404, "not Found");
            }
        } else {
            error_function(404, "not Found");
        }
    }

    function delete_place($place_name) {
		global $database;
		
		$result = $database->query("DELETE FROM `places` WHERE name = '$place_name';");
        
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
