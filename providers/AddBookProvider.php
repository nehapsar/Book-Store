<?php

namespace App\providers;
use App\util\BaseDataProviders;

class AddBookProvider  extends BaseDataProviders{

    protected function collection(){
        return "add_books_to_categories";
    }
}
