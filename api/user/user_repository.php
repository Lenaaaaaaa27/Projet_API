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

    public function CreateUser(UserModel $body): UserModel{

        $query = pg_prepare($this->connection, "CreateUser", "INSERT INTO \"USER\" (mail, password, role) VALUES ($1, $2, $3) RETURNING id, mail, password, role");
        $result = pg_execute($this->connection, "CreateUser", [$body->mail, $body->password, $body->role]);

        if (!$result) {
            throw new HttpException(pg_last_error());
        }

        $user = pg_fetch_assoc($result);

        return new UserModel($user["mail"], $user["password"], $user["role"], $user["id"]);
    }

    public function GetUser(int $id): UserModel{
        $query = pg_prepare($this->connection, "GetUser", "SELECT * FROM \"USER\" WHERE id = $1");
        $result = pg_execute($this->connection, "GetUser", [$id]);

        if (!$result) {
            throw new HttpException(pg_last_error());
        }

        $user = pg_fetch_assoc($result);
        if ($user == null) {
            throw new NotFoundException("User not found.");
        }

        return new UserModel($user["mail"], $user["password"], $user["role"], $user["id"]);
    }

    public function GetUsers():array{

        $query = pg_query($this->connection, "SELECT * FROM \"USER\" ORDER BY id DESC");
        $Users = [];

        if (!$query) {
            throw new HttpException(pg_last_error());
        }

        while($row = pg_fetch_assoc($query)){
            $Users[] = new UserModel($row["mail"], $row["password"], $row["role"], $row["id"]);
        }

        return $Users;
    }

    public function UpdateUser(UserModel $body): UserModel{
        
    }

    public function DeleteUser(int $id){
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