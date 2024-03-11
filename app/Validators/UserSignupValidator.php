<?php

namespace App\validators;

use App\util\BaseValidator;

class UserSignupValidator extends BaseValidator{

    protected function collection(){
        return 'user_signup_details';
    }

    protected function rules(){
        return [
            'signup' => [
                'first_name' => ['required' , 'string','regex:/^[A-Za-z]+$/'],
                'last_name' => ['required' , 'string','regex:/^[A-Za-z]+$/'],
                'email' => ['required' , 'email'],
                'password' => ['required' , 'string','min:8','max:12' ,'regex:/^(?=.*[A-Z])(?=.*[!@#$%^&*()-_=+{};:,<.>]){1,}(?=.*[0-9]){1,}/']
            ]
        ];
    }
}
