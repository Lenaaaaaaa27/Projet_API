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

    public function createApartment(stdClass $body): ApartmentModel {
        $tempFlat = new ApartmentModel(NULL, NULL, NULL, NULL, NULL, NULL, NULL);
        foreach($tempFlat as $key){
            if($key == "id") continue;
            if(!isset($body->$key))
                throw new Exception('Impossible creation : ' . $key . ' is needed.');

            $tempFlat->$key = $body->$key;
        }

        //Essayer de vérifier par l'adresse si un appart existe déjà. Faire quand on pourra tester

        return $this->repository->insertApartment($tempFlat);
    }

    public function deleteApartment($id): void{
        $this->repository->deleteApartment($id);
    }

    public function modifyApartment($id, stdClass $body): ApartmentModel{
        if(empty(get_object_vars($body)))
            throw new Exception('No change needed.');

        if(isset($body->id) && $body->id != $id)
            throw new Exception('Modify an ID is not possible.');

        $tempFlat = new ApartmentModel($id, NULL, NULL, NULL, NULL, NULL, NULL);

        foreach($tempFlat as $key){
            if($key == "id") continue;
            if(!property_exists($tempFlat, $key))
                throw new Exception('Modifying ' . $key . ' is not possible : Does not exist');

            $tempFlat->$key = $body->$key;
        }

        return $this->repository->updateApartment($tempFlat);
    }
}
?>
