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

        $result = $database->query("INSERT INTO `places` (`position`,`name`, `type`) VALUES ('$position', '$name', '$type');");

		return true;
    }

    function update_place($place_name, $position, $name, $type) {
		global $database;

		$result = $database->query("UPDATE `places` SET position = '$position', name = '$name', type = '$type' WHERE name = '$place_name';");

		if (!$result) {
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
