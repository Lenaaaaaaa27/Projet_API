<?php

include_once 'user_model.php';
include_once 'user_service.php';

class UserController{

    public $UserService;
    public $uri;
    public $method;

    public function __construct($uri, $method){
        $this->UserService = new UserService;
        $this->uri = $uri;
        $this->method = $method;
    }

    public function switch_methods(){

        switch($this->method) {
            case 'GET':
                
                if (sizeof($this->uri) == 4) {
                    try {
                       $result = $this->UserService->GetUser(intval($this->uri[3]));
                    } catch (HTTPException $e) {
                        exit_with_message($e->getMessage(), $e->getCode());
                    }
                } else{
                    $result = $this->UserService->GetUsers();
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
                    $result = $this->UserService->CreateUser($json);
                } catch (HTTPException $e) {
                    exit_with_message($e->getMessage(), $e->getCode());
                }

                return $result;
                break;
    
            case 'PATCH':
    
                $body = file_get_contents("php://input");
                $json = json_decode($body);
    
                if (!isset($json->new_password) && !isset($json->current_password) 
                    && !isset($json->new_mail) && !isset($json->current_mail)
                    && !isset($json->id) && !isset($json->role)) {
                    exit_with_message("Bad Request", 400);
                }
    
                try {
                    $result = $this->UserService->UpdateUser($json);
                    exit_with_content($result);
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
                    $this->UserService->DeleteUser($json);
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