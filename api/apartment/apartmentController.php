<?php
class ApartmentController{
    private $service;

    public function __construct(){
        $this->service = new ApartmentService();
    }

    public function router($req, $res){
        switch($req->getMethod()){
            case 'GET':
                if($req->getPathAt(3) === 'free')
                    $res->setContent($this->service->getFreeApartments());
                elseif($req->getPathAt(3) !== '')
                    $res->setContent($this->service->getApartment($req->getPathAt(3)));
                else
                    $res = json_encode($this->service->getApartments());
                break;

            case 'POST':
                $result = $this->service->createApartment($req->getBody());
                $res->setContent($result);
                break;

            case 'PATCH':
                if($req->getPathAt(3) === '')
                    throw new Exception('Please provide the ID of the apartment you want to modify.');

                $result = $this->service->modifyApartment($req->getPathAt(3), $res->getBody());
                $res->setContent($result);
                break;

            case 'DELETE':
                if($req->getPathAt(3) === '')
                    throw new Exception('Please provide the ID of the apartment you want to modify.');

                $this->service->deleteApartment($req->getPathAt(3));
                $res->setMessage('Successfully deleted Apartment id' . $req->getPathAt(3), 200);
                break;
        }

        return $res;
    }
}
?>