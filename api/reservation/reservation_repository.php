<?php
include_once 'reservation_model.php';
include_once 'commons/exceptions/repository_exceptions.php';

class ReservationRepository {
    private $connection = null;

    function __construct() {
        try {
            $this->connection = pg_connect("host=database port=5432 dbname=rent_db user=rental password=password");
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
            var_dump(pg_last_error($this->connection));
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
           $reservations[] = new ReservationModel($row['start_date'], $row['end_date'], $row['price'], $row['renter'], $row['apartment'],$row['id']);
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

        return new ReservationModel($reservation['start_date'], $reservation['end_date'], $reservation['price'], $reservation['renter'], $reservation['apartment'],$reservation['id']);
    }

    public function getReservationByDate(string $start_date, string $end_date, string $apartment, string $reservationId): mixed {
        $query = "SELECT * FROM RESERVATION WHERE 
        apartment = $1 AND 
        RESERVATION.id <> $4 AND
        (start_date BETWEEN $2 AND $3 OR 
        end_date BETWEEN $2 AND $3 OR
        (start_date <= $2 AND end_date >= $3))
        ";
        
        $result = $this->query($query, $apartment, $start_date, $end_date,$reservationId);

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
        $result = $this->query($query, $reservation->start_date,
                                        $reservation->end_date,
                                        $reservation->price,
                                        $reservation->renter,
                                        $reservation->apartment
                                    );

        $created = pg_fetch_assoc($result);
        return new ReservationModel($created['start_date'], $created['end_date'], $created['price'], $created['renter'], $created['apartment'], $created['id']);
    }

    public function updateReservation($id,ReservationModel $reservation): ReservationModel {
        $values = [];

        $query = "UPDATE RESERVATION SET ";

        if (isset($reservation->start_date)) {
            $values[] = $reservation->start_date;
            $query .= "start_date = $".sizeof($values);
        }
        if (isset($reservation->end_date)) {
            $values[] = $reservation->end_date;
            $query .= ",end_date = $".sizeof($values);
        }
        if (isset($reservation->price)) {
            $values[] = $reservation->price;
            $query .= ",price = $".sizeof($values);
        }
        if (isset($reservation->renter)) {
            $values[] = $reservation->renter;
            $query .= ",renter = $".sizeof($values);
        }
        if (isset($reservation->apartment)) {
            $values[] = $reservation->apartment;
            $query .= ",apartment = $".sizeof($values);
        }

        $query .= " WHERE id = $id RETURNING id, start_date,end_date,price,renter,apartment";

        $result = $this->query($query, ...$values);
        if (!$result) {
            throw new BDDException(pg_last_error());
        }

        if (pg_affected_rows($result) == 0) {
            throw new BDDNotFoundException("Reservation not found.");
        }

        $modified = pg_fetch_assoc($result);

        return new ReservationModel($modified['start_date'], $modified['end_date'], $modified['price'], $modified['renter'], $modified['apartment'],$modified['id']);
    }
}