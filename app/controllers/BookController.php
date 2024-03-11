<?php

namespace App\controllers;

use App\providers\BookCategoryProvider;
use App\providers\BooksProvider;
use App\validators\BookValidator;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\Regex;


class BookController{

    public function __construct(){
        $this->dbBookObj = new BooksProvider();
        $this->dbBookCateObj = new BookCategoryProvider();
    }

    public function addBook($data){
        $validator = new BookValidator($data,'add');
        $validator->validate();

        $searchArray=['_id'=>new ObjectId( $data['category_id'])];
        $result = $this->dbBookCateObj->findOne($searchArray);

        if (!$result) {
            return ["message" => "category does not exist"];
        }

        $isExist =$this->isBookExist($data);
        if($isExist){
            return [
                'status' =>'failed',
                'message' => 'book already exist'
            ];
        }

        $this->dbBookObj->insertOne($data);
        return [
            'status' => 'success',
            'message' => 'book added successfully'];
    }

    private function isBookExist($data){
       $searchArray = ['name' => $data['name']];
       return  $this->dbBookObj->find($searchArray);
    }

    public function updateBook($id, $data) {
        $searchArray = ["_id" => new ObjectId($id)];
        $updateArray = ['$set' => $data];
        $updateResult = $this->dbBookObj->updateOne($searchArray, $updateArray);

        if ($updateResult) {
            return ["result" => 'book updated successfully'];
        }

        return ["message" => "Failed to update book"];
    }



    public function getBookDetails($id){
        $searchArray = ['_id' => new ObjectId($id)];
        return $this->dbBookObj->findOne($searchArray);
    }

    public function getAllBooks($searchArray = [], $projection = []){
        return $this->dbBookObj->find($searchArray, $projection);
    }

    public function deleteBook($id){
        $searchArray = ['_id' => new ObjectId($id)];
        $result = $this->dbBookObj->deleteOne($searchArray);
        return ["result" => $result];
    }

    public function searchBook($data){
        $searchArray = ["name" => ['$regex' => $data] ];
        $searchResult = $this->dbBookObj->find($searchArray);
        return $searchResult;
    }
    
    public  function getBookCategoryName($id){
        $searchArray = ['_id'=> new ObjectId($id)];
        $searchResult = $this->dbBookObj->findOne($searchArray);

        if(!$searchResult){
           return ['message' => 'Book not fount'];
        }

        $categoryId = $searchResult['category_id'];
        $name=$this->findNameOfCategory($categoryId);
        $result = ['name'=>$searchResult['name'],"author"=>$searchResult['author'],"quantity" => $searchResult['quantity'],"category"=>$name,
                 "season_start_date"=>$searchResult['season_start_date'],"season_end_date"=>$searchResult['season_end_date']];

        return $result;
    }

    private function findNameOfCategory($categoryId){
        $searchArray = ['_id'=> new ObjectId($categoryId)];
        $result = $this->dbBookCateObj->findOne($searchArray);
        $name= $result['name'];
        return  $name;
    }

    public function searchBooks($data){
        $searchArray = ['tags' =>['$regex' =>$data,'$options' => 'i']];
        $searchResult = $this->dbBookObj->find($searchArray);
        if(!$searchResult){
            return ["message" => "Book not found"];
        }
        $bookList = [];
        foreach ($searchResult as $value){
            $bookList[] =$value['name'];
        }
        return $bookList;
    }
}
