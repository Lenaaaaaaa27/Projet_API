<?php

include_once 'user_model.php';

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

    public function CreateUser(int $role): UserModel{
        $query = pg_prepare($this->connection, "addUser", "INSERT INTO user (role) VALUES ($1) RETURNING id, role");
        $result = pg_execute($this->connection, "adduser", [$role]);

        if (!$result) {
            throw new HttpException(pg_last_error());
        }

        $user = pg_fetch_assoc($result);
        
        return new UserModel($user["id"], $user["role"]);
    }

    public function GetUser(int $id): UserModel{
        $query = pg_prepare($this->connection, "GetUser", "SELECT * FROM user WHERE id = $1");
        $result = pg_execute($this->connection, "GetUser", [$id]);

        if (!$result) {
            throw new HttpException(pg_last_error());
        }

        $user = pg_fetch_assoc($result);

        if ($user == null) {
            throw new NotFoundException("User not found.");
        }

        return new UserModel($user["id"], $user["role"]);
    }

    public function GetUsers():array{

        $query = pg_query($this->connection, "GetUsers", "SELECT * FROM user ORDER BY id DESC");
        $Users = [];

        if (!$result) {
            throw new HttpException(pg_last_error());
        }

        while($row = pg_fetch_assoc($query)){
            $Users[] = new UserModel($row["id"], $row["role"]);
        }

        return $Users;

    }

/*public function UpdateUser(): UserModel{
    A FAIRE
    } */

    public function DeleteUser(int $id){
        $query = pg_prepare($this->connection, "DeleteUser", "DELETE FROM user WHERE id = $1");
        $result = pg_execute($this->connection, "DeleteUser", [$id]);

        
        if (!$result ) {
            throw new HttpException(pg_last_error());
        }

        if (pg_affected_rows($result) == 0) {
            throw new NotFoundException("Todo not found.");
        }
    }
}