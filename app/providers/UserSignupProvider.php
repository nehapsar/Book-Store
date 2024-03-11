<?php

namespace App\providers;

use App\util\BaseDataProviders;

class UserSignupProvider extends BaseDataProviders{

    protected function collection(){
     return "user_signup_details";
    }
}
