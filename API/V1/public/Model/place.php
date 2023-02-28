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

?>