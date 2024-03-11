<?php

namespace App\controllers;

use App\providers\BookCategoryProvider;
use App\providers\BooksProvider;
use App\validators\BookCategoryValidator;
use MongoDB\BSON\ObjectId;

class BookCategoryController{

  private $dbBookCatObj;
  private $dbBookObj;

  public function __construct(){
        $this->dbBookCatObj = new BookCategoryProvider();
        $this->dbBookObj = new BooksProvider();
    }
 
  public function addCategory($data){
        $validator = new BookCategoryValidator($data,'add');
        $validator->validate();
        $isCategoryExist =$this->checkCategoryExist($data);

        if($isCategoryExist){
           return [
               'status' => 'Failed',
               'message' => 'Category Already Exist'
           ];
        }

        $this->dbBookCatObj->insertOne($data);
       return [
           'status' => 'success',
           'message' => 'Book Category added successfully'
       ];
   }
  
  private function checkCategoryExist($data){
       $searchArray = ['name' => $data['name']];
       $searchResult =$this->dbBookCatObj->find($searchArray);
       return $searchResult ;
   }

  public function updateCategory($id,$data){
       $validator = new BookCategoryValidator($data,'update');
       $validator->validate();
       $searchArray = ['_id' => new ObjectId($id)];
       $updateArray = ['$set' => $data];
       $updateResult = $this->dbBookCatObj->updateOne($searchArray,$updateArray);

       if(!$updateResult){
           return [
               'status' => 'Failed',
               'message' => 'Category does not exist'
           ];
       }

       return [
           'status' => 'success',
           'message' => 'Book Category updated successfully'
       ];
   }

  public function getCategory($id){
        $searchArray=["_id"=>new ObjectId($id)];
        return $this->dbBookCatObj->findOne($searchArray);
  }

  public function getCategoryList($searchArray=[],$projection=[]){
        $result =$this->dbBookCatObj->find($searchArray,$projection);
        return ["result"=>$result];
  }

  public function numberOfBooksInEachCategory($searchArray = [],$projection =[]){
        $result = $this->dbBookCatObj->find($searchArray ,$projection);
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
            $data= $this->dbBookCatObj->findOne($searchArray);
            $searchArrayforCount = ['category_id' => $item];
            $result = $this->dbBookObj->recordCount($searchArrayforCount);
            $name = $data['name'];
            $countArray["$name"] = $result;
        }
        return $countArray;
   }

  public function deleteCategory($categoryIdToDelete, $categoryIdToMove) {
        $result = $this->isCategoryExist($categoryIdToDelete, $categoryIdToMove);
        if ($result == true) {
            $this->moveBooksToOtherCategory($categoryIdToDelete, $categoryIdToMove);
            $searchArray = ["_id" => new ObjectId($categoryIdToDelete)];
            $deleteResult = $this->dbBookCatObj->deleteOne($searchArray);

            if ($deleteResult >= 1) {
                return ["message" => "Category deleted"];
            }
        }
    }

  private function isCategoryExist($categoryIdToDelete, $categoryIdToMove) {

       $searchArrayToDelete = ["_id" => new ObjectId($categoryIdToDelete)];
       $searchArrayToMove = ['category_id' => (string)$categoryIdToMove];
       $resultToDelete = $this->dbBookCatObj->findOne($searchArrayToDelete);
       $resultToMove = $this->dbBookCatObj->findOne($searchArrayToMove);

        if (!$resultToDelete || !$resultToMove ) {
            return 'Delete category does not exist';
        }

        return true;
    }
  
  private function moveBooksToOtherCategory($categoryIdToDelete, $categoryIdToMove) {
        $searchArray = ["category_id" => $categoryIdToDelete];
        $updateArray = ['$set' => ["category_id" => $categoryIdToMove]];
        $result = $this->dbBookObj->updateMany($searchArray, $updateArray);
        return $result;
    }
}
