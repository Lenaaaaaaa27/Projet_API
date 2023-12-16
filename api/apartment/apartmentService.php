<?php
require_once 'apartmentRepository.php';
require_once 'apartmentModel.php';
require_once 'user/user_repository.php';
require_once 'commons/exceptions/service_exceptions.php';

class ApartmentService{
    private $apartRepository;
    private $userRepository;

    public function __construct(){
        $this->apartRepository = new ApartmentRepository();
        $this->userRepository = new UserRepository();
    }

    private function makeURLFromObject($object): string{
        if(isset($object->mail)) $type = 'user';
        if(isset($object->area)) $type = 'apartment';
        if(isset($object->start_date)) $type = 'reservation';

        $url = "http://localhost:8083/index.php/restpatrop/$type/$object->id";
        return $url;
    }

    private function getUserInfos($id): array{
        $owner = $this->userRepository->getUser($id);
        $infos = ['id' => $id, 'mail' => $owner->mail, 'role' => $owner->role, 'url' => $this->makeURLFromObject($owner)];
        return $infos;
    }

    public function getApartments(): array{
        $res = $this->apartRepository->getApartments();

        foreach($res as $value)
            $value->owner = $this->getUserInfos($value->owner);

        return $res;
    }

    public function getApartment($id): ApartmentModel {
        $res = $this->apartRepository->getApartment($id);
        $res->owner = $this->getUserInfos($res->owner);
        return $res;
    }

    public function getFreeApartments(): array{
        $res = $this->apartRepository->getApartmentsBy("disponibility","TRUE");
        
        foreach($res as $value)
            $value->owner = $this->getUserInfos($value->owner);
        
        return $res;
    }

    public function getApartmentsByOwner($id): array{
        $res = $this->apartRepository->getApartmentsBy("owner", $id);
        
        foreach($res as $value)
            $value->owner = $this->getUserInfos($value->owner);
        
        return $res;
    }

    public function createApartment(stdClass $body): ApartmentModel {
        $tempFlat = new ApartmentModel(NULL, NULL, NULL, NULL, NULL, NULL, NULL);
        foreach($tempFlat as $key => $value){
            if($key == "id") continue;
            if(!isset($body->$key))
                throw new ValidationException('Impossible creation : property ' . $key . ' is not provided.');

            $tempFlat->$key = $body->$key;
        }
        
        //We check then if this apartment already exists in the database
        $existing = $this->apartRepository->getApartmentsBy("address", $tempFlat->address);

        if($existing != NULL)
            throw new ValueTakenException("Apartment with address '$tempFlat->address' already exists.");

        $res = $this->apartRepository->insertApartment($tempFlat);
        $res->owner = $this->getUserInfos($res->owner);

        return $res;
    }

    public function deleteApartment($id): void{
        $this->apartRepository->deleteApartment($id);
    }

    public function modifyApartment($id, stdClass $body): ApartmentModel{
        if(empty(get_object_vars($body)))
            throw new ValidationException('No change needed.');

        if(isset($body->id) && $body->id != $id)
            throw new ValidationException('Modify an ID is not possible.');

        $tempFlat = new ApartmentModel($id, NULL, NULL, NULL, NULL, NULL, NULL);

        foreach($body as $key => $value){
            if($key == "id") continue;
            if(!property_exists($tempFlat, $key))
                throw new ValidationException('Modifying ' . $key . ' is not possible : Does not exist');

            $tempFlat->$key = $body->$key;
        }

        $res = $this->apartRepository->updateApartment($tempFlat);
        $res->owner = $this->getUserInfos($res->owner);
        
        return $res;
    }

    public function switchDisponibityOn($id): ApartmentModel{
        $res = $this->apartRepository->switchDisponibility($id);
        $res->owner = $this->getUserInfos($res->owner);
        
        return $res;
    }
}
?>
