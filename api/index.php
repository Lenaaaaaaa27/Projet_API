<?php 

include_once './auth/authentification_controller.php';
include_once './reservation/reservation_controller.php';
include_once './commons/request.php';
include_once './commons/response.php';
include_once './commons/middlewares/authorization_middleware.php';
include_once './commons/middlewares/json_middleware.php';
include_once './commons/middlewares/urlGenerator_middleware.php';
include_once './commons/exceptions/controller_exceptions.php';
include_once './commons/exceptions/authorization_exceptions.php';
include_once './user/user_controller.php';
// error_reporting(E_ERROR | E_PARSE);


require_once 'apartment/apartmentController.php';
// Skipper les warnings, pour la production (vos exceptions devront être gérées proprement)
error_reporting(E_ERROR | E_PARSE);

class GeneralController {
    function dispatch (Request $req,Response $res): void {
        $res->setMessage("Welcome !");
    }
}

function router(Request $req, Response $res): mixed {
    $controller = null;
    switch($req->getPathAt(2)) {
        case(null):
            $controller = new GeneralController();
            break;

        case 'auth' :
             
            $controller = new AuthentificationController();
            break;

        case 'back_office' : 

            switch($req->getPathAt(3)){
                case 'user' :
                    $controller = new UserController();
                    break;
                
                case 'apartment' :
                    $controller = new ApartmentController();
                    break;

                case 'reservation' :
                    $controller = new ReservationController();
                    break;

                default:
                    throw new NotFoundException("Ce point d'entrée n'existe pas !");
            }

            break;

        case 'restpatrop' : 
            switch($req->getPathAt(3)){
                case 'reservation':
                    $controller = new ReservationController();
                    break;
        
                case 'user':
                    $controller = new UserController();
                    break;

                case 'apartment':
                    $controller = new ApartmentController();
                    break;
                default:
                    throw new NotFoundException("Ce point d'entrée n'existe pas !");
            }

            break;
        
        default:
            throw new NotFoundException("Ce point d'entrée n'existe pas !");
    }

        return $controller->dispatch($req, $res);
}

// On instancie req et res
$req = new Request();
$res = new Response();

// Chainer les middlewares et le controlleur pour les appeler tour a tour 
try {

    json_middleware($req, $res);
    authorizationMiddleware($req, $res);
    
    $result = router($req, $res);
    urlGeneratorMiddleware($req, $res, $result);
} catch (NotFoundException | EntityNotFoundException | BDDNotFoundException $e) {
    $res->setMessage($e->getMessage(), 404);
} catch (ValidationException | ValueTakenException | BadRequestException | EmailAlreadyExists | FailConnexionAccount $e) {
    $res->setMessage($e->getMessage(), 400);
} catch(ExpiredTokenException | NoToken | TokenDoesntExistException $e){
    $res->setMessage($e->getMessage(), 401);
} catch(AdminAccessException | OwnerAccessException | InternAccessException | AccessException | ForbiddenUpdateUser | ForbiddenDeleteUser $e){
    $res->setMessage($e->getMessage(), 403);
} catch (Exception $e) {
    $res->setMessage("An error occured with the server.", 500);
}

// On envoie la réponse
$res->send();
?>
