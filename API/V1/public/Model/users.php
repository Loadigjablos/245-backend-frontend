<?php
    // Database conection string
    require "util/database.php";
 
    function get_all_users() {
        global $database;

        // get all the names from the users table
        $result = $database->query("SELECT name FROM users;");

        // if there's an error, return a 500 status code and error message
        if ($result == false) {
            error_function(500, "Error");
        } 
        // if there are results, return them as an array of associative arrays
        else if ($result !== true) {
            if ($result->num_rows > 0) {
                $result_array = array();
                while ($user = $result->fetch_assoc()) {
                    $result_array[] = $user;
                }
                return $result_array;
            } 
            // if there are no results, return a 404 status code and error message
            else {
                error_function(404, "not Found");
            }
        } 
        // if the result is true, return a 404 status code and error message
        else {
            error_function(404, "not Found");
        }
    }


    function change_player_data($data, $id) {
        global $database;

        // update the player_data for the user with the given id
        $result = $database->query("UPDATE users SET player_data = '$data' WHERE users.id = $id;");

        // if there's an error, return a 500 status code and error message
        if (!$result) {
            error_function(500, "Error");
        }
    }

    function get_user_email($id) {
        global $database;

        // get the email for the user with the given id
        $result = $database->query("SELECT email FROM users WHERE id = '$id';");

        // if there's an error, return a 500 status code and error message
        if ($result == false) {
            error_function(500, "Error");
		} 
        // if there are results, return the first one as an associative array
		else if ($result !== true) {
			if ($result->num_rows > 0) {
                return $result->fetch_assoc();
			} 
            // if there are no results, return a 404 status code and error message
			else {
                error_function(404, "not Found");
            }
		} 
        // if the result is true, return a 404 status code and error message
        else {
            error_function(404, "not Found");
        }

        $result = $result->fetch_assoc();

	    return $result;
    }

    function get_user_by_mail($mail) {
        global $database;

        // get the user with the given email
        $result = $database->query("SELECT * FROM users WHERE email = '$mail';");

        // if there's an error, return a 500 status code and error message
        if ($result == false) {
            error_function(500, "Error");
		} 
        // if there are results, return the first one as an associative array
		else if ($result !== true) {
			if ($result->num_rows > 0) {
                return $result->fetch_assoc();
			} 
            // if there are no results, return a 404 status code and error message
			else {
                error_function(404, "not Found");
            }
		} 
        // if the result is true, return a 404 status code and error message
		else {
            error_function(404, "not Found");
        }
    }
    
    function get_user_type($id) {
        global $database;
    
        // Execute the SQL query
        $result = $database->query("SELECT * FROM users WHERE id = '$id';");

        // Check if the query was successful
        if ($result == false) {
            // If there was an error, call the error_function and pass the appropriate error code and message
            error_function(500, "Error");
        } else if ($result !== true) {
            // If there are results, fetch the user's data and return it
            if ($result->num_rows > 0) {
                return $result->fetch_assoc();
            } else {
                // If there are no results, call the error_function and pass the appropriate error code and message
                error_function(404, "not Found");
            }
        } else {
            // If the query returned true, call the error_function and pass the appropriate error code and message
            error_function(404, "not Found");
        }

        // This line of code will never be executed because the function returns before it
        $result = $result->fetch_assoc();

        // This line of code will cause an error because json_decode expects a string, not an array
        echo json_decode($result);
    }
       
    // This function gets a user's information from the database based on their username.
    function get_user_by_username($name) {
        // We need to access the global variable $database, which holds our database connection.
        global $database;

        // We run a SQL query to select all columns from the "users" table where the "name" column matches the given $name parameter.
        $result = $database->query("SELECT * FROM users WHERE name = '$name';");

        // If the SQL query fails, we call the error_function with a 500 Internal Server Error code and an error message.
        if ($result == false) {
            error_function(500, "Error");
        } 
        // If the SQL query returns a result (i.e. it's not true or false), and the number of rows in the result is greater than 0,
        // we return the first row of the result as an associative array (i.e. a dictionary with column names as keys).
        else if ($result !== true) {
            if ($result->num_rows > 0) {
                return $result->fetch_assoc();
            } 
        } 
        // If the SQL query returns true (i.e. it succeeded but returned no rows), we call the error_function with a 404 Not Found code and an error message.
        else {
            error_function(404, "not Found");
        }
    }

    // Define a function to retrieve user data from the database based on their id
    function get_user_by_id($id) {
        // Access the global database object
        global $database;

        // Query the database to retrieve the user data for the given id
        $result = $database->query("SELECT * FROM users WHERE id = '$id';");

        // Check if there was an error with the query
        if ($result == false) {
            // If there was an error, call the error function with a 500 status code and error message
            error_function(500, "Error");
        } 
        // If there was no error with the query
        else if ($result !== true) {
            // Check if the query returned any rows
            if ($result->num_rows > 0) {
                // If there are rows, fetch the associative array of the first row of results and return it
                return $result->fetch_assoc();
            } 
            // If there are no rows returned by the query
            else {
                // Call the error function with a 404 status code and error message
                error_function(404, "not Found");
            }
        } 
        // If the result is true (i.e. query was successful but returned no rows), call the error function with a 404 status code and error message
        else {
            error_function(404, "not Found");
        }

        // Fetch the associative array of the first row of results (this line will never be reached due to the if/else conditions above)
        $result = $result->fetch_assoc();

        // Output the user data in JSON format (this line will also never be reached due to the if/else conditions above)
        echo json_decode($result);
    }

    /**
     * Retrieves the name and type of a user given their ID from the database.
     *
     * @param int $id - the ID of the user to retrieve information for.
     *
     * @return array|void - an associative array containing the name and type of the user, or an error is returned through the error_function().
     */
    function get_user_id($id) {
        global $database;

        // query the database for the name and type of the user with the specified ID
        $result = $database->query("SELECT name, type FROM users WHERE id = '$id';");

        if ($result == false) {
            // if the query failed, return a 500 error
            error_function(500, "Error");
        } else if ($result !== true) {
            if ($result->num_rows > 0) {
                // if there is at least one result, return the first row as an associative array
                return $result->fetch_assoc();
            } else {
                // if there are no results, return a 404 error
                error_function(404, "not Found");
            }
        } else {
            // if the query returned true, return a 404 error
            error_function(404, "not Found");
        }

        // fetch the result as an associative array and output it as JSON (this will not be reached if an error was returned earlier)
        $result = $result->fetch_assoc();

        echo json_decode($result);
    }


    /**
     * Retrieves a skill from the database by its ID.
     * @param int $id The ID of the skill to retrieve.
     * @return array|void Returns an associative array containing the skill's data if found, or calls an error function if not found or if there was an error retrieving the data.
     */
    function get_skill_by_id($id) {
        global $database;

        // Query the database for the skill with the given ID
        $result = $database->query("SELECT * FROM skills WHERE id = '$id';");

        // Check if there was an error retrieving the data from the database
        if ($result == false) {
            error_function(500, "Error");
        } 
        // If the query was successful and there are results, return the skill's data as an associative array
        else if ($result !== true) {
            if ($result->num_rows > 0) {
                return $result->fetch_assoc();
            } 
            // If there are no results, call an error function
            else {
                error_function(404, "not Found");
            }
        } 
        // If the query was successful but there are no results, call an error function
        else {
            error_function(404, "not Found");
        }
    }


    /**
     * Creates a new user with the given parameters.
     *
     * @param string $name The name of the new user.
     * @param string $email The email address of the new user.
     * @param string $password The hashed password of the new user.
     * @param string $type The type of user to create (e.g. "admin", "regular").
     * @param string $add_date The date the user was added in YYYY-MM-DD format.
     *
     * @return boolean Returns true if the user was created successfully, false otherwise.
     */
    function create_user($name, $email, $password, $type, $add_date) {
        global $database;

        // Check if a user with the same name already exists
        $existing_user = $database->query("SELECT * FROM `users` WHERE `name` = '$name'")->fetch_assoc();
        if ($existing_user) {
            // Handle error
            error_function(400, "A user with the name '$name' already exists.");
            return false;
        }

        // Insert the new user into the database
        $result = $database->query("INSERT INTO `users` (`name`,`email`, `password_hash`, `type`, `add_date`) VALUES ('$name', '$email', '$password', '$type', '$add_date');");

        if ($result) {
            // User created successfully
            return true;
        }
        else {
            // There was an error creating the user
            return false;
        }
    }

    /**
     * Update user information in the database.
     *
     * @param int    $user_id  The ID of the user to update.
     * @param string $name     The new name for the user.
     * @param string $email    The new email for the user.
     * @param string $password The new password hash for the user.
     * @param int    $type     The new type for the user.
     * @param string $add_date The new date for when the user was added.
     *
     * @return bool            True if the update was successful, false otherwise.
     */
    function update_user($user_id, $name, $email, $password, $type, $add_date) {
        global $database;

        // Execute the SQL query to update the user's information in the database.
        $result = $database->query("UPDATE `users` SET name = '$name', email = '$email', password_hash = '$password', type = '$type', add_date = '$add_date' WHERE id = '$user_id';");

        // If the query was unsuccessful, return false.
        if (!$result) {
            return false;
        }

        // Otherwise, return true to indicate a successful update.
        return true;
    }

    /**
     * Deletes the user with the specified name from the database
     * 
     * @param string $name The name of the user to be deleted
     * @return boolean|null Returns true if the user was successfully deleted, null if no user with the specified name was found, and false if an error occurred during the deletion
     */
    function delete_user($name) {
        global $database;
        
        // Execute the delete query
        $result = $database->query("DELETE FROM `users` WHERE name = '$name';");

        // Check if the query was successful
        if (!$result) {
            // An error occurred during the deletion
            return false;
        } else if ($database->affected_rows == 0) {
            // No user with the specified name was found
            return null;
        } else {
            // The user was successfully deleted
            return true;
        }
    }
?>
