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

function dispatch(Request $req, Response $res): mixed {
    switch($req->getMethod()) {
        case 'GET':
            if ($req->getPathAt(4) === "between" && is_string($req->getPathAt(4))) {
                $params = $req->getQueryParams();
                if(!$this->validDate($params["start_date"]) || !$this->validDate($params["end_date"])) 
                    throw new BadRequestException("date format is invalid.");

                $result = $this->getReservationsBetween($params["start_date"],$params["end_date"],$req->getBody());
            } else if ($req->getPathAt(4) !== "" && is_string($req->getPathAt(4))) {
                if(!is_numeric($req->getPathAt(4))) 
                    throw new BadRequestException("id is not valid.");

                $result = $this->getReservation($req->getPathAt(4),$req->getBody());
            }
             else {
                $result = $this->getReservations($req->getBody());
            }
            break;

        case 'POST':
            
            $result = $this->postReservation($req->getBody());
            break;

        case 'PATCH':
            if ($req->getPathAt(4) === "") {
                throw new BadRequestException("Please provide an ID for the reservation to modify.");
            }
            
            $result = $this->patchReservation($req->getPathAt(4), $req->getBody());
            break;

        case 'DELETE':
            if ($req->getPathAt(4) === "" || !is_numeric($req->getPathAt(4))) {
                throw new BadRequestException("Please provide an ID for the reservation to delete.");
            }
            $this->deleteReservation($req->getPathAt(4),$req->getBody());
            $res->setMessage("Successfuly deleted resource.", 200);
            $result = null;
            break;
    }
    return $result;
} 


    function getReservations($userId): array {
        $result = $this->service->getReservations($userId);
        return $result;
    }

    function getReservation($id, $userId): ReservationModel {
        $result = $this->service->getReservation($id, $userId);
        return $result;    
    }

    function getReservationsBetween($start_date, $end_date, $userId): Array {
        $result = $this->service->getReservationsBetween($start_date, $end_date, $userId);
        return $result;
    }

    function postReservation(stdClass $content): ReservationModel {
        $result = $this->service->createReservation($content);
        return $result;
    }

    function deleteReservation(int $id,int $userId): void {
        $this->service->deleteReservation($id, $userId);
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