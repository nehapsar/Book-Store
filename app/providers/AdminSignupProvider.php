<?php

namespace App\providers;

use App\util\BaseDataProviders;

class AdminSignupProvider extends BaseDataProviders{
  
   protected function collection(){
        return "admin_signups" ;
    }
}
