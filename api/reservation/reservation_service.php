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

        return $this->repositoryReservation->getReservations();
    }

    function getReservation(int $id) : ReservationModel {
        
        return $this->repositoryReservation->getReservation($id);
    }

    function createReservation(stdClass $body) : ReservationModel {
        
        if (!isset($body->start_date)) {
            throw new ValidationException("Please provide a start date for your reservation !");
        }
        if (!isset($body->end_date)) {
            throw new ValidationException("Please provide an end date for your reservation !");
        }
        if (!isset($body->price)) {
            throw new ValidationException("Please provide a price for your reservation !");
        }
        if (!isset($body->renter)) {
            throw new ValidationException("Please provide a renter for your reservation !");
        }
        if (!isset($body->apartment)) {
            throw new ValidationException("Please indicate an apartment for your reservation !");
        }
        if(strtotime($body->end_date) < strtotime($body->start_date) + (24 * 60 * 60)) {
            throw new ValueTakenExcepiton("A reservation cannot be made for less than one day.");
        }

        $existing = $this->repositoryReservation->getReservationByDate($body->start_date, $body->start_date, $body->apartment,"0");

        if($existing) {
            throw new ValueTakenExcepiton("the apartment is already booked during this period");
        } 

        $user = $this->repositoryUser->getUser($body->renter);
        $apartment = $this->repositoryApartment->getApartment($body->apartment);

       $body->price = $apartment->price * ((( strtotime($body->end_date) - strtotime($body->start_date)))/(24 * 60 * 60));
        
        return $this->repositoryReservation->createReservation(new ReservationModel($body->start_date, $body->end_date, $body->price, $body->renter, $body->apartment,NULL));
    }

    public function updateReservation($id, stdClass $body) : ReservationModel {

        if (!isset($body->start_date)) {
            throw new ValidationException("Please provide a start date for your reservation !");
        }
        if (!isset($body->end_date)) {
            throw new ValidationException("Please provide an end date for your reservation !");
        }
        if (!isset($body->price)) {
            throw new ValidationException("Please provide a price for your reservation !");
        }
        if (!isset($body->renter)) {
            throw new ValidationException("Please provide a renter for your reservation !");
        }
        if (!isset($body->apartment)) {
            throw new ValidationException("Please indicate an apartment for your reservation !");
        }

        if(strtotime($body->end_date) < strtotime($body->start_date) + (24 * 60 * 60)) {
            throw new ValueTakenExcepiton("A reservation cannot be made for less than one day.");
        }
        
        $existing = $this->repositoryReservation->getReservationByDate($body->start_date, $body->start_date, $body->apartment,$id);

        if($existing) {
            throw new ValueTakenExcepiton("the apartment is already booked during this period");
        } 

        $user = $this->repositoryUser->getUser($body->renter);
        $apartment = $this->repositoryApartment->getApartment($body->apartment);

        $body->price = $apartment->price * ((( strtotime($body->end_date) - strtotime($body->start_date)))/(24 * 60 * 60));
        

        return $this->repositoryReservation->updateReservation($id, new ReservationModel($body->start_date, $body->end_date, $body->price, $body->renter, $body->apartment,null));
    }

    public function deleteReservation(int $id): void { 
        $this->repositoryReservation->deleteReservation($id);
    }
}