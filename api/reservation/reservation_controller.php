<?php
// reservation_controller.php

include_once 'reservation_model.php';
include_once 'reservation_service.php';
include_once './commons/request.php';
include_once './commons/response.php';

class MusicsController {
    private $service;    

    function __construct() {
        $this->service = new ReservationService();
    }

function dispatch(Request $req, Response $res): void {
    switch($req->getMethod()) {
        case 'GET':
            if ($req->getPathAt(3) !== "" && is_string($req->getPathAt(3))) {
                $res->setContent($this->getReservation($req->getPathAt(3)));
            } else {
                $res->setContent($this->getReservations());
            }
            break;

        case 'POST':
            
            $result = $this->postReservation($req->getBody());
            $res->setContent($result);
            break;

        case 'PATCH':
            if ($req->getPathAt(3) === "") {
                throw new BadRequestException("Please provide an ID for the reservation to modify.");
            }
            
            $result = $this->patchReservation($req->getPathAt(3), $req->getBody());
            $res->setContent($result, 200); 
            break;

        case 'DELETE':
            if ($req->getPathAt(3) === "") {
                throw new BadRequestException("Please provide an ID for the music to delete.");
            }
            $this->deleteReservation($req->getPathAt(3));
            $res->setMessage("Successfuly deleted resource.", 200); 
            break;
    }
} 


    function getReservations(): array {
        $result = $this->service->getReservations();
        return $result;
    }

    function getReservation(int $id): ReservationModel {
        $result = $this->service->getReservation($id);
        return $result;    
    }

    function postReservation(stdClass $content): ReservationModel {
        $result = $this->service->createReservation($content);
        return $result;
    }

    function deleteReservation(int $id): void {
        $this->service->deleteReservation($id);
    }

    function patchReservation(int $id, stdClass $body): ReservationModel {
        return $this->service->updateReservation($body);
    }
}