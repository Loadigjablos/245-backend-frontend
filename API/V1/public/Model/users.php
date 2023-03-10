<?php
    // Database conection string
    require "util/database.php";
 
    function get_all_users() {
        global $database;

        $result = $database->query("SELECT name FROM users;");

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


    function change_player_data($data, $id) {
        global $database;

        $result = $database->query("UPDATE users SET player_data = '$data' WHERE users.id = $id;");

        if (!$result) {
            error_function(500, "Error");
        }
    }

    function get_user_by_mail($mail) {
        global $database;

        $result = $database->query("SELECT * FROM users WHERE email = '$mail';");

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
    
    function get_user_type($id) {
        global $database;
    
        $result = $database->query("SELECT type FROM users WHERE id = '$id';");
    
        if ($result == false) {
            error_function(500, "Error");
        } else if ($result !== true) {
            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                return $user['type'];
            } else {
                error_function(404, "not Found");
            }
        } else {
            error_function(404, "not Found");
        }
    }
       
    function get_user_by_username($name) {
        global $database;

        $result = $database->query("SELECT * FROM users WHERE name = '$name';");

        if ($result == false) {
            error_function(500, "Error");
		} 
        else if ($result !== true) {
			if ($result->num_rows > 0) {
                return $result->fetch_assoc();
			} 
		} 
        else {
            error_function(404, "not Found");
        }
    }

    function get_user_by_id($id) {
        global $database;

        $result = $database->query("SELECT * FROM users WHERE id = '$id';");

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

        $result = $result->fetch_assoc();

	    echo json_decode($result);
    }

    function get_user_id($id) {
        global $database;

        $result = $database->query("SELECT name, type FROM users WHERE id = '$id';");

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

        $result = $result->fetch_assoc();

	    echo json_decode($result);
    }

    function get_skill_by_id($id) {
        global $database;

        $result = $database->query("SELECT * FROM skills WHERE id = '$id';");

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

    function create_user($name, $email, $password, $type, $add_date) {
        global $database;

        $existing_place = $database->query("SELECT * FROM `users` WHERE `name` = '$name'")->fetch_assoc();
        if ($existing_place) {
            // handle error
            error_function(400, "A place with the name '$name' already exists.");
            return false;
        }

        $result = $database->query("INSERT INTO `users` (`name`,`email`, `password_hash`, `type`, `add_date`) VALUES ('$name', '$email', '$password', '$type', '$add_date');");

        if ($result) {
            return true;
        }
        else {
            return false;
        }
    }

    function update_user($user_id, $name, $email, $password, $type, $add_date) {
		global $database;

		$result = $database->query("UPDATE `users` SET name = '$name', email = '$email', password_hash = '$password', type = '$type', add_date = '$add_date' WHERE id = '$user_id';");

		if (!$result) {
			return false;
		}
		
		return true;
	}

    function update_product($product_id, $name, $active, $sku, $category_id, $image, $description, $price, $stock) {
		global $database;

		$result = $database->query("UPDATE `product` SET name = '$name', active = $active, sku = '$sku', category_id = $category_id, image = '$image', description = '$description', price = $price, stock = $stock WHERE product_id = $product_id");

		if (!$result) {
			return false;
		}
		
		return true;
	}

    function delete_user($name) {
		global $database;
		
		$result = $database->query("DELETE FROM `users` WHERE name = '$name';");
        
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
