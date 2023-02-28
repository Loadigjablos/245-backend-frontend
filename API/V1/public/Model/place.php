<?php
    // Database conection string
    require "util/database.php";

    function get_room($id) {
        global $database;

        $result = $database->query("SELECT * FROM places where id = $id;");

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

    function update_place($id, $position, $name, $type) {
		global $database;

		$result = $database->query("UPDATE `places` SET position = '$position', name = '$name', type = $type WHERE id = $id");

		if (!$result) {
			return false;
		}
		
		return true;
	}

    function delete_place($id) {
		global $database;

		$id = intval($id);

		$result = $database->query("DELETE FROM `places` WHERE id = $id");
        
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