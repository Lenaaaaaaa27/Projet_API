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

        $salt = "DJSOJQ02ddqodkCSQDzqdzdKOPDKSDkapodkP09D92KC2ie2I";

        $body->password = $body->password . $salt;
        $body->password = hash('sha256', $body->password);
        
        $users = $this->repository->getUsers();

        foreach ($users as $user) {
            if ($user->mail === $body->mail) {
                throw new EmailAlreadyExist("Email is already used !");
            }
        }
        
        return $this->repository->createUser(new UserModel($body->mail, $body->password, $body->role, NULL));
    }

    function updateUser(stdClass $body): UserModel {
        $salt = "DJSOJQ02ddqodkCSQDzqdzdKOPDKSDkapodkP09D92KC2ie2I";

        $body->current_password = $body->current_password . $salt;
        $body->current_password = hash('sha256', $body->current_password);

        $user = $this->repository->getUser(intval($body->id));

        if ($user->password != $body->current_password) {
            throw new FailConnexionAccount("Mail or/and password is wrong !");
        }

        $body->new_password = $body->new_password . $salt;
        $body->new_password = hash('sha256', $body->new_password);

        return $this->repository->updateUser(new UserModel($body->new_mail, $body->new_password, $body->role, $body->id));
    }

    function deleteUser(int $id): void {
        $this->repository->DeleteUser($id);
    }

}