<?php

include_once 'user_model.php';
include_once 'user_service.php';

class UserController{

    public $userService;

    public function __construct(){
        $this->userService = new UserService;
    }

    public function dispatch(Request $req, Response $res):void{
        switch($req->getMethod()) {
            case 'GET':
                
                if ($req->getPathAt(4) !== "" && is_string($req->getPathAt(4))) {
                    try {
                        if(!is_numeric($req->getPathAt(4))) 
                            throw new BadRequestException("id is not valid!.");
                       $res->setContent($this->userService->getUser(intval($req->getPathAt(4))));
                    } catch (HTTPException $e) {
                        $res->setContent($e->getMessage(), $e->getCode());
                    }
                } else{
                    $res->setContent($result = $this->userService->getUsers());
                }
                
                break;
    
            case 'POST':
                if (!$req->getBody()) {
                    throw new BadRequestException("Bad Request", 400);
                }
    
                try {
                    $res->setContent($result = $this->userService->createUser($req->getBody()));
                } catch (HTTPException $e) {
                    $res->setMessage($e->getMessage(), $e->getCode());
                }
                break;
    
            case 'PATCH':
                if($req->getPathAt(2) == "back_office"){
                    if (!isset($req->getBody()->id) || (!isset($req->getBody()->role))) {
                        throw new BadRequestException("Bad Request", 400);
                    }
                }
                
                if($req->getPathAt(2) == "restpatrop"){
                    if (!isset($req->getBody()->id) || !isset($req->getBody()->mail)
                    || !isset($req->getBody()->password) || $req->getBody()->role != NULL){
                        throw new BadRequestException("Bad Request", 400);
                    }

                    $req->getBody()->role = NULL;
                }
                
    
                try {
                    $res->setContent($this->userService->updateUser($req->getBody()));
                } catch (HTTPException $e) {
                    $res->setMessage($e->getMessage(), $e->getCode());
                }
                break;
    
            case 'DELETE':

                if ($req->getPathAt(3) === "" || !is_numeric($req->getPathAt(4))) {
                    throw new BadRequestException("Please provide an ID for the user to delete.");
                }

                try {                    
                    $this->userService->deleteUser($req->getPathAt(4));
                    $res->setMessage("Deleted", 200);
                } catch (HTTPException $e) {
                    $res->setMessage($e->getMessage(), $e->getCode());
                }
                
                break;
    
            default:        
                header("HTTP/1.1 200 OK");
                exit();
            
        }
    }
}