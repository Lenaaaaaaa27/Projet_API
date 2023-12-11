<?php 

class Logout{

    private $logout = null;

    public function __construct() {
            try {
                $this->logout = pg_connect("host=database port=5432 dbname=rent_db user=rental password=password");
            if (  $this->logout == null ) {
                throw new BDDException("Could not connect to database.");
            }
        } catch (Exception $e) {
            throw new BDDException("Could not connect db: ". $e->getMessage());
        }
    }

    public function logout(int $id){
        try{
            $this->updateToken($id);
        }catch (Exception $e){
            throw new BDDException("Could not execute the request: ". $e->getMessage());
        }
    }

    public function updateToken(int $id){
        $query = pg_prepare($this->logout, "updateUser", "UPDATE \"USER\" SET token = $1 WHERE id = $2");
        $result = pg_execute($this->logout, "updateUser", array(NULL, $id));
        
        if(!$result){
            throw new BDDException("Could not execute the request");
        }
    }

    
}