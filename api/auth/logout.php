<?php 

class Logout{
    public function logout(int $id){

    try{
        $this->updateToken($id);
    }catch (Exception $e){
        throw new BDDException("Could not execute the request: ". $e->getMessage());
    }
}

public function updateToken(int $id){
    $query = pg_prepare($this->login, "updateUser", "UPDATE \"USER\" SET token = $1 WHERE id = $2");
    $result = pg_execute($this->login, "updateUser", array(NULL, $id));
    
    if(!$result){
        throw new BDDException("Could not execute the request");
    }
}

    
}