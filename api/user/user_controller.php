<?php

include_once 'user_model.php';
include_once 'user_service.php';

class UserController{

    public $user_service;
    public $uri;
    public $method;

    public function __construct($uri, $method){
        $this->user_service = new UserService;
        $this->uri = $uri;
        $this->method = $method;
    }

    public function switch_methods(){

        switch($this->method) {
            case 'GET':
                
                if (sizeof($this->uri) == 4) {
                    try {
                       $result = $this->user_service->GetUser(intval($this->uri[3]));
                    } catch (HTTPException $e) {
                        exit_with_message($e->getMessage(), $e->getCode());
                    }
                } else{
                    $result = $this->user_service->GetUsers();
                }
                
                return $result;
                break;
    
            case 'POST':
                $body = file_get_contents("php://input");
                $json = json_decode($body);
    
                if (!isset($json)) {
                    exit_with_message("Bad Request", 400);
                }
    
                try {
                    $result = $this->user_service->CreateUser($json);
                } catch (HTTPException $e) {
                    exit_with_message($e->getMessage(), $e->getCode());
                }

                return $result;
                break;
    
            case 'PATCH':
    
                $body = file_get_contents("php://input");
                $json = json_decode($body);
    
                if (!isset($json->description) && !isset($json->done)) {
                    exit_with_message("Bad Request", 400);
                }
    
                try {
                    $result = $this->user_service->UpdateUser($json);
                    exit_with_message("Updated", 200);
                } catch (HTTPException $e) {
                    exit_with_message($e->getMessage(), $e->getCode());
                }

                return $result;
                break;
    
            case 'DELETE':

                $body = file_get_contents("php://input");
                $json = json_decode($body);

                if (!isset($json)) {
                    exit_with_message("Bad Request", 400);
                }
    
                try {                    
                    $this->user_service->DeleteUser($json);
                    exit_with_message("Deleted", 200);
                } catch (HTTPException $e) {
                    exit_with_message($e->getMessage(), $e->getCode());
                }
                
                break;
    
            default:        
                header("HTTP/1.1 200 OK");
                exit();
            
        }
    }
}