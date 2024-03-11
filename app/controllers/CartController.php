<?php

namespace App\controllers;

use App\providers\BookCategoryProvider;
use App\providers\BooksProvider;
use App\providers\CartProvider;
use MongoDB\BSON\ObjectId;

class CartController{

   private $dbCartObj;
   private $dbBookObj;
   private $dbCateObj;
   private $time;
   private $todayDateTimeIndia;

   public function __construct(){
        $this->dbCartObj = new CartProvider();
        $this->dbBookObj = new BooksProvider();
        $this->dbCateObj = new BookCategoryProvider();
        $this->time =date_default_timezone_set('Asia/Kolkata');
        $this->todayDateTimeIndia = date('Y-m-d');
   }

   public function addToCart($id, $quantity){
        $searchArray =['_id' =>new ObjectId($id)];
        $quantityAddedToCart = $quantity['quantity'];
        $bookResult= $this->dbBookObj->findOne($searchArray);

        if (!$bookResult) {
            return ["message" => "book not found"];
        }

        $quantityResult = $this->checkQuantityAvailable($bookResult,$quantityAddedToCart);
        if(!$quantityResult){
             return ["message" =>"quantity not available"];
        }

       $priceResult = $this->checkPrice($bookResult ,$quantityAddedToCart);
       $name = $bookResult['name'];
       $cartDetails = ["name" => $name, "quantity" => $quantity, "price" => $priceResult];
       $this->dbCartObj->insertOne($cartDetails);
       return ["message"=>"Item added to cart"];
   }
   
   private function checkQuantityAvailable($bookResult,$quantityAddedToCart) {
        $quantityAvailable = $bookResult['quantity'];

        if ($quantityAddedToCart > $quantityAvailable) {
            return  false;
        }

        return true;
    }
   
    private function checkPrice($bookResult, $quantityAddedToCart) {

        $seasonStartDate = $bookResult['season_start_date'];
        $seasonEndDate = $bookResult['season_end_date'];
        $todayTimestamp = strtotime($this->todayDateTimeIndia);
        $seasonStartTimestamp = strtotime($seasonStartDate);
        $seasonEndTimestamp = strtotime($seasonEndDate);

        if ($todayTimestamp >= $seasonStartTimestamp && $todayTimestamp <= $seasonEndTimestamp) {
            return $bookResult['season_price'] * $quantityAddedToCart;
        }

        return $bookResult['price'] * $quantityAddedToCart;
    }

    public function deleteItemFromCart($id){
       $searchArray = ['_id'=>new ObjectId($id)];
       $deletResult = $this->dbCartObj->deleteOne($searchArray);
       return $deletResult;
    }

    public function viewCart($searchArray=[],$projectionArray=[]){
        $result = $this->dbCartObj->find($searchArray,$projectionArray);
        return $result;
    }

    public function cartStatus(){
      $result = $this->itemsIncart();

      if($result ==0){
          return ["cart status" =>"complete"];
      }

      return  ["cart status" =>"Active"];
    }
    
    private function itemsIncart(){
        $result = $this->dbCartObj->recordCount($searchArray=[]);
        return $result;
    }

    public function checkOut(){
        $data = $this->dbCartObj->find();

        if (!$data) {
         return ["message" => "cart is empty"];
        }

        $checkoutResult =$this->checkOutPrice($data);
      return $checkoutResult;
    }

    private  function checkOutPrice($data) {
        $cartInfo = []; $totalPrice = 0;

        foreach ($data as $value) {
            $name = $value['name'];
            $searchArray =["name" =>$name];
            $result = $this->dbBookObj->findOne($searchArray);
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
