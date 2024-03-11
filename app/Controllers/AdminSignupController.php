<?php

namespace App\controllers;

use App\providers\AdminSignupProvider;
use App\validators\AdminSignupValidator;

class AdminSignupController{

    private $dbSignupObj;
    
    public function __construct() {
        $this->dbSignupObj = new AdminSignupProvider();
    }
    
    public function signupDetails($data) {
        $validator = new AdminSignupValidator($data , 'signup');
        $validator->validate();

        $email = $data['email'];
        $emailResult = $this->isEmailIdExist($email);

        if ($emailResult) {
            return ["message" => "Email already registered"];
        }

        $hashedPassword = password_hash($data['password'],PASSWORD_DEFAULT);
        $details = ['first_name'=>$data['first_name'],'last_name'=>$data['last_name'],'email'=>$data['email'],'password'=>$hashedPassword];
        $insertResult = $this->dbSignupObj->insertOne($details);

        if ($insertResult) {
            return ["message" => 'Signup completed'];
        }

        return ["message" => 'Failed to complete signup'];
    }
  
    private function isEmailIdExist($email) {
        $searchArray = ['email' => $email];
        $result = $this->dbSignupObj->findOne($searchArray);
        return $result;
    }
}


