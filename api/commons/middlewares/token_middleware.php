<?php 

include_once './commons/request.php';
include_once './commons/response.php';
include_once './auth/Token.php';
include_once './commons/exceptions/authorization_exceptions.php';
include_once './auth/authentification_repository.php';

function tokenMiddleware(&$req, &$res){

    $authentificationRepository = new AuthentificationRepository();

    if($req->getUrl() == "/index.php/auth" && $req->getMethod() == 'POST' || $req->getUrl() == "/index.php/restpatrop/user" && $req->getMethod() == 'POST'){
        return;
    }    

    $token = $req->getHeaders()["Authorization"];

    if($token == NULL){
        throw new NoToken();
    }

    if(!$authentificationRepository->getUserByToken($token)){
        throw new TokenDoesntExistException("Token doesn't exist");
    }
    
    $time = decodeToken($token)->time;

    if($time < time()){
        throw new ExpiredTokenException("Token is expired");
    }    
}

