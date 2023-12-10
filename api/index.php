<?php
require_once 'apartment/apartmentController.php';
require_once 'commons/request.php';
require_once 'commons/response.php';
require_once 'commons/exceptions/controller_exceptions.php';
// Skipper les warnings, pour la production (vos exceptions devront être gérées proprement)
//error_reporting(E_ERROR | E_PARSE);

class GeneralController{
    public function dispatch(Request $req, Response $res): void{
        $res->setMessage('Welcome to the renting API !');
    }
}

function router(Request $req, Response $res): void{
    $controller = null;
    switch($req->getPathAt(2)){
        case null:
            $controller = new GeneralController();
            break;

        case 'apartment':
            $controller = new ApartmentController();
            break;

        default:
            throw new NotFoundException("Startpoint does not exist.");
    }
    $controller->dispatch($req, $res);
}

$req = new Request();
$res = new Response();

try{
    router($req, $res);
}catch(NotFoundException | BDDNotFoundException $e){
    $res->setMessage($e->getMessage(), 404);
}catch(BadResquestException | ValidationException | ApartmentAlreadyExists $e){
    $res->setMessage($e->getMessage(), 400);
}catch(Exception $e){
    $res->setMessage($e->getMessage(), 500);
}

$res->send();
?>