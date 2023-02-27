<?php
	ini_set('display_errors', '1');
	ini_set('display_startup_errors', '1');
	error_reporting(E_ALL);

	use Psr\Http\Message\ResponseInterface as Response;
	use Psr\Http\Message\ServerRequestInterface as Request;
	use Slim\Factory\AppFactory;
	use ReallySimpleJWT\Token;

	require __DIR__ . "/../vendor/autoload.php";

	require "util/database.php";

	header("Content-Type: application/json");

	$app = AppFactory::create();
	
	$app->setBasePath("/API/V1");

	/**
	 * @OA\Post(
	 *     path="/Authenticate",
	 *     summary="Checks the provided username and password and returns an access token if they are valid. The access token is saved in the cookies.",
	 *     tags={"Authentication"},
	 *     requestBody=@OA\RequestBody(
	 *         request="/Authenticate",
	 *         required=true,
	 *         description="Username and password",
	 *         @OA\MediaType(
	 *             mediaType="application/json",
	 *             @OA\Schema(
	 *                 @OA\Property(property="username", type="string", example="root"),
	 *                 @OA\Property(property="password", type="string", example="sUP3R53CR3T#")
	 *             )
	 *         )
	 *     ),
	 *     @OA\Response(response="200", description="Success")
	 * )
	 */
	$app->post("/Login1", function (Request $request, Response $response, $args) {
		global $database;
		$data = json_decode(file_get_contents("php://input"), true);
	
		// Return a 400 response if no category information was provided in the request body.
		if (!$data) {
			http_response_code(400);
			die("Please provide the category information as a correct JSON object in the request body.");
		}
	
		// Make sure the required fields are provided.
		if (!isset($data["name"]) || !isset($data["password"])) {
			http_response_code(400);
			die("You must provide the attributes \"name\" and \"password\".");
		}
	
		// Check if the username and password are valid.
		$result = $database->query("SELECT * FROM users WHERE name='" . $data["name"] . "' AND password_hash='" . $data["password"] . "'");
	
		// Return a 500 response with error message if the query fails or no matching user found.
		if (!$result || mysqli_num_rows($result) == 0) {
			http_response_code(500);
			die(json_encode(array("error" => "Incorrect username or password.")));
		}
	
		// Generate an access token.
		$token = bin2hex(random_bytes(16));
	
		// Store the access token in a cookie that expires in 1 hour.
		setcookie("access_token", $token, time() + 3600, "/");
	
	
		
	});
	

	require "routes/product.php";
	require "routes/category.php";
	$app->run();
?>
