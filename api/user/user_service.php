<?php 
include_once 'user_repository.php';
include_once 'user_model.php';
include_once 'reservation/reservation_repository.php';
include_once 'apartment/apartmentRepository.php';
include_once 'commons/exceptions/service_exceptions.php';

class UserService{
    private $repositoryUser;
    private $repositoryReservation;
    private $repositoryApartment;

    function __construct() {
        $this->repositoryUser = new UserRepository();
        $this->repositoryReservation = new Reservationrepository();
        $this->repositoryApartment = new ApartmentRepository();
    }

    private function getMadeReservationsInfos($renterID): array{
        $madeReservations = $this->repositoryReservation->getReservationBy('renter',$renterID);
        $infos = [];
        foreach($madeReservations as $reservation){
            $infos[] = ['id' => $reservation->id, 
                      'start_date' => $reservation->start_date,
                      'end_date' => $reservation->end_date,
                      'price' => $reservation->price,
                      'apartment' => $reservation->apartment,
                      'url' => ''];
        }
        return $infos;
    }

    private function getOwnedApartsInfos($ownerID): array{
        $ownedApartments = $this->repositoryApartment->getApartmentsBy('owner', $ownerID);
        $infos = [];
        foreach($ownedApartments as $apartment){
            $infos[] = ['id' => $apartment->id, 
                    'address' => $apartment->address,
                    'area' => $apartment->area,
                    'disponibility' => $apartment->disponibility,
                    'url' => ''];
        }
        return $infos;
    }

    function getUsers(): array {
        $res = $this->repositoryUser->getUsers();
        foreach($res as $userModel){
            $userModel->ownedApartments = $this->getOwnedApartsInfos($userModel->id);
            $userModel->madeReservations = $this->getMadeReservationsInfos($userModel->id);
        }
        return $res;
    }

    function getUser(int $id): UserModel{
        $res = $this->repositoryUser->getUser($id);
        $res->ownedApartments = $this->getOwnedApartsInfos($res->id);
        $res->madeReservations = $this->getMadeReservationsInfos($res->id);
        return $res;
    }

    function createUser(stdClass $body): UserModel {
        if($this->repositoryUser->getUserByMail($body->mail)){
            throw new EmailAlreadyExists("Email is already used !");
        }
        $res = $this->repositoryUser->createUser(new UserModel($body->mail, $body->password, 0));
        $res->ownedApartments = $this->getOwnedApartsInfos($res->id);
        $res->madeReservations = $this->getMadeReservationsInfos($res->id);
        return $res;
    }

    function updateUser(stdClass $body): UserModel {
        
        if(!empty($body->mail)){
            $id = $this->repositoryUser->getUserByMail($body->mail)["id"];
            if($body->id != $id && $id != NULL){
                throw new EmailAlreadyExists("Email is already used !");
            }
        }
        $res = $this->repositoryUser->updateUser(new UserModel($body->mail, $body->password, $body->role, $body->id));
        $res->ownedApartments = $this->getOwnedApartsInfos($res->id);
        $res->madeReservations = $this->getMadeReservationsInfos($res->id);
        return $res;
    }

    function deleteUser(int $id): void {
        $this->repositoryUser->DeleteUser($id);
    }

}