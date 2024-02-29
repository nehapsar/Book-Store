<?php

namespace App\controllers;
use App\providers\AddBookProvider;
use App\providers\BookCategoryProvider;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\Regex;
use App\controllers\BookCategoryController;

class AddBookController{

    private $dbObj;

    public function __construct(){
        $this->dbObj = new AddBookProvider();
        $this->dbBookObj = new BookCategoryProvider();
    }

    public function addBooksToCategory($data){
       $searchArray=['_id'=>new ObjectId( $data['category_id'])];
        $result = $this->dbBookObj->findOne($searchArray);
        if (!$result) {
            return ["message" => "category doesnot exist"];
        }

        $validationResult = $this->isValidData($data);
        if ($validationResult === true) {
              $insertionResult = $this->dbObj->insertOne($data);
              return ["result" => 'book added successfully'];
         } else {
                return $validationResult;
            }
    }

    private function isValidData($data) {
        if (!isset($data['name']) || !preg_match('/^[a-zA-Z\s]+$/', $data['name'])) {
            return ["message" => "invalid name"];
        } elseif (!isset($data['author']) || !preg_match('/^[a-zA-Z\s]+$/', $data['author'])) {
            return ["message" => "invalid author name"];
        } elseif (!isset($data['price']) || !is_int($data['price']) || $data['price'] == 0) {
            return ["message" => "invalid price"];
        } elseif (!isset($data['season_price']) || !is_int($data['season_price']) || $data['season_price'] == 0) {
            return ["message" => "invalid season price"];
        }
        date_default_timezone_set('Asia/Kolkata');
        $todayDateTimeIndia = date('Y-m-d');
        $seasonStartDate = $data['season_start_date'];
        $seasonEndDate = $data['season_end_date'];
        $todayTimestamp = strtotime($todayDateTimeIndia);
        $seasonStartTimestamp = strtotime($seasonStartDate);
        $seasonEndTimestamp = strtotime($seasonEndDate);
        if ($seasonStartTimestamp < $todayTimestamp) {
            return ["message" => "invalid season start date date"];
        } elseif ($seasonEndTimestamp < $todayTimestamp) {
            return ["message" => "invalid season end date"];
        } elseif (!isset($data['quantity']) || !is_int($data['quantity']) || $data['quantity'] < 1) {
            return ["message" => "invalid quantity"];
        }
        return true;
     }

    public function updateBookInformation($id, $data) {
         $searchArray = ["_id" => new ObjectId($id)];
         $updateArray = ['$set' => $data];
         $result = $this->dbObj->updateOne($searchArray, $updateArray);
         if ($result) {
             return ["result" => 'book updated successfully'];
         } else {
             return ["message" => "Failed to update book"];
           }
    }

    public function book_details($id){
        $searchArray = ['_id' => new ObjectId($id)];
        return $this->dbObj->findOne($searchArray);
    }

    public function listOfAllBooks($searchArray = [], $projection = []){
        return $this->dbObj->find($searchArray, $projection);
    }

    public function deleteBook($id){
        $searchArray = ['_id' => new ObjectId($id)];
        $result = $this->dbObj->deleteOne($searchArray);
        return ["result" => $result];
    }
  
    public function searchBookWithAlphabet($data){
        $searchArray = ["name" => ['$regex' => $data] ];
        $result = $this->dbObj->find($searchArray);
        return $result;
    }
    
    public  function findBookWithCategoryName($id){
        $searchArray = ['_id'=> new ObjectId($id)];
        $result = $this->dbObj->findOne($searchArray);
        $categoryId = $result['category_id'];
        $name=$this->findNameofCategory($categoryId);
        $output=['name'=>$result['name'],"author"=>$result['author'],"quantity" => $result['quantity'],"category"=>$name,"seson_start_date"=>$result['season_start_date'],"season_end_date"=>$result['season_end_date']];
     return $output;
    }

    private function findNameofCategory($categoryId){
        $searchArray = ['_id'=> new ObjectId($categoryId)];
        $result = $this->dbBookObj->findOne($searchArray);
        $name= $result['name'];
        return  $name;
     }
}



