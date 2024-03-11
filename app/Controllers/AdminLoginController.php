<?php

namespace App\controllers;

use App\providers\AdminLoginProvider;
use App\providers\AdminSignupProvider;
use App\validators\AdminLoginValidator;
use Firebase\JWT\JWT;

class AdminLoginController{
  
    public function login($data){
        $validator = new AdminLoginValidator($data,'login');
        $validator->validate();
        $validator->getMessageBag();

        $dbLoginObj = new AdminLoginProvider();
        $dbSignupObj = new AdminSignupProvider();

        $searchArray = ['email' => $data['email']];
        $result = $dbSignupObj->findOne($searchArray);
        $name = $result['first_name'] . ' ' . $result['last_name'];

        $hashedPassword = $result['password'];
        $password = $data['password'];
        $isPasswordValid = password_verify($password, $hashedPassword);

        if (!$isPasswordValid) {
            return ['message' => 'Invalid Username or password'];
        }

        $token = $this->generateToken($data['email']);
        $loginDetails = ['name' => $name, 'email' => $data['email'], 'token' => $token];
        $dbLoginObj->insertOne($loginDetails);

        return ['message' => 'Login successful', 'token' => $token];
    }

    private function generateToken($email){
        $secretKey = '6666';
        $payload = [
            'email' => $email,
            'exp' => time() + 3600,
        ];

        $token = JWT::encode($payload, $secretKey, 'HS256');

        return $token;
    }
}
