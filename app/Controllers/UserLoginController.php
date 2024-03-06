<?php

namespace App\controllers;
use App\Middleware\AuthenticateUser;
use App\providers\LoginsProvider;
use App\providers\SignupProvider;
use Firebase\JWT\JWT;

class LoginController{
  
    public function userLogin($data){

        $dbObj = new LoginsProvider();
        $dbSignupObj = new SignupProvider();
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
        $dbObj->insertOne($loginDetails);

        return ['message' => 'Login successful', 'token' => $token];

    }
    private function generateToken($email){
        $secretKey = '6666';
        $payload = [
            'email' => $email,
            'exp' => time() + 60,
        ];

        $token = JWT::encode($payload, $secretKey, 'HS256');

        return $token;
    }
}
