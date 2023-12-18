<?php 

include_once 'reservation_model.php';
include_once 'reservation_repository.php';
include_once 'user/user_repository.php';
include_once 'apartment/apartmentRepository.php';
include_once 'commons/exceptions/service_exceptions.php';

class ReservationService {
    private $repositoryReservation;
    private $repositoryUser;
    private $repositoryApartment;

    function __construct() {
        $this->repositoryReservation = new Reservationrepository();
        $this->repositoryUser = new UserRepository();
        $this->repositoryApartment = new ApartmentRepository();

    }


    function getReservations() : array {

        $reservations = $this->repositoryReservation->getReservations();
        foreach($reservations as $reservation) {
            $reservation->apartment = $this->repositoryApartment->getApartmentsBy("id",$reservation->apartment);
            $reservation->renter = $this->repositoryUser->getUser($reservation->renter);
        }
        return $reservations;
    }

    function getReservationsBetween($start_date, $end_date) : array {
        $reservations = $this->repositoryReservation->getReservationsBetween($start_date, $end_date);
        foreach($reservations as $reservation) {
            $reservation->apartment = $this->repositoryApartment->getApartmentsBy("id",$reservation->apartment);
            $reservation->renter = $this->repositoryUser->getUser($reservation->renter);
        }
        return $reservations;
    }

    function getReservation(int $id) : ReservationModel {
        
        $result = $this->repositoryReservation->getReservation($id);
        $result->apartment = $this->repositoryApartment->getApartmentsBy("id",$result->apartment);
        $result->renter = $this->repositoryUser->getUser($result->renter);
        return $result;
    }

    function createReservation(stdClass $body) : ReservationModel {
        
        if (!isset($body->start_date)) {
            throw new ValidationException("Please provide a start date for your reservation !");
        }
        if (!isset($body->end_date)) {
            throw new ValidationException("Please provide an end date for your reservation !");
        }
        if(!$this->validDate($body->start_date) || !$this->validDate($body->end_date)) {
            throw new ValidationException("date format is invalid.");
        }
        if (!isset($body->renter) || !is_numeric($body->renter)) {
            throw new ValidationException("Please provide a renter for your reservation !");
        }
        if (!isset($body->apartment) || !is_numeric($body->apartment)) {
            throw new ValidationException("Please indicate an apartment for your reservation !");
        }
        if(strtotime($body->end_date) < strtotime($body->start_date) + (24 * 60 * 60)) {
            throw new ValueTakenException("A reservation cannot be made for less than one day.");
        }
        $existing = $this->repositoryReservation->getReservationByDate($body->start_date, $body->start_date, $body->apartment,"0");

        if($existing) {
            throw new ValueTakenException("the apartment is already booked during this period");
        } 

        $user = $this->repositoryUser->getUser(intval($body->renter));
        $apartment = $this->repositoryApartment->getApartment($body->apartment);

       $body->price = $apartment->price * ((( strtotime($body->end_date) - strtotime($body->start_date)))/(24 * 60 * 60));
        
        $reservation =  $this->repositoryReservation->createReservation(new ReservationModel($body->start_date, $body->end_date, $body->price, $body->renter, $body->apartment,NULL));
        $reservation->renter = $user;
        $reservation->apartment = $apartment;
        return $reservation;
    }

    public function updateReservation($id, stdClass $body) : ReservationModel {

        if (!isset($body->start_date)) {
            throw new ValidationException("Please provide a start date for your reservation !");
        }
        if (!isset($body->end_date)) {
            throw new ValidationException("Please provide an end date for your reservation !");
        }
        if(strtotime($body->end_date) < strtotime($body->start_date) + (24 * 60 * 60)) {
            throw new ValueTakenException("A reservation cannot be made for less than one day.");
        }

        $oldReservation = $this->repositoryReservation->getReservation(intval($id));
        
        $existing = $this->repositoryReservation->getReservationByDate($body->start_date, $body->start_date, $oldReservation->apartment,$id);

        if($existing) {
            throw new ValueTakenException("the apartment is already booked during this period");
        } 
        

        $result = $this->repositoryReservation->updateReservation($id, new ReservationModel($body->start_date, $body->end_date, $body->price, $body->renter, $body->apartment, null));

        $apartment = $this->repositoryApartment->getApartment($result->apartment);

        $result->price = $apartment->price * ((( strtotime($body->end_date) - strtotime($body->start_date)))/(24 * 60 * 60));
        return $result;
    }

    public function deleteReservation(int $id): void { 
        $this->repositoryReservation->deleteReservation($id);
    }

    public function validDate($date, $format = 'Y-m-d'):bool{
        if(!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) return false;
        $dt = DateTime::createFromFormat($format, $date);
        return $dt->format($format) === $date;
      }
}