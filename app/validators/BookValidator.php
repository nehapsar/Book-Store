<?php

namespace App\validators;

use App\util\BaseValidator;
use Carbon\Carbon;

class BookValidator extends BaseValidator{

    protected function collection(){
        return 'books';
    }

    protected function rules(){
        return [
          'add'=>[
              'name' => ['required','string'],
              'author' => ['required' , 'string'],
              'price' => ['required' , 'numeric','gt:0'],
              'category_id' => ['required' , 'string'],
              'quantity' => ['required' , 'numeric', 'gt:0'],
              'season_start_date' => ['date','date', 'after_or_equal:' .Carbon::now()->toDateString()],
              'season_end_date' => ['date', 'after_or_equal:season_start_date'],
              'season_price' => ['numeric','gt:0']
          ]
        ];
    }
}


