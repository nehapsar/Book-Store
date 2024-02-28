<?php

namespace App\controllers;
use App\providers\AddBookProvider;
use App\providers\BookCategoryProvider;
use MongoDB\BSON\ObjectId;

class BookCategoryController{

  private $dbObj;
  public function __construct(){
        $this->dbObj = new BookCategoryProvider();
        $this->dbBookObj = new AddBookProvider();
    }
  
  public function addCategory($data){
        if ($this->isValidData($data)) {
            $this->dbObj->insertOne($data);
            return ["result" => "category added"];
        } else {
            return ["error" => "Invalid data"];
        }
    }

    private function isValidData($data){
        if (!isset($data['name']) || !preg_match('/^[a-zA-Z]+$/', $data['name'])) {
            return false;
        }
        return true;
    }

    public function updateCategory($id,$data){
        $searchArray=["_id"=>new ObjectId($id)];
        if($this->isValidData($data)){
        $updateArray = ['$set' => $data];
         $result =$this->dbObj->updateOne($searchArray, $updateArray);
        return ["result" => "category updated"];
        }else {
            return ["error" => "Invalid data"];
        }
    }

    public function getCategory($id){
        $searchArray=["_id"=>new ObjectId($id)];
        return $this->dbObj->findOne($searchArray);
    }
  
    public function getAllCategory($searchArray=[],$projection=[]){
        $result =$this->dbObj->find($searchArray,$projection);
        return ["result"=>$result];
    }
  
    public function numberOfBooksInEachCategory($searchArray = [],$projection =[]){
        $result = $this->dbObj->find($searchArray ,$projection);
           $categoryObjId =[];
          foreach ($result as $categoryId) {
              $oid =$categoryId['_id']->__toString();
              array_push($categoryObjId, $oid);
          }
       return $this->countNumberOfBooks($categoryObjId);
    }

    private function countNumberOfBooks($categoryIds){
        $countArray = [];
        foreach ($categoryIds as $item) {
            $searchArray = ['_id'=> new ObjectId($item)];
            $data= $this->dbObj->findOne($searchArray);
            $searchArrayforCount = ['category_id' => $item];
            $result = $this->dbBookObj->recordCount($searchArrayforCount);
            $name = $data['name'];
            $countArray["$name"] = $result;
        }
             return $countArray;
    }

     public function deleteCategory($categoryIdToDelete,$categoryIdToMove){
             $this->moveBooksToOtherCategory($categoryIdToDelete,$categoryIdToMove);
             $searchArray = ["_id" =>  new ObjectId($categoryIdToDelete)];
             $result = $this->dbObj->deleteOne($searchArray);
             if($result>=1) {
                 return ["message" => "Category deleted"];
             }
             else{
                 return ["message" => "Category not exist"];
             }
         }

       private function moveBooksToOtherCategory($categoryIdToDelete,$categoryIdToMove){
        $searchArray = ["category_id"=>$categoryIdToDelete];
        $updateArray = ['$set'=>$categoryIdToMove];
        $result = $this->dbBookObj->updateMany($searchArray,$updateArray);
        return $result;
       }
}
