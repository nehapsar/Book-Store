<?php

    use App\middleware\AuthenticateUser;
    use Slim\App;
    use Psr\Http\Message\ServerRequestInterface;
    use Psr\Http\Message\ResponseInterface;

    $configuration = [
        'settings' => [
            'displayErrorDetails' => true,
        ],
    ];

    $app = new App($configuration);

    $app->post('/login', function (ServerRequestInterface $request, ResponseInterface $response) {
        $parsedBody = $request->getParsedBody();
        $loginUser = new \App\controllers\LoginController();
        $result = $loginUser->login($parsedBody);
        return $response->withStatus(200)->withJson($result);
    });

    $app->run();
