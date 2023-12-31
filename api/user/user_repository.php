<?php

include_once 'user_model.php';
include_once 'commons/exceptions/repository_exceptions.php';

class UserRepository{

    private $connection = null;

    public function __construct() {
            try {
                $this->connection = pg_connect("host=database port=5432 dbname=rent_db user=rental password=password");
            if (  $this->connection == null ) {
                throw new BDDException("Could not connect to database.");
            }
        } catch (Exception $e) {
            throw new BDDException("Could not connect db: ". $e->getMessage());
        }
    }

    public function createUser(UserModel $body): UserModel{

        $query = pg_prepare($this->connection, "createUser", "INSERT INTO \"USER\" (mail, password, role) VALUES ($1, $2, $3) RETURNING id, mail, password, role");
        $result = pg_execute($this->connection, "createUser", [$body->mail, $body->password, $body->role]);

        if (!$result) {
            throw new HttpException(pg_last_error());
        }

        $user = pg_fetch_assoc($result);

        return new UserModel($user["mail"], NULL, $user["role"], $user["id"], NULL);
    }

    public function getUser(int $id): UserModel{
        $query = pg_prepare($this->connection, "getUser", "SELECT * FROM \"USER\" WHERE id = $1");
        $result = pg_execute($this->connection, "getUser", [$id]);

        if (!$result) {
            throw new HttpException(pg_last_error());
        }

        $user = pg_fetch_assoc($result);
        if ($user == null) {
            throw new NotFoundException("User not found.");
        }

        return new UserModel($user["mail"], NULL, $user["role"], $user["id"], NULL);
    }

    public function getUsers():array{

        $query = pg_query($this->connection, "SELECT * FROM \"USER\" ORDER BY id DESC");
        $Users = [];

        if (!$query) {
            throw new HttpException(pg_last_error());
        }

        while($row = pg_fetch_assoc($query)){
            $Users[] = new UserModel($row["mail"], NULL, $row["role"], $row["id"], NULL);
        }

        return $Users;
    }

    public function getUserByMail(string $mail){

        $query = pg_prepare($this->connection, "getUserByMail", "SELECT * FROM \"USER\" WHERE mail = $1");
        $result = pg_execute($this->connection, "getUserByMail", [$mail]);

        return pg_fetch_assoc($result);
    }

    public function updateUser(UserModel $userModel): UserModel{
        
        if(isset($userModel->role)){
            $query = pg_prepare($this->connection, "updateUserByAdmin", "UPDATE \"USER\" SET role = $1 WHERE id = $2 RETURNING id, mail, password, role, token");
            $result = pg_execute($this->connection, "updateUserByAdmin", array($userModel->role, $userModel->id));
        }else{
            $query = pg_prepare($this->connection, "updateUser", "UPDATE \"USER\" SET mail = $1, password = $2 WHERE id = $3 RETURNING id, mail, password, role, token");
            $result = pg_execute($this->connection, "updateUser", array($userModel->mail, $userModel->password, $userModel->id));
        }

        $user = pg_fetch_assoc($result);

        if($user["id"] == NULL){
            throw new NotFoundException("User not found.");
        }

        return new UserModel($user["mail"], NULL, $user["role"], $user["id"], NULL);
    }

    public function deleteUser(int $id){
        $query = pg_prepare($this->connection, "DeleteUser", "DELETE FROM \"USER\" WHERE id = $1");
        $result = pg_execute($this->connection, "DeleteUser", [$id]);

        
        if (!$result ) {
            throw new HttpException(pg_last_error());
        }

        if (pg_affected_rows($result) == 0) {
            throw new NotFoundException("User not found.");
        }
    }
}