<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
    ],
];

$app = new App($configuration);

$app->post('/login' ,function (Request $request,Response $response){
    $parsedBody = $request->getParsedBody();
    $loginObj = new \App\controllers\AdminLoginController();
    $result = $loginObj->login($parsedBody);
    return $response->withStatus(200)->withJson($result);
});

$app->run();
