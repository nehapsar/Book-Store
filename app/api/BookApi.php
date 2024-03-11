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
$app->add(new \App\Middleware\AuthenticateAdmin());

$app->post('/book', function (Request  $request,Response $response){
    $parsedBody = $request->getParsedBody();
    $addController = new \App\controllers\BookController();
    $result = $addController->addBook($parsedBody);
    return $response->withStatus(200)->withJson($result);
});

$app->put('/book/{id}',function (Request $request,Response $response){
    $id =$request->getAttribute('id');
    $parsedBody = $request->getParsedBody();
    $updateAddCategory = new \App\controllers\BookController();
    $result = $updateAddCategory->updateBook($id,$parsedBody);
    return $response->withStatus(200)->withJson($result);
});

$app->get('/books/{id}',function (Request $request,Response $response){
    $id = $request->getAttribute('id');
    $bookInfo = new \App\controllers\BookController();
    $result = $bookInfo->getBookDetails($id);
    return $response->withStatus(200)->withJson($result);
});

$app->get('/books',function (Request $request,Response $response){
    $listOfAllBook = new \App\controllers\BookController();
    $result = $listOfAllBook->getAllBooks();
    return $response->withStatus(200)->withJson($result);
});

$app->delete('/book/{id}' , function (Request $request,Response $response){
   $id = $request->getAttribute('id');
   $deleteBook = new \App\controllers\BookController();
   $result = $deleteBook->deleteBook($id);
   return $response->withStatus(200)->withJson($result);
});

$app->get('/book/search/{char}', function (Request $request, Response $response) {
    $char = $request->getAttribute('char');
    $bookInfo = new \App\controllers\BookController();
    $result = $bookInfo->searchBook($char);
    return $response->withStatus(200)->withJson($result);
});

$app->get('/book/{id}',function (Request $request,Response $response){
    $id = $request->getAttribute('id');
    $bookInfo = new \App\controllers\BookController();
    $result = $bookInfo->getBookCategoryName($id);
    return $response->withStatus(200)->withJson($result);
});

$app->get('/tags/{tag}',function (Request $request,Response $response){
    $data = $request->getAttribute('tag');
    $bookTag = new \App\controllers\BookController();
    $result = $bookTag->searchBooks($data);
    return $response->withStatus(200)->withJson($result);
});

$app->run();
