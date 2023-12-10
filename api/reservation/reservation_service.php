<?php 

include_once 'musics_model.php';
include_once 'musics_repository.php';
include_once 'commons/exceptions/service_exceptions.php';

class ReservationService {
    private $repository;

    function __construct() {
        $this->repository = new ReservationRepository();

    }


    function getReservations() : array {

        return $this->repository->getReservations();
    }

    function getReservation(int $id) : ReservationModel {
        
        return $this->repository->getReservation($id);
    }

    function createReservation(stdClass $body) : ReservationModel {
        if (isset($body->start_date)) {
            throw new ValidationException("Please provide a start date for your reservation !");
        }
        if (isset($body->end_date)) {
            throw new ValidationException("Please provide an end date for your reservation !");
        }
        if (isset($body->price)) {
            throw new ValidationException("Please provide a price for your reservation !");
        }
        if (isset($body->renter)) {
            throw new ValidationException("Please provide a renter for your reservation !");
        }
        if (isset($body->apartment)) {
            throw new ValidationException("Please indicate an apartment for your reservation !");
        }

        if($body->end_date >= $body->start_date + (24 * 60 * 60)) {
            throw new ValueTakenExcepiton("A reservation cannot be made for less than one day.");
        }

        $existing = $this->repository->getReservationByDate($body->start_date, $body->start_date, $body->apartment);

        if($existing) {
            throw new ValueTakenExcepiton("the apartment is already booked during this period");
        } 

        /* ajouter calcul du prix une fois les requete des apartment recupérer 
        */
        
        return $this->repository->createReservation(new ReservationModel(null, $body->start_date, $body->end_date, $body->price, $body->renter, $body->apartment));
    }

    public function updateReservation(stdClass $body) : ReservationModel {

        if (isset($body->start_date)) {
            throw new ValidationException("Please provide a start date for your reservation !");
        }
        if (isset($body->end_date)) {
            throw new ValidationException("Please provide an end date for your reservation !");
        }
        if (isset($body->price)) {
            throw new ValidationException("Please provide a price for your reservation !");
        }
        if (isset($body->renter)) {
            throw new ValidationException("Please provide a renter for your reservation !");
        }
        if (isset($body->apartment)) {
            throw new ValidationException("Please indicate an apartment for your reservation !");
        }

        if($body->end_date >= $body->start_date + (24 * 60 * 60)) {
            throw new ValueTakenExcepiton("A reservation cannot be made for less than one day.");
        }
        
        
        $existing = $this->repository->getReservationByDate($body->start_date, $body->start_date, $body->apartment);

        if($existing) {
            throw new ValueTakenExcepiton("the apartment is already booked during this period");
        } 

        /* ajouter calcul du prix une fois les requete des apartment recupérer 
        */

        return $this->repository->updateReservation($body->id, new ReservationModel(null, $body->start_date, $body->end_date, $body->price, $body->renter, $body->apartment));
    }

    public function deleteReservation(int $id): void { 
        return $this->repository->deleteReservation($id);
    }
}