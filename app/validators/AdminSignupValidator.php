<?php

namespace App\validators;

use App\util\BaseValidator;

class AdminSignupValidator extends BaseValidator {

    protected function collection(){
        return "admin_signups" ;
    }

    protected function rules(){
        return [
            'signup' => [
                'first_name' => ['required' , 'string' , 'regex:/^[A-Za-z]+$/'],
                'last_name'  => ['required' , 'string','regex:/^[A-Za-z]+$/'],
                'email' => ['required' , 'string'],
                'password' => ['required' , 'string','regex:/^(?=.*[A-Z])(?=.*[!@#$%^&*()-_=+{};:,<.>]){1,}(?=.*[0-9]){1,}/']
            ]
        ];
    }
}
