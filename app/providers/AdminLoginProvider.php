<?php

namespace App\providers;

use App\util\BaseDataProviders;

class AdminLoginProvider extends BaseDataProviders {

    protected function collection(){
       return "admin_logins";
    }
}
