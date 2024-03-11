<?php

use App\controllers\BookCategoryController;
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

$configuration = [
    'settings' => [
        'displayErrorDetails' => true,
    ],
];

$app = new App($configuration);
$app->add(new \App\Middleware\AuthenticateAdmin());

$app->post('/category', function(Request $request, Response $response ){
    $parsedBody = $request->getParsedBody();
    $categoryController = new \App\controllers\BookCategoryController();
    $result = $categoryController->addCategory($parsedBody);
    return $response->withStatus(200)->withJson($result);
});

$app->put('/category/{id}', function (Request $request, Response $response){
    $id = $request->getAttribute('id');
    $parsedBody = $request->getParsedBody();
    $categoryController = new BookCategoryController();
    $result = $categoryController-> updateCategory($id, $parsedBody);
    return $response->withStatus(200)->withJson($result);
});

$app->get('/category/{id}', function (Request $request, Response $response){
    $id = $request->getAttribute('id');
    $categoryController = new BookCategoryController();
    $result = $categoryController->getCategory($id);
    return $response->withStatus(200)->withJson($result);
});

$app->get('/category',function (Request $request,Response $response){
    $getAllCategory = new BookCategoryController();
    $result = $getAllCategory->getCategoryList();
    return $response->withStatus(200)->withJson($result);
});

$app->get('/categories' ,function (Request $request,Response $response){
    $categoryCount = new BookCategoryController();
    $result = $categoryCount->numberOfBooksInEachCategory();
    return $response->withStatus(200)->withJson($result);
});

$app->delete('/category/{id}',function (Request $request,Response $response){
    $id = $request->getAttribute('id');
    $data = $request->getParsedBody();
    $categoryController = new BookCategoryController();
    $result = $categoryController->deleteCategory($id,$data);
    return $response->withStatus(200)->withJson($result);
});

$app->run();

