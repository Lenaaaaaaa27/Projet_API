<?php 

include_once 'user_repository.php';
include_once 'user_model.php';
include_once 'commons/exceptions/service_exceptions.php';

class UserService{
    private $repository;

    function __construct() {
        $this->repository = new UserRepository();
    }
    
    function GetUsers(): array {
        return $this->repository->GetUsers();
    }

    function GetUser(int $id): UserModel{
        return $this->repository->GetUser($id);
    }

    function CreateUser(stdClass $body): UserModel {

        $users = $this->repository->GetUsers();

        foreach ($users as $user) {
            if ($user->mail === $body->mail) {
                throw new EmailAlreadyExist("Email is already used !");
            }
        }
        
        return $this->repository->CreateUser(new UserModel($body->mail, $body->password, $body->role));
    }

/*     function UpdateUser(int $id, stdClass $body): UserModel {
        return $this->repository->UpdateUser($id, new UserModel($body->url));
    } */

    function DeleteUser(int $id): void {
       $this->repository->DeleteUser($id);
    }

}