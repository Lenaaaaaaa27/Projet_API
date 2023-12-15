<?php 

include_once './commons/request.php';
include_once './commons/response.php';
include_once './auth/Token.php';
include_once './commons/exceptions/authorization_exceptions.php';
include_once './auth/authentification_repository.php';

function authorizationMiddleware(&$req, &$res){

    $authentificationRepository = new AuthentificationRepository();
    $url = $req->getUrl();
    $path = $req->getPathAt(2);

    if($url == "/index.php/auth" && $req->getMethod() == 'POST' || $url == "/index.php/restpatrop/user" && $req->getMethod() == 'POST'){
        return;
    }    

    $token = $req->getHeaders()["Authorization"];
    $userAgent = $req->getHeaders()["User-Agent"];

    if($token == NULL){
        throw new NoToken();
    }

    if(!$authentificationRepository->getUserByToken($token)){
        throw new TokenDoesntExistException("Token doesn't exist");
    }
    
    $time = decodeToken($token)->time;
    $role = decodeToken($token)->role;

    if($time < time()){
        throw new ExpiredTokenException("Token is expired");
    }    

    // On verifie les roles

    // Pour accéder au back office

    if($role != "3" && $path == "back_office"){
        throw new AdminAccessException("Vous n'avez pas l'acces au back office");
    }

    // Pour acceder à l'appli : que les proprio qui peuvent rendre dispo/indispo leurs appart (role : 1)

    if($role != "1" && $userAgent == "restpatrop" && $path == "restpatrop"){
        throw new OwnerAccessException("Vous n'avez pas l'accès à l'application respatrop");
    }

}
