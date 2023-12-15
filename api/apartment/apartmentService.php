<?php
require_once 'apartmentRepository.php';
require_once 'apartmentModel.php';
require_once 'commons/exceptions/service_exceptions.php';

class ApartmentService{
    private $repository;

    public function __construct(){
        $this->repository = new ApartmentRepository();
    }

    public function getApartments(): array{
        return $this->repository->getApartments();
    }

    public function getApartment($id): ApartmentModel {
        return $this->repository->getApartment($id);
    }

    public function getFreeApartments(): array{
        return $this->repository->getApartmentsBy("disponibility","TRUE");
    }

    public function getApartmentsByOwner($id): array{
        return $this->repository->getApartmentsBy("owner", $id);
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
        $existing = $this->repository->getApartmentsBy("address", $tempFlat->address);

        if($existing != NULL)
            throw new ValueTakenExcepiton("Apartment with address '$tempFlat->address' already exists.");

        return $this->repository->insertApartment($tempFlat);
    }

    public function deleteApartment($id): void{
        $this->repository->deleteApartment($id);
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

        return $this->repository->updateApartment($tempFlat);
    }

    public function switchDisponibityOn($id): ApartmentModel{
        return $this->repository->switchDisponibility($id);
    }
}
?>
