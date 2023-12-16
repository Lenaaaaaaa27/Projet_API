<?php
// reservation_controller.php

include_once 'reservation_model.php';
include_once 'reservation_service.php';
include_once './commons/request.php';
include_once './commons/response.php';

class ReservationController {
    private $service;    

    function __construct() {
        $this->service = new ReservationService();
    }

function dispatch(Request $req, Response $res): void {
    switch($req->getMethod()) {
        case 'GET':
            if ($req->getPathAt(4) === "between" && is_string($req->getPathAt(4))) {
                $params = $req->getQueryParams();
                if(!$this->validDate($params["start_date"]) || !$this->validDate($params["end_date"])) 
                    throw new BadRequestException("date format is invalid.");

                $res->setContent($this->getReservationsBetween($params["start_date"],$params["end_date"]));
            } else if ($req->getPathAt(4) !== "" && is_string($req->getPathAt(4))) {
                if(!is_numeric($req->getPathAt(4))) 
                    throw new BadRequestException("id is not valid.");

                $res->setContent($this->getReservation($req->getPathAt(4)));
            }
             else {
                $res->setContent($this->getReservations());
            }
            break;

        case 'POST':
            
            $result = $this->postReservation($req->getBody());
            $res->setContent($result);
            break;

        case 'PATCH':
            if ($req->getPathAt(4) === "") {
                throw new BadRequestException("Please provide an ID for the reservation to modify.");
            }
            
            $result = $this->patchReservation($req->getPathAt(4), $req->getBody());
            $res->setContent($result, 200); 
            break;

        case 'DELETE':
            if ($req->getPathAt(4) === "") {
                throw new BadRequestException("Please provide an ID for the reservation to delete.");
            }
            $this->deleteReservation($req->getPathAt(4));
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

    function getReservationsBetween($start_date, $end_date): Array {
        $result = $this->service->getReservationsBetween($start_date, $end_date);
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
        return $this->service->updateReservation($id, $body);
    }


    public function validDate($date, $format = 'Y-m-d'):bool{
        if(!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) return false;
        $dt = DateTime::createFromFormat($format, $date);
        return $dt->format($format) === $date;
      }
}