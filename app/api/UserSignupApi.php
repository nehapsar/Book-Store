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

$app->post('/signup' , function (Request $request,Response $response){
    $parsedBody = $request->getParsedBody();
    $data = new \App\controllers\UserSignupController();
    $result = $data->signupDetails($parsedBody);
    return   $response->withStatus(200)->withJson($result);
});

$app->run();
