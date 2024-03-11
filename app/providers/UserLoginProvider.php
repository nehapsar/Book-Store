<?php

namespace App\providers;

use App\util\BaseDataProviders;

class UserLoginsProvider extends BaseDataProviders {

    protected function collection(){
        return "users";
    }
}
