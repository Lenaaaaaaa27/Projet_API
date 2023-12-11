<?php

include_once 'commons/exceptions/service_exceptions.php';
include_once 'auth/Token.php';
include_once 'user/user_model.php';

class Login{

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

    public function Connection(string $mail, string $password):UserModel{
        $salt = "DJSOJQ02ddqodkCSQDzqdzdKOPDKSDkapodkP09D92KC2ie2I";

        $password = $password . $salt;
        $password = hash('sha256', $password);


        $user = $this->getUserByMail($mail, $password);

        $UserModel = new UserModel($user["mail"], $user["password"], $user["role"], $user["id"], NULL);
        $UserModel->token = GenerateToken($UserModel);

        if (!$this->AddToken($UserModel)){
            echo 'ca marche pas';
            exit;
        }

        return $UserModel;

    }

    public function Deconnection(int $id){

        try{
            $this->DeleteToken($id);
        }catch (Exception $e){
            throw new BDDException("Could not execute the request: ". $e->getMessage());
        }
        
    }
    public function getUserByMail(string $mail, string $password):array{

        $query = pg_prepare($this->connection, "getUser", "SELECT * FROM \"USER\" WHERE mail = $1 AND password = $2");
        $result = pg_execute($this->connection, "getUser", [$mail, $password]);

        
        if (!$result) {
            throw new FailConnexionAccount("Mail or/and password is wrong !");
        }

        return pg_fetch_assoc($result);
    }

    public function AddToken(UserModel $userModel):object{

        $userModel->id = intval($userModel->id);
        $query = pg_prepare($this->connection, "updateUser", "UPDATE \"USER\" SET token = $1 WHERE id = $2");
        $result = pg_execute($this->connection, "updateUser", array($userModel->token, $userModel->id));

        return $result;
    }

    public function DeleteToken(int $id){
        $query = pg_prepare($this->connection, "updateUser", "UPDATE \"USER\" SET token = $1 WHERE id = $2");
        $result = pg_execute($this->connection, "updateUser", array(NULL, $id));
        
        if(!$result){
            throw new BDDException("Could not execute the request");
        }
    }

  
}