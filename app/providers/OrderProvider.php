<?php

namespace App\providers;
use App\util\BaseDataProviders;

class OrderProvider extends BaseDataProviders{

  protected function collection(){
        return "orders";
    }
}
