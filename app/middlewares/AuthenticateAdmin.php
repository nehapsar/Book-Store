<?php

namespace App\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AuthenticateAdmin{

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next){
        $isLoggedIn = $this->checkUserIsLoggedIn($request, $response);
      
        if (!$isLoggedIn) {
            $response = $response->withStatus(401);
            $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
            return $response;
        }
      
        return $next($request, $response);
    }
    
    private function checkUserIsLoggedIn(ServerRequestInterface $request, ResponseInterface $response){
        $token = $this->getTokenFromRequest($request);

        if (!$token || !$this->validateToken($token)) {
            $response = $response->withStatus(401);
            $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
            return $response;
        }

        return $token;
    }

    private function getTokenFromRequest(ServerRequestInterface $request){
        $token = $request->getHeaderLine('Authorization');
        return $token ? str_replace('Bearer ', '', $token) : null;
    }

    private function validateToken($token){
        $secretKey = '6666';
        $data = JWT::decode($token, new Key($secretKey, 'HS256'));

        if (isset($data->exp) && $data->exp < time()){
            return false;
        }

        return true;
    }
}
