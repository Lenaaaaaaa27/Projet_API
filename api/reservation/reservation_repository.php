<?php
include_once 'reservation_model.php';

class ReservationRepository {
    private $connection = null;

    function __construct() {
        try {
            $this->connection = pg_connect("host=database port=5433 dbname=rent_db user=rental password=password");
            if (  $this->connection == null ) {
                throw new BDDException("Could not connect to database.");
            }
        } catch (Exception $e) {
            throw new BDDException("Could not connect db: ". $e->getMessage());
        }
    }


    private function query(string $query,string ...$args): PgSql\Result {
        $prepared = pg_prepare($this->connection, "", $query);
 
        if (!$prepared) {
            throw new BDDException(pg_last_error($this->connection));
        }

        $result = pg_execute($this->connection, "", $args);
    
        if (!$result) {
            throw new BDDException(pg_last_error($this->connection));
        }

        return $result;
    }


     /**
    * @return reservationModel[]
    */
    public function getReservations(): array {
        $query =  "SELECT * FROM RESERVATION ORDER BY id DESC";
        $result = $this->query($query);

        $reservations = [];
        while ($row = pg_fetch_assoc($result)) {
           $reservations[] = new ReservationModel($row['id'], $row['start_date'], $row['end_date'], $row['price'], $row['renter'], $row['apartment']);
        }

        return $reservations;
    }


    /**
    * @return reservationModel
    */
    public function getReservation(int $id): reservationModel {
        $query =  "SELECT * FROM RESERVATION WHERE id = $1";
        $result = $this->query($query, $id);

        if (!$result) {
            throw new BDDException(pg_last_error());
        }

        $reservation = pg_fetch_assoc($result);

        if ($reservation == null) {
            throw new BDDNotFoundException("Reservation not found.");
        }

        return new ReservationModel($row['id'], $row['start_date'], $row['end_date'], $row['price'], $row['renter'], $row['apartment']);
    }

    public function geReservationBy(string $attribute, string $value): mixed {
        $query = "SELECT * FROM RESERVATION WHERE ".$attribute."=$1";
        
        $result = $this->query($query, $value);

        return pg_fetch_assoc($result);
    }

    public function deleteReservation(int $id): void {
        $query = "DELETE FROM RESERVATION WHERE id = $1";
        $result = $this->query($query, $id);

        if (!$result ) {
            throw new BDDException(pg_last_error());
        }

        if (pg_affected_rows($result) == 0) {
            throw new BDDNotFoundException("Reservation with ID ".$id." was not found.");
        }
    }


    public function createReservation(ReservationModel $reservation): ReservationModel {
        $query = "INSERT INTO RESERVATION (start_date,end_date,price,renter,apartment) VALUES ($1,$2,$3,$4,$5) RETURNING id, start_date,end_date,price,renter,apartment";
        
        $result = $this->query($query, $reservation->start_date
                                        $reservation->end_date
                                        $reservation->price 
                                        $reservation->renter 
                                        $reservation->apartment
                                    );

        $created = pg_fetch_assoc($result);
        return new ReservationModel($row['id'], $row['start_date'], $row['end_date'], $row['price'], $row['renter'], $row['apartment']);
    }

    public function updateReservation(int $id, ReservationModel $reservation): ReservationModel {
        $values = [];

        $query = "UPDATE RESERVATION SET ";

        if (isset($music_object->url)) {
            $values[] = $music_object->url;
            $query .= "url = $".sizeof($values);
        }

        $query .= " WHERE id = $id RETURNING id, url, created_at;";

        $result = $this->query($query, ...$values);
        if (!$result) {
            throw new BDDException(pg_last_error());
        }

        if (pg_affected_rows($result) == 0) {
            throw new BDDNotFoundException("Music not found.");
        }

        $modified = pg_fetch_assoc($result);

        return new MusicModel($modified['url'], $modified['id'], $modified['created_at']); 
    }



    function __construct($id = null, $start_date,$end_date, $price, $renter, $apartment) {
        

        $this->id = $id;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->price = $price;
        $this->renter = $renter;
        $this->apartment = $apartment;
    }

}