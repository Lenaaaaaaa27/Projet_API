<?php 

include_once 'auth/authentification_repository.php';
include_once 'auth/Token.php';

class AuthentificationService{

    public $authentificationRepository;

    public function __construct(){
        $this->authentificationRepository = new AuthentificationRepository;
    }

    public function login(stdClass $body):UserModel{

        $salt = "DJSOJQ02ddqodkCSQDzqdzdKOPDKSDkapodkP09D92KC2ie2I";

        $body->password = $body->password . $salt;
        $body->password = hash('sha256', $body->password);

        $userModel = $this->authentificationRepository->getUserByMail($body->mail, $body->password);
        $userModel->token = GenerateToken($userModel);

        if($this->authentificationRepository->AddToken($userModel))
        return $userModel;
        }

    public function logout():void{
        if($this->authentificationRepository->deleteToken($id)) 
    }
}