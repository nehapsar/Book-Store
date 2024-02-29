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

$app->post('/book', function (Request  $request,Response $response){
    $parsedBody = $request->getParsedBody();
    $addController = new \App\controllers\AddBookController();
    $result = $addController->addBooksToCategory($parsedBody);
    return $response->withStatus(200)->withJson($result);
});

$app->put('/book/{id}',function (Request $request,Response $response){
    $id =$request->getAttribute('id');
    $parsedBody = $request->getParsedBody();
    $updateAddCategory = new \App\controllers\AddBookController();
    $result = $updateAddCategory->updateBookInformation($id,$parsedBody);
    return $response->withStatus(200)->withJson($result);
});

$app->get('/book/{id}',function (Request $request,Response $response){
    $id = $request->getAttribute('id');
    $bookInfo = new \App\controllers\AddBookController();
    $result = $bookInfo->book_details($id);
    return $response->withStatus(200)->withJson($result);
});

$app->get('/book',function (Request $request,Response $response){
    $listOfAllBook = new \App\controllers\AddBookController();
    $result = $listOfAllBook->listOfAllBooks();
    return $response->withStatus(200)->withJson($result);
});

$app->delete('/book/{id}' , function (Request $request,Response $response){
   $id = $request->getAttribute('id');
   $deleteBook = new \App\controllers\AddBookController();
   $result = $deleteBook->deleteBook($id);
   return $response->withStatus(200)->withJson($result);
});

$app->get('/books/{char}', function (Request $request, Response $response) {
    $char = $request->getAttribute('char');
    $bookInfo = new \App\controllers\AddBookController();
    $result = $bookInfo->searchBookWithAlphabet($char);
    return $response->withStatus(200)->withJson($result);
});

$app->get('/books/{id}',function (Request $request,Response $response){
    $id = $request->getAttribute('id');
    $bookInfo = new \App\controllers\AddBookController();
    $result = $bookInfo->findBookWithCategoryName($id);
    return $response->withStatus(200)->withJson($result);
});

$app->run();
