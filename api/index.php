<?php 

include_once 'reservation/reservation_controller.php';
include_once './commons/request.php';
include_once './commons/response.php';
include_once './commons/middlewares/json_middleware.php';
include_once './commons/exceptions/controller_exceptions.php';
include_once 'user/user_controller.php';
// error_reporting(E_ERROR | E_PARSE);


// Skipper les warnings, pour la production (vos exceptions devront être gérées proprement)
error_reporting(E_ERROR | E_PARSE);

// le contenu renvoyé par le serveur sera du JSON
header("Content-Type: application/json; charset=utf8");
// Autorise les requêtes depuis localhost
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET,POST,PUT,DELETE,OPTIONS,PATCH');
header('Access-Control-Allow-Headers: Content-Type, Authorization');


class GeneralController {
    function dispatch (Request $req,Response $res): void {
        $res->setMessage("Welcome !");
    }
}

function router(Request $req, Response $res): void {
    $controller = null;
    switch($req->getPathAt(2)) {
        case(null):
            $controller = new GeneralController();
            break;

        case 'reservation':
            $controller = new ReservationController();
            break;

        case 'user':
            $controller = new UserController();
            break;
        default:
            // Si la ressource demandée n'existe pas, alors on renvoie une erreur 404
            throw new NotFoundException("Ce point d'entrée n'existe pas !");
            break;
    }

        $controller->dispatch($req, $res);
}

// On instancie req et res
$req = new Request();
$res = new Response();

// Chainer les middlewares et le controlleur pour les appeler tour a tour 
try {

    json_middleware($req, $res);

    router($req, $res);
} catch (NotFoundException | EntityNotFoundException | BDDNotFoundException $e) {
    $res->setMessage($e->getMessage(), 404);
} catch (ValidationException | ValueTakenExcepiton | BadRequestException $e) {
    $res->setMessage($e->getMessage(), 400);
}catch (EmailAlreadyExist |FailConnexionAccount $e){
    $res->setMessage($e->getMessage(), 400);
} catch (Exception $e) {
    $res->setMessage("An error occured with the server.", 500);
}


// On envoie la réponse
$res->send();
?>
