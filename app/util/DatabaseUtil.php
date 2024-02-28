<?php

namespace App\util;

class DatabaseUtil{
  
    private $dbName ="bookstore";
    public function getConnection($collectionName){
         $dbUrl ="mongodb://"."127.0.0.1".":"."27017";
         $client = new \MongoDB\Client($dbUrl);
         $db = $client->selectDatabase($this->dbName);
         $options = ["typeMap" => ['root' => 'array', 'document' => 'array']];
         $colln = $db->selectCollection($collectionName,$options);
      return $colln;
    }
}
