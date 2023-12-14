<?php
include_once './auth/logout.php';
include_once './auth/authentification_service.php';
include_once './auth/Token.php';

class AuthentificationController{

    public $authentificationService;

    public function __construct(){
        $this->authentificationService = new AuthentificationService();
    }

    public function dispatch(Request $req, Response $res):void{
        switch($req->getMethod()) {
            case 'POST' : 
                $res->setContent($this->authentificationService->login($req->getbody()));
                break;

            case 'DELETE' :
                $this->authentificationService->logout($req->getHeaders()["Authorization"]);
                break;
        }
    }
}