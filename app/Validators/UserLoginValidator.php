<?php

namespace App\validators;

use App\util\BaseValidator;

class UserLoginValidator extends BaseValidator{

    protected function collection(){
        return "users";
    }

    protected function rules(){
        return [
            'login' => [
                'email' =>['required' , 'email'],
                'password' => ['required','string','min:8','max:12' ,'regex:/^(?=.*[A-Z])(?=.*[!@#$%^&*()-_=+{};:,<.>]){1,}(?=.*[0-9]){1,}/']
            ]
        ];
    }
}
