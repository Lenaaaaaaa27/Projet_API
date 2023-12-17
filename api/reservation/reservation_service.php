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

    private function makeURLFromObject($object): string{
        if(isset($object->mail)) $type = 'user';
        if(isset($object->area)) $type = 'apartment';
        if(isset($object->start_date)) $type = 'reservation';

        $url = "http://localhost:8083/index.php/restpatrop/$type/$object->id";
        return $url;
    }

    private function getUserInfos($id): array{
        $renter = $this->repositoryUser->getUser($id);
        $infos = ['id' => $id, 'mail' => $renter->mail, 'role' => $renter->role, 'url' => $this->makeURLFromObject($renter)];
        return $infos;
    }

    private function getApartInfos($id): array{
        $apartment = $this->repositoryApartment->getApartment($id);
        $infos = ['id' => $id, 
                  'address' => $apartment->address,
                  'area' => $apartment->area,
                  'capacity' => $apartment->capacity,
                  'price' => $apartment->price,
                  'owner' => $apartment->owner,
                  'url' => $this->makeURLFromObject($apartment)];
        return $infos;
    }

    function getReservations() : array {
        $res = $this->repositoryReservation->getReservations();
        foreach($res as $value){
            $value->renter = $this->getUserInfos($value->renter);
            $value->apartment = $this->getApartInfos($value->apartment);
        }
        return $res;
    }

    function getReservationsBetween($start_date, $end_date) : array {
        $res = $this->repositoryReservation->getReservationsBetween($start_date, $end_date);
        foreach($res as $value){
            $value->renter = $this->getUserInfos($value->renter);
            $value->apartment = $this->getApartInfos($value->apartment);
        }
        return $res;
    }

    function getReservation(int $id) : ReservationModel {
        $res = $this->repositoryReservation->getReservation($id);
        $res->renter = $this->getUserInfos($res->renter);
        $res->apartment = $this->getApartInfos($res->apartment);
        return $res;
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

        $apartment = $this->repositoryApartment->getApartment($body->apartment);

       $body->price = $apartment->price * ((( strtotime($body->end_date) - strtotime($body->start_date)))/(24 * 60 * 60));
        
        $res = $this->repositoryReservation->createReservation(new ReservationModel($body->start_date, $body->end_date, $body->price, $body->renter, $body->apartment,NULL));
        $res->renter = $this->getUserInfos($res->renter);
        $res->apartment = $this->getApartInfos($res->apartment);
        return $res;
    }

    public function updateReservation($id, stdClass $body) : ReservationModel {

        if (!isset($body->start_date)) {
            throw new ValidationException("Please provide a start date for your reservation !");
        }
        if (!isset($body->end_date)) {
            throw new ValidationException("Please provide an end date for your reservation !");
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
        
        $existing = $this->repositoryReservation->getReservationsBetween($body->start_date, $body->start_date, $body->apartment,$id);

        if($existing) {
            throw new ValueTakenException("the apartment is already booked during this period");
        } 

        $apartment = $this->repositoryApartment->getApartment($body->apartment);

        $body->price = $apartment->price * ((( strtotime($body->end_date) - strtotime($body->start_date)))/(24 * 60 * 60));
        
        $res = $this->repositoryReservation->updateReservation($id, new ReservationModel($body->start_date, $body->end_date, $body->price, $body->renter, $body->apartment,null));
        $res->renter = $this->getUserInfos($res->renter);
        $res->apartment = $this->getApartInfos($res->apartment);
        return $res;
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