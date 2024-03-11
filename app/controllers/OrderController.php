<?php

namespace App\controllers;

use App\providers\BooksProvider;
use App\providers\CartProvider;
use App\providers\OrderProvider;
use MongoDB\BSON\ObjectId;

class OrderController{

    private $dbOrderObj;
    private $dbCartObj;
    private $dbBookObj;

    public function __construct(){
        $this->dbOrderObj = new OrderProvider();
        $this->dbCartObj = new CartProvider();
        $this->dbBookObj = new BooksProvider();
    }

    public function completeOrder($searchArray = []){
        $result = $this->dbCartObj->find($searchArray);
        $this->reduceQuantity($result);
        $this->dbOrderObj->insertOne($result);
        $result = $this->dbCartObj->deleteAll($searchArray);

        if($result==0) {
            return ["message" => 'please add items to cart'];
        }

        return ["message" =>"order Complete"];
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
        $result =$this->dbOrderObj->findOne($searchArray);
    
        if(!$result){
            return ["message" => "order not found"];
        }
      
        return $result;
    }

    public function viewOrderList(){
        $searchArray = [];
        $orderResult = $this->dbOrderObj->find($searchArray);
        return $orderResult;
    }
}
