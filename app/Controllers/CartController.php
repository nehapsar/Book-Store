<?php

namespace App\controllers;
use App\providers\AddBookProvider;
use App\providers\BookCategoryProvider;
use App\providers\CartProvider;
use MongoDB\BSON\ObjectId;

class CartController{

    private $dbObj;
    
    public function __construct(){
        $this->dbObj = new CartProvider();
        $this->dbObjBook = new AddBookProvider();
        $this->dbObjCategory = new BookCategoryProvider();
        $this->time =date_default_timezone_set('Asia/Kolkata');
        $this->todayDateTimeIndia = date('Y-m-d');
    }
    
    public function addToCart($id, $quantity){
        $searchArray =[_id =>new ObjectId($id)];
        $result = $this->checkQuantityAvailable($id, $quantity);
        if($result == 0){
             return ["message" =>"quantity not available"];
        }
        if($result==1) {
               $result = $this->checkPrice($id,$quantity);
               $data = $this->dbObjBook->findOne($searchArray);
               $name = $data['name'];
               $cartDetails = ["name" => $name, "quantity" => $quantity, "price" => $result];
               $result =$this->dbObj->insertOne($cartDetails);
                return ["message"=>" added succusfull"];
           }
    }
    
    private function checkQuantityAvailable($id, $quantity) {
        $searchArray = [_id => new ObjectId($id)];
        $result = $this->dbObjBook->findOne($searchArray);
        if (!$result) {
            return ["message" => "book not found"];
        }
        $quantity = $quantity['quantity'];
        $quantityAvailable = $result['quantity'];
         if ($quantity > $quantityAvailable) {
            return  0;
        }else{
            return 1;
        }
    }
    
    private function checkPrice($id, $quantity) {
        $searchArray = ['_id' => new ObjectId($id)];
        $quantity = $quantity['quantity'];
        $result = $this->dbObjBook->findOne($searchArray);
        $seasonStartDate = $result[season_start_date];
        $seasonEndDate = $result[season_end_date];
        $todayTimestamp = strtotime($this->todayDateTimeIndia);
        $seasonStartTimestamp = strtotime($seasonStartDate);
        $seasonEndTimestamp = strtotime($seasonEndDate);
        if ($todayTimestamp >= $seasonStartTimestamp && $todayTimestamp <= $seasonEndTimestamp) {
            return $result[season_price] * $quantity;
        } else {
            return $result[price] * $quantity;
        }
    }
    
    public function deleteItemFromCart($id){
    $searchArray = ['_id'=>new ObjectId($id)];
    $result = $this->dbObj->deleteOne($searchArray);
    return $result;
    }

    public function viewCart($searchArray=[],$projectionArray=[]){
        $result = $this->dbObj->find($searchArray,$projectionArray);
        return $result;
    }

    public function cartStatus($searchArray =[]){
      $result = $this->itemsIncart($searArray =[]);
      if($result ==0){
          return ["cart status" =>"complete"];
      }
      else{
          return  ["cart status" =>"Active"];
      }
    }
    
    private function itemsIncart($searchArray=[]){
        $result = $this->dbObj->recordCount($searchArray);
        return $result;
    }

    public function checkOut(){
        $data = $this->dbObj->find($searchArray = []);
        if (!$data) {
         return ["message" => "cart is empty"];
        }
        $result =$this->checkOutPrice($data);
      return $result;
    }

    private  function checkOutPrice($data) {
        $cartInfo = []; $totalPrice = 0;
         foreach ($data as $value) {
            $name = $value['name'];
            $searchArray =["name" =>$name];
            $result = $this->dbObjBook->findOne($searchArray);
            $id = $result['_id'];
            $quantity = $value['quantity'];
            $result =$this->checkPrice($id,$quantity);
            $cartInfo [] =["name" => $value['name'],"quantity" => $value['quantity'],"price" => $result];
            $totalPrice +=$result;
         }
    
        $cartInfo [] =["totalPrice" =>$totalPrice];
        return $cartInfo;
  }
}

