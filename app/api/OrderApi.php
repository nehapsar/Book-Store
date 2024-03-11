<?php

use App\controllers;
use App\Middleware\AuthenticateAdmin;
use App\Middleware\AuthenticateUser;
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
    ],
];

$app = new App($configuration);

$app->group('/authenticate' , function ($app){
    
    $app->get('/order',function (Request $request,Response $response){
        $parsedBody = $request->getParsedBody();
        $viewAllItems = new controllers\OrderController();
        $result = $viewAllItems->viewOrderList($parsedBody);
        return $response->withStatus(200)->withJson($result);
    });

    $app->get('/order/{id}' , function (Request $request,Response $response){
        $id = $request->getAttribute(id);
        $viewOrderDetails =new controllers\OrderController();
        $result = $viewOrderDetails->viewOrderDetails($id);
        return $response->withStatus(200)->withJson($result);
    });

})->add(new AuthenticateAdmin());


$app->group('/authenticates' , function ($app){

    $app->get('/order',function (Request $request,Response $response){
        $parsedBody = $request->getParsedBody();
        $viewAllItems = new controllers\OrderController();
        $result = $viewAllItems->viewOrderList($parsedBody);
        return $response->withStatus(200)->withJson($result);
    });

    $app->get('/order/{id}' , function (Request $request,Response $response){
        $id = $request->getAttribute('id');
        $viewOrderDetails =new controllers\OrderController();
        $result = $viewOrderDetails->viewOrderDetails($id);
        return $response->withStatus(200)->withJson($result);
    });

    $app->delete('/orders',function (Request $request,Response $response){
        $cartItems = new controllers\OrderController();
        $result = $cartItems->completeOrder();
        return $response->withStatus(200)->withJson($result);
    });

 })->add(new AuthenticateUser());

$app->run();
