<?php
    /**
     * @param _string the string that will be processed to safety
     * @return _string either false or a correct string
     */
    function validate_string($_string) {
        $_string = addslashes($_string);
        $_string = strip_tags($_string);
        // a string needs at least one character
        if (!(isset($_string) && !(strlen($_string) < 1) && !(empty($_string)))) {
            return false;
        }
        return $_string;
    }
    /**
     * @param _integer this variable will be turned into a safe integer
     * @return _integer a integer number
     */
    function validate_number($_integer) {
        $_integer = intval($_integer);
        return $_integer;
    }
    /**
     * @param _float this variable will be turned into a safe float
     * @return _float a float number
     */
    function validate_float($_float) {
        $_float = floatval($_float);
        return $_float;
    }
    /**
     * @param _bool changes every value to a boolean
     * @return _bool either true or false
     */
    function validate_boolean($_bool) {
        $_bool = filter_var($_bool, FILTER_VALIDATE_BOOLEAN);
        return $_bool;
    }
    /**
     * prints out a error message and instantly shuts down
     */
    function error_function($status_code, $message) {
        $array = array("error" => $message);
        echo json_encode($array, true);
        http_response_code($status_code);
        die();
    }
    /**
     * prints out a information message and instantly shuts down
     */
    function message_function($status_code, $message) {
        $array = array("information" => $message);
        echo json_encode($array, true);
        http_response_code($status_code);
        die();
    }
    require_once "Controler/Secret.php";
    /**
     * 
     */
    function create_token($mail, $password_hash, $id) {
        global $secret;
        $token = $mail . $secret . $password_hash;
        $token = md5($token);
        $token = $token . "[tr]" . $id;
        return $token;
    }
    /**
     * validates the token in the cookies if it matches with a user.
     */
    function validate_token($token = false) {
        $the_set_token = validate_string($_COOKIE["token"]); // cookie from the browser
        
        if ($the_set_token === false) {
            error_function(403, "no token ;_;");
        }
        if ($token !== false) {
            $the_set_token = $token;
        }

        $token_exploded = explode("[tr]", $the_set_token);

        $user = get_user_by_id($token_exploded[1]); // array of all users

        $user_token = create_token($user["email"], $user["password_hash"], $token_exploded[1]);

        if ($user["ban"] . "" !== "0") {
            error_function(403, "You Are Banned ;_;");
        }
        if ($user_token === $the_set_token) {
            return $token_exploded[1];
        }
        error_function(403, "Authentication Failed ;_;");
    }
?>
