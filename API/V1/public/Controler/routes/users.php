<?php
    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;

    $app->get("/Users", function (Request $request, Response $response, $args) {
        $id = user_validation("A");
        validate_token(); // unotherized pepole will get rejected

        $users = get_all_users();

        if ($users) {
            echo json_encode($users);
        }
        else if (is_string($users)) {
            error($users, 500);
        }
        else {
            error("The ID "  . $id . " was not found.", 404);
        }

        return $response;
    });
    
?>