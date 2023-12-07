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

    public function getApartment($id): ApartmentModel{
        $query = 'SELECT * FROM APARTMENT WHERE id = $1';

        $res = $this->query($query, $id);

        $res = pg_fetch_assoc($res);
        if($res == NULL){
            throw new Exception("No apartment found");
        }

        return new ApartmentModel($res['id'], $res['address'], $res['area'], $res['owner'], $res['capacity'], $res['price'], $res['disponibility']);
    }

    public function getApartmentsBy($attribute, $value): ApartmentModel{
        $query = 'SELECT * FROM APARTMENT WHERE ' . $attribute . ' = $1';
        
        $res = $this->query($query, $value);
        
        $apartments = [];
        while($row = pg_fetch_assoc($res)){
            $apartments[] = new ApartmentModel($row['id'], $row['address'], $row['area'], $row['owner'], $row['capacity'], $row['price'], $row['disponibility'])
        }

        return $apartments;
    }

    public function getApartments(): array{
        $query = 'SELECT * FROM APARTMENT';

        $res = $this->query($query);

        $apartments = [];
        while($row = pg_fetch_assoc($res)){
            $apartments[] = new ApartmentModel($row['id'], $row['address'], $row['area'], $row['owner'], $row['capacity'], $row['price'], $row['disponibility'])
        }

        return $apartments;
    }

    public function getFreeApartments(): array{
        return getApartmentsBy("disponibility","TRUE");
    }

    public function updateApartment(): ApartmentModel{

    }

    public function deleteApartment($id): void{
        $query = 'DELETE FROM APARTMENTS WHERE id = $1';
        $res = $this->query($query, $id);

        if(!$res){
            throw new Exception(pg_last_error());
        }
        if(pg_affected_rows($res) == 0){
            throw new Exception('Apartment ID ' . $id . ' was not found.');
        }
    }
}
?>