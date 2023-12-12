<?php
require_once 'apartmentModel.php';
require_once 'user/user_repository.php';
require_once 'commons/exceptions/repository_exceptions.php';

class ApartmentRepository{
    private $db;
    private $userRepository;

    public function __construct(){
        try{
            $this->db = pg_connect("host=database port=5432 dbname=rent_db user=rental password=password");
            if($this->db == NULL)
                throw new BDDException("Could not connect to database");
        }catch(Exception $e){
            throw new BDDException("Database connection failed :" . $e->getMessage());
        }

        $this->userRepository = new UserRepository();
    }

    private function query($req, ...$args): PgSql\Result {
        $prepared = pg_prepare($this->db, "", $req);
        if(!$prepared){
            throw new BDDException(pg_last_error($this->db));
        }

        if(preg_match("#^UPDATE#", $req))
            $args = $args[0];

        $res = pg_execute($this->db, "", $args);
        if(!$res){
            throw new BDDException(pg_last_error($this->db));
        }

        return $res;
    }

    private function makeURLFromObject($object): string{
        if(isset($object->mail)) $type = 'user';
        if(isset($object->area)) $type = 'apartment';
        if(isset($object->start_date)) $type = 'reservation';

        $url = "http://localhost:8083/index.php/restpatrop/$type/$object->id";
        return $url;
    }

    public function insertApartment(ApartmentModel $apart): ApartmentModel{
        $query = "INSERT INTO APARTMENT (area, capacity, address, disponibility, price, owner) 
                                VALUES ($1, $2, $3, $4, $5, $6) RETURNING *";

        $res = $this->query($query, $apart->area, $apart->capacity, $apart->address, $apart->disponibility, $apart->price, $apart->owner);
        $created = pg_fetch_assoc($res);

        $owner = $this->userRepository->getUser($created['owner']);
        $created['owner'] = ['mail' => $owner->mail, 'role' => $owner->role, "url" => $this->makeURLFromObject($owner)];

        return new ApartmentModel($created['id'], $created['address'], $created['area'], $created['owner'], $created['capacity'], $created['price'], $created['disponibility']);
    }

    /**
    * @return ApartmentModel[]
    */

    public function getApartmentsBy($attribute, $value): array{
        $query = 'SELECT * FROM APARTMENT WHERE ' . $attribute . ' = $1';
        
        $res = $this->query($query, $value);
        
        $apartments = [];
        while($row = pg_fetch_assoc($res)){
            $owner = $this->userRepository->getUser($row['owner']);
            $row['owner'] = ['mail' => $owner->mail, 'role' => $owner->role, "url" => $this->makeURLFromObject($owner)];
            $apartments[] = new ApartmentModel($row['id'], $row['address'], $row['area'], $row['owner'], $row['capacity'], $row['price'], $row['disponibility']);
        }

        return $apartments;
    }

    public function getApartment($id): ApartmentModel{
        $res = $this->getApartmentsBy('id', $id);
        $res = $res[0];

        if($res == NULL){
            throw new BDDNotFoundException("Apartment not found");
        }

        return $res;
    }

    /**
    * @return ApartmentModel[]
    */

    public function getApartments(): array{
        $query = 'SELECT * FROM APARTMENT';

        $res = $this->query($query);

        $apartments = [];
        while($row = pg_fetch_assoc($res)){
            $owner = $this->userRepository->getUser($row['owner']);
            $row['owner'] = ['mail' => $owner->mail, 'role' => $owner->role, "url" => $this->makeURLFromObject($owner)];
            $apartments[] = new ApartmentModel($row['id'], $row['address'], $row['area'], $row['owner'], $row['capacity'], $row['price'], $row['disponibility']);
        }

        return $apartments;
    }

    public function updateApartment(ApartmentModel $apart): ApartmentModel{ //May receive an apart like : {id, area, capacity, address, disponibility, price, owner}
        $query = 'UPDATE APARTMENT SET ';

        $first = true;
        $values = [];
        foreach($apart as $attr => $value){
            if($value != NULL){
                if(!$first){
                    $query .= ", ";
                }
            $values[] = $value;
            $query .= $attr . '=$' . count($values);
            $first = false;
            }
        }
        $query .= ' WHERE id='. $apart->id .' RETURNING *';

        $res = $this->query($query, $values);
        if(!$res){
            throw new BDDException(pg_last_error($this->db));
        }

        if(pg_affected_rows($res) == 0){
            throw new BDDNotFoundException("Apartment not found.");
        }

        $res = pg_fetch_assoc($res);

        $owner = $this->userRepository->getUser($res['owner']);
        $res['owner'] = ['mail' => $owner->mail, 'role' => $owner->role, "url" => $this->makeURLFromObject($owner)];
        
        return new ApartmentModel($res['id'], $res['address'], $res['area'], $res['owner'], $res['capacity'], $res['price'], $res['disponibility']);
    }

    public function deleteApartment($id): void{
        $query = 'DELETE FROM APARTMENT WHERE id = $1';
        $res = $this->query($query, $id);

        if(!$res){
            throw new BDDException(pg_last_error($this->db));
        }
        if(pg_affected_rows($res) == 0){
            throw new BDDNotFoundException('Apartment ID ' . $id . ' was not found.');
        }
    }
}
?>