<?php 

include_once 'auth/authentification_repository.php';
include_once 'auth/Token.php';

class AuthentificationService{

    public $authentificationRepository;

    public function __construct(){
        $this->authentificationRepository = new AuthentificationRepository;
    }

    public function login(stdClass $body){

        $userModel = $this->authentificationRepository->VerifLogin($body->mail, $body->password);
        $userModel->token = GenerateToken($userModel);

        if($this->authentificationRepository->AddToken($userModel))

        $token["token"] = $userModel->token;

        return $token;
        }

    public function logout(string $token):void{

        if($this->authentificationRepository->deleteToken($token));
    }
}