<?php

namespace App\providers;
use App\util\BaseDataProviders;

class BookCategoryProvider extends BaseDataProviders {

    protected function collection(){
        return "book_categories";
    }
}
