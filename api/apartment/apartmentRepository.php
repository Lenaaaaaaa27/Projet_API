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
        $args = [$this->area, $this->capacity, $this->address, $this->disponibility, $this->price, $this->owner];

        $res = $this->query($query, $args);
        $created = pg_fetch_assoc($res);

        return new ApartmentModel($created['id'], $created['address'], $created['area'], $created['owner'], $created['capacity'], $created['price'], $created['disponibility']);
    }

    public function getApartmentBy($attribute, $value): ApartmentModel{
        $query = 'SELECT 
                    address, area,
                    capacity, disponibility,
                    price, owner
                  FROM APARTMENT WHERE ' . $attribute . ' = $1';
        
        $res = $this->query($query, $value);
        $res = pg_fetch_assoc($res);
        if($res == NULL){
            throw new Exception("Apartment not found.");
        }

        return new ApartmentModel($res['id'], $res['address'], $res['area'], $res['owner'], $res['capacity'], $res['price'], $res['disponibility']);
    }

    public function getApartments(): array{

    }

    public function getFreeApartments(): array{

    }

    public function updateApartment(): ApartmentModel{

    }

    public function deleteApartment(): void{

    }
}
?>