<?php 

include_once 'user_repository.php';
include_once 'user_model.php';
include_once 'commons/exceptions/service_exceptions.php';

class UserService{
    private $repository;

    function __construct() {
        $this->repository = new UserRepository();
    }

    function getUsers(): array {
        return $this->repository->getUsers();
    }

    function getUser(int $id): UserModel{
        return $this->repository->getUser($id);
    }

    function createUser(stdClass $body): UserModel {
        if($this->repository->getUserByMail($body->mail)){
            throw new EmailAlreadyExists("Email is already used !");
        }
        return $this->repository->createUser(new UserModel($body->mail, $body->password, 0));
    }

    function updateUser(stdClass $body): UserModel {
        
        if(!empty($body->mail)){
            $id = $this->repository->getUserByMail($body->mail)["id"];
            if($body->id != $id && $id != NULL){
                throw new EmailAlreadyExists("Email is already used !");
            }
        }
        return $this->repository->updateUser(new UserModel($body->mail, $body->password, $body->role, $body->id));
    }

    function deleteUser(int $id): void {
        $this->repository->DeleteUser($id);
    }

}