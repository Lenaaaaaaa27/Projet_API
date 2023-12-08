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

        $salt = "DJSOJQ02ddqodkCSQDzqdzdKOPDKSDkapodkP09D92KC2ie2I";

        $body->password = $body->password . $salt;
        $body->password = hash('sha256', $body->password);
        
        $users = $this->repository->GetUsers();

        foreach ($users as $user) {
            if ($user->mail === $body->mail) {
                throw new EmailAlreadyExist("Email is already used !");
            }
        }
        
        return $this->repository->CreateUser(new UserModel($body->mail, $body->password, $body->role, NULL));
    }

    function UpdateUser(stdClass $body): UserModel {
        $salt = "DJSOJQ02ddqodkCSQDzqdzdKOPDKSDkapodkP09D92KC2ie2I";

        $body->password = $body->password . $salt;
        $body->password = hash('sha256', $body->password);

        $user = $this->repository->GetUser(intval($body->id));

        if ($user->password != $body->password) {
            throw new FailConnexionAccount("Mail or/and password is wrong !");
        }

        return $this->repository->UpdateUser(new UserModel($body->mail, $body->password, $body->role, $body->id));
    }

    function DeleteUser(stdClass $body): void {

        $salt = "DJSOJQ02ddqodkCSQDzqdzdKOPDKSDkapodkP09D92KC2ie2I";

        $body->password = $body->password . $salt;
        $body->password = hash('sha256', $body->password);

        $user = $this->repository->GetUser(intval($body->id));

        if ($user->password != $body->password) {
            throw new FailConnexionAccount("Mail or/and password is wrong !");
        }

        $this->repository->DeleteUser($body->id);
    }

}