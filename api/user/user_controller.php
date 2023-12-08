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
            // Si on a une requête GET, on renvoie la liste des todos ou un todo en particulier
            case 'GET':
                
                if (sizeof($this->uri) == 4) {
                    try {
                       $result = $this->user_service->GetUser($this->uri[3]);
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
    
            // Si on a une requête PUT, on met à jour un todo
            /* case 'PATCH':
    
                $body = file_get_contents("php://input");
                $json = json_decode($body);
    
                if (!isset($json->description) && !isset($json->done)) {
                    exit_with_message("Bad Request", 400);
                }
    
                try {
                    $result = $this->todo_service->updateTodos($json);
                    exit_with_message("Updated", 200);
                } catch (HTTPException $e) {
                    exit_with_message($e->getMessage(), $e->getCode());
                }

                return $result;
                break; */
    
            // Si on a une requête DELETE, on supprime un todo
       /*      case 'DELETE':
                if (sizeof($this->uri) < 4) {
                    exit_with_message("Bad Request", 400);
                }
    
                try {
                    $this->todo_service->DeleteUser($this->uri[3]);
                    exit_with_message("Deleted", 200);
                } catch (HTTPException $e) {
                    exit_with_message($e->getMessage(), $e->getCode());
                }
                
                break;
    
            // On gère les requêtes OPTIONS pour permettre le CORS
            default:        
                header("HTTP/1.1 200 OK");
                exit(); */
            
        }
    }
}