<?php
class ApartmentRepository{
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

    private function query($req, ...$args): PgSql\Result {
        $prepared = pg_prepare($this->db, "", $query);
        if(!$prepared){
            throw new Exception(pg_last_error($this->db));
        }

        $res = pg_execute($this->db, "", $args);
        if(!$res){
            throw new Exception(pg_last_error($this->db));
        }

        return $res;
    }

    public function newApartment(ApartmentModel $apart): ApartmentModel{
        $query = "INSERT INTO APARTMENT (area, capacity, address, disponibility, price, owner) 
                                VALUES ($1, $2, $3, $4, $5, $6)";
        $args = [$this->area, $this->capacity, $this->address, $this->disponibilty, $this->price, $this->owner];

        $res = $this->query($query, $args);
        $created = pg_fetch_assoc($res);

        return new ApartmentModel($created['id'], $created['address'], $created['area'], $created['owner'], $created['capacity'], $created['price'], $created['disponibility']);
    }
}
?>