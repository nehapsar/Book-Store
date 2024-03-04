<?php

namespace App\providers;
use App\util\BaseDataProviders;

class AddBookProvider  extends BaseDataProviders{

    protected function collection(){
        return "books";
    }
}
