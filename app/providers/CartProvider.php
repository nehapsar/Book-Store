<?php

namespace App\providers;
use App\util\BaseDataProviders;

class CartProvider extends BaseDataProviders {

    protected function collection(){
      return  "cart";
    }
}
