<?php

namespace App\controllers;
use App\providers\AddBookProvider;
use App\providers\BookCategoryProvider;
use MongoDB\BSON\ObjectId;
use App\controllers\BookCategoryController;

class AddBookController{

    private $dbObj;
    public function __construct(){
        $this->dbObj = new AddBookProvider();
        $this->dbBookObj = new BookCategoryProvider();
    }

    public function addBooksToCategory($data) {
        $validationResult = $this->isValidData($data);

        if ($validationResult === true) {
            $insertionResult = $this->dbObj->insertOne($data);
            return ["result" => 'book added successfully'];
        } else {
            return $validationResult;
        }
    }

    private function isValidData($data) {
        if (!isset($data['name']) || !preg_match('/^[a-zA-Z]+$/', $data['name'])) {
            return ["message" => "invalid name"];
        } elseif (!isset($data['author']) || !preg_match('/^[a-zA-Z]+$/', $data['author'])) {
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

   /* public function searchBookWithAlphabet($data){
        $char_to_match = $data;
        $regex_pattern = "/^" . preg_quote($char_to_match, '/') . "/i";
        $searchArray = ['name' => ['$regex' => $regex_pattern, '$options' => 'i']];
        $result = $this->dbObj->findOne($searchArray);
        return $result;
    }*/

    public  function findBookWithCategoryName($id){
        $searchArray = ['_id'=> new ObjectId($id)];
        $result = $this->dbObj->findOne($searchArray);
        $ouput =[];
        $ouput["name"]=$result['name'];
        $ouput["author"]  =$result['"author"'];
        $ouput["quantity"]=$result['quantity'];
        $ouput["season_start_date"]=$result['season_start_date'];
        $ouput["season_end_date"]=$result['season_end_date'];
        $categoryId = $result['category_id'];
        $name=$this->findNameofCategory($categoryId);
        $ouput['category_name']=$name;
        return ['result' =>$ouput];
    }

    private function findNameofCategory($categoryId){
        $searchArray = ['_id'=> new ObjectId($categoryId)];
        $result = $this->dbBookObj->findOne($searchArray);
        $name= $result['name'];
        return  $name;
   }
}
