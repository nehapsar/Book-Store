<?php

namespace App\validators;

use App\util\BaseValidator;

class BookCategoryValidator extends BaseValidator{

    public function collection(){
        return 'book_categories';
    }

    protected function rules(){
        return [
            'add' => [
                'name'=> ['required' , 'string']
            ],
            'update' => [
                'name' => ['sometimes' , 'required' , 'string']
            ]
        ];
    }
}
