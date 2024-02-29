<?php

namespace App\controllers;
use App\providers\AddBookProvider;
use App\providers\CartProvider;
use App\providers\OrderProvider;
use MongoDB\BSON\ObjectId;

class OrderController{
 
  private $dbObj;
  private $dbCartObj ;
  private $dbBookObj;
  
  public function __construct(){
        $this->dbObj = new OrderProvider();
        $this->dbCartObj = new CartProvider();
        $this->dbBookObj = new AddBookProvider();
   }
  
  public function completeOrder($searchArray = []){
      $result = $this->dbCartObj->find($searchArray);
      $this->reduceQuantity($result);
      $this->dbObj->insertOne($result);
      $result = $this->dbCartObj->deleteAll($searchArray);
      if($result==0){
         return ["message" =>'please add items to caart'];
     }
      return ["mesage" =>"order Complete"];
   }
  
   private function reduceQuantity($result) {
        foreach ($result as $value) {
            $searchArray = ['name' => $value['name']];
            $updateArray = ['$inc' => ['quantity' => -(int)$value['quantity']]];
            $this->dbBookObj->updateOne($searchArray, $updateArray);
        }
    }
  
    public function viewOrderDetails($id){
        $searchArray = ['_id' =>new ObjectId($id)];
        $result =$this->dbObj->findOne($searchArray);
        if(!$result){
            return ["message" => "order not found"];
        }
        return $result;
    }
    
    public function viewOrderList($id){
        $searchArray = [];
        $result = $this->dbObj->find($searchArray);
        return $result;
    }
}
