<?php

use App\controllers;
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
    ],
];

$app = new App($configuration);

$app->group('/authenticated', function ($app) {

$app->post('/cart/{id}' ,function (Request $request,Response$response){
    $id = $request->getAttribute('id');
    $parsedBody = $request->getParsedBody();
    $addToCartController = new controllers\CartController();
    $result = $addToCartController->addToCart($id,$parsedBody);
    return $response->withStatus(200)->withJson($result);
});

$app->delete('/cart/{id}', function (Request $request, Response $response){
    $id = $request->getAttribute('id');
    $itemToDelete = new controllers\CartController();
    $result = $itemToDelete->deleteItemFromCart($id);
    return $response->withStatus(200)->withJson($result);
});

$app->get('/cart', function (Request $request, Response $response){
    $viewCartItems = new controllers\CartController();
    $result = $viewCartItems->viewCart();
    return $response->withStatus(200)->withJson($result);
});

$app->get('/carts', function (Request $request, Response $response){
    $veiwStatus = new controllers\CartController();
    $result = $veiwStatus->cartStatus();
    return $response->withStatus(200)->withJson($result);
});
    
//route middleware
$app->get('/checkout', function (Request $request, Response$response){
    $checkOutCart = new controllers\CartController();
    $result = $checkOutCart->checkOut();
    return $response->withStatus(200)->withJson($result);
});

})->add(new \App\Middleware\AuthenticateUser());



$app->get('/cart', function (Request $request, Response $response){
    $viewCartItems = new controllers\CartController();
    $result = $viewCartItems->viewCart();
    return $response->withStatus(200)->withJson($result);
})->add(new \App\Middleware\AuthenticateAdmin());

$app->run();

