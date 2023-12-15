<?php 

include_once 'user/user_model.php';
include_once 'commons/exceptions/repository_exceptions.php';
include_once 'commons/exceptions/service_exceptions.php';

class AuthentificationRepository{

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
    
    public function getUserByMail(string $mail, string $password):UserModel{

        $query = pg_prepare($this->connection, "getUserByMail", "SELECT * FROM \"USER\" WHERE mail = $1 AND password = $2");
        $result = pg_execute($this->connection, "getUserByMail", [$mail, $password]);

        $user = pg_fetch_assoc($result);

        if (!$user) {
            throw new FailConnexionAccount("Mail or/and password is wrong !");
        }

        return new UserModel($user["mail"], $user["password"], $user["role"], $user["id"], NULL);

    }

    public function getUserByToken(string $token){
        $query = pg_prepare($this->connection, "getUserByToken", "SELECT * FROM \"USER\" WHERE token = $1");
        $result = pg_execute($this->connection, "getUserByToken", [$token]);

        return pg_fetch_assoc($result);
    }

    public function AddToken(UserModel $userModel):object{

        $userModel->id = intval($userModel->id);
        $query = pg_prepare($this->connection, "addToken", "UPDATE \"USER\" SET token = $1 WHERE id = $2");
        $result = pg_execute($this->connection, "addToken", array($userModel->token, $userModel->id));

        return $result;
    }
    
    public function deleteToken(string $token){
        $query = pg_prepare($this->connection, "deleteToken", "UPDATE \"USER\" SET token = $1 WHERE token = $2");
        $result = pg_execute($this->connection, "deleteToken", array(NULL, $token));
        
        if(!$result){
            throw new BDDException("Could not execute the request");
        }
    }
}