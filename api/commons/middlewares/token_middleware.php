<?php 

include_once 'commons/request.php';
include_once 'commons/response.php';
include_once 'auth/Token.php';
include_once 'commons/exceptions/authorization_exceptions.php';
include_once './auth/authentification_repository.php';

function tokenMiddleware(&$req, &$res){

    $authentificationRepository = new AuthentificationRepository();

    if($req->getUrl() == "/index.php/auth" && $req->getMethod() == 'POST'){
        return;
    }    
    $token = $req->getHeaders()["Authorization"];

    if($token == NULL){
        throw new NoToken();
    }

    var_dump($authentificationRepository->getUserByToken($token));
    if($authentificationRepository->getUserByToken($token) == 0){
        return new TokenDoesntExistException("Token doesn't exists");
    }
    
    $time = decodeToken($token)->time;

    if($time < time()){
        throw new ExpiredTokenException("Token is expired");
    }


    
}

