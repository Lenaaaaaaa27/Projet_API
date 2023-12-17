<?php
require_once 'apartmentService.php';
require_once 'apartmentModel.php';
require_once 'commons/exceptions/controller_exceptions.php';
require_once 'commons/response.php';
require_once 'commons/request.php';

class ApartmentController{
    private $service;

    public function __construct(){
        $this->service = new ApartmentService();
    }

    public function dispatch(Request $req, Response $res): void{
        $result = NULL;
        switch($req->getMethod()){
            case 'GET':
                    $result = $this->getMethodController($req, $res);
                break;

            case 'POST':
                $result = $this->service->createApartment($req->getBody());
                break;

            case 'PATCH':
                if($req->getPathAt(4) === '')
                    throw new BadRequestException('Please provide the ID of the apartment you want to modify.');

                if($req->getPathAt(5) == 'switch')
                    $result = $this->service->switchDisponibityOn($req->getPathAt(4));
                else
                    $result = $this->service->modifyApartment($req->getPathAt(4), $req->getBody());

                break;

            case 'DELETE':
                if($req->getPathAt(4) === '')
                    throw new BadRequestException('Please provide the ID of the apartment you want to delete.');

                $this->service->deleteApartment($req->getPathAt(4));
                $res->setMessage('Successfully deleted Apartment of ID ' . $req->getPathAt(4), 200);
                break;
        }
    }

    private function getMethodController(Request $req, Response $res): mixed{
        switch($req->getPathAt(4)){
            case 'free':
                $result = $this->service->getFreeApartments();
                break;

            case 'owner':
                if(!isset($_GET['id']))
                    throw new BadRequestException('Please provide ID of the owner as an argument.');
                $result = $this->service->getApartmentsByOwner($_GET['id']);
                break;

            case '':
                $result = $this->service->getApartments();
                break;

            default:
                $result = $this->service->getApartment($req->getPathAt(4));
        }
        return $result;
    }
}
?>