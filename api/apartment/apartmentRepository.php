<?php
class apartmentRepository{
    private $db;

    public function __construct(){
        try{
            $this->db = pg_connect("host=database port=5432 dbname=rent_db user=rental password=password");
            if($this->db == NULL)
                throw new Exception("Could not connect to database");
        }catch(Exception $e){
            throw new Exception("Database connection failed :" . $e->getMessage());
        }
    }
}
?>