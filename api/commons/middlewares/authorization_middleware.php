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
    $user = $req->getPathAt(3);

    if($url == "/index.php/auth" && $req->getMethod() == 'POST' || $url == "/index.php/restpatrop/user" && $req->getMethod() == 'POST'){
        
        $salt = "DJSOJQ02ddqodkCSQDzqdzdKOPDKSDkapodkP09D92KC2ie2I";
    
        $req->getBody()->password = $req->getBody()->password . $salt;
        $req->getBody()->password = hash('sha256', $req->getBody()->password);
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
    
    $decodeToken = decodeToken($token);
    $time = $decodeToken->time;

    if($time < time()){
        throw new ExpiredTokenException("Token is expired");
    }    

    $role = $decodeToken->role;
    $id = $decodeToken->id;

    // On verifie les roles

    // Pour accéder au back office

    if($role != "3" && $path == "back_office"){
        throw new AdminAccessException("Vous n'avez pas l'acces au back office");
    }

    // Pour acceder à l'appli : que les proprio qui peuvent rendre dispo/indispo leurs appart (role : 1)

    if($userAgent == "restpatrop"){
        if($role != "1" && $path == "restpatrop"){
            throw new OwnerAccessException("Vous n'avez pas l'accès à l'application respatrop");
        }
    }elseif($path == "restpatrop"){
        if($role != 2 && ($req->getMethod() == 'PATCH' || $req->getMethod() == 'POST') && $req->getPathAt(3) == "apartment"){
            throw new InternAccessException("Vous ne pouvez modifier ou ajouter un appartement");
        }

        if($user == "user"){

            if($req->getMethod() == 'PATCH'){
                $idBody = $req->getBody()->id;
                if(!is_numeric($idBody)){
                    throw new ForbiddenUpdateUser("L'id est invalide");
                }
                if($idBody != $id){
                    throw new ForbiddenUpdateUser("Tu n'as pas le droit de modifier un autre compte que le tien");
                }
            }

            if($req->getMethod() == 'PATCH' || $req->getMethod() == 'POST'){

                $salt = "DJSOJQ02ddqodkCSQDzqdzdKOPDKSDkapodkP09D92KC2ie2I";
    
                $req->getBody()->password = $req->getBody()->password . $salt;
                $req->getBody()->password = hash('sha256', $req->getBody()->password);
            }

            if($req->getMethod() == 'DELETE'){
                $idUrl = $req->getPathAt(4);
                if($idUrl != $id){
                    throw new ForbiddenDeleteUser("Tu n'as pas le droit de supprimer un autre compte que le tien");
                }
            }
        }
    }
    

    

}
