<?php

include_once 'commons/exceptions/service_exceptions.php';
include_once 'auth/Token.php';
include_once 'user/user_model.php';

class Login{

    private $login = null;

    public function __construct() {
            try {
                $this->login = pg_connect("host=database port=5432 dbname=rent_db user=rental password=password");
            if (  $this->login == null ) {
                throw new BDDException("Could not connect to database.");
            }
        } catch (Exception $e) {
            throw new BDDException("Could not connect db: ". $e->getMessage());
        }
    }

    public function login(string $mail, string $password):UserModel{
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

    public function logout(int $id){

        try{
            $this->updateToken($id);
        }catch (Exception $e){
            throw new BDDException("Could not execute the request: ". $e->getMessage());
        }
        
    }
    public function getUserByMail(string $mail, string $password):array{

        $query = pg_prepare($this->login, "getUser", "SELECT * FROM \"USER\" WHERE mail = $1 AND password = $2");
        $result = pg_execute($this->login, "getUser", [$mail, $password]);

        
        if (!$result) {
            throw new FailConnexionAccount("Mail or/and password is wrong !");
        }

        return pg_fetch_assoc($result);
    }

    public function AddToken(UserModel $userModel):object{

        $userModel->id = intval($userModel->id);
        $query = pg_prepare($this->login, "updateUser", "UPDATE \"USER\" SET token = $1 WHERE id = $2");
        $result = pg_execute($this->login, "updateUser", array($userModel->token, $userModel->id));

        return $result;
    }


    public function updateToken(int $id){
        $query = pg_prepare($this->login, "updateUser", "UPDATE \"USER\" SET token = $1 WHERE id = $2");
        $result = pg_execute($this->login, "updateUser", array(NULL, $id));
        
        if(!$result){
            throw new BDDException("Could not execute the request");
        }
    }
    
  
}