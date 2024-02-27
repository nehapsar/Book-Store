<?php

namespace App\util;

abstract class BaseDataProviders{

    protected $dbObj;
    protected $collectionObj;

    public function __construct() {
        $this->dbObj = new DatabaseUtil();
        $this->collectionObj = $this->dbObj->getConnection($this->collection());
    }

    abstract protected function collection();

    public function insertOne($data){
        $result = $this->collectionObj->insertOne($data);
        return $result->isAcknowledged();
    }

    public function updateOne($searchArray, $updateArray) {
        $result = $this->collectionObj->updateOne($searchArray, $updateArray);
        return $result->getModifiedCount();
    }
  
    public function findOne($searchArray, $projection =[]){
        return $this->collectionObj->findOne($searchArray, ['projection' => $projection]);
    }
  
    public function deleteOne($searchArray){
        $result = $this->collectionObj->deleteOne($searchArray);
        return $result->getDeletedCount();
    }
  
    public function updateMany($searchArray, $updateArray){
        $result = $this->collectionObj->updateMany($searchArray, $updateArray);
        return $result->getModifiedCount();
    }

    public function find($searchArray = [], $projection = []) {
        return  $this->collectionObj->find($searchArray, ["projection" => $projection])->toArray();
    }

    public function recordCount($searchArray) {
        $result = $this->collectionObj->countDocuments($searchArray);
        return $result;
    }

    public function replaceOne($searchArray, $updateArray){
        $result = $this->collectionObj->replaceOne($searchArray, $updateArray);
        return $result;
    }

    public  function bulkInsert($data) {
        $query = [
            'insertOne'  => [$data]
        ];
        return $query;
    }
  
    public  function bulkUpdate($searchArray, $updateArray) {
        $query = [
            'updateOne'  => [$searchArray, $updateArray]
        ];
        return $query;
    }
  
    public  function bulkDelete($searchArray) {
        $query = [
            'deleteOne'  => [$searchArray]
        ];
        return $query;
    }
  
    public  function bulkWrite($operations, $ordered=false) {
        return $this->collectionObj->bulkWrite($operations, ['ordered' => $ordered]);
    }


}
