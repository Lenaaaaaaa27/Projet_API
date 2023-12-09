<?php 

include_once 'user/user_controller.php';
include_once 'commons/exceptions/controller_exceptions.php';
include_once 'auth/Login.php';

// Skipper les warnings, pour la production (vos exceptions devront être gérées proprement)
error_reporting(E_ERROR | E_PARSE);

// le contenu renvoyé par le serveur sera du JSON
header("Content-Type: application/json; charset=utf8");
// Autorise les requêtes depuis localhost
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET,POST,PUT,DELETE,OPTIONS,PATCH');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// On récupère l'URI de la requête et on le découpe en fonction des / 
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode( '/', $uri ); // On obtient un tableau de la forme ['index.php', 'todos', '1']

// Si on a moins de 3 éléments dans l'URI, c'est que l'on est sur l'index de l'API
if (sizeof($uri) < 3) {
    header("HTTP/1.1 200 OK");
    echo '{"message": "Welcome to the API"}';
    exit();
}

function exit_with_message($message = "Internal Server Error", $code = 500) {
    http_response_code($code);
    echo '{"message": "' . $message . '"}';
    exit();
}

function exit_with_content($content = null, $code = 200) {
    http_response_code($code);
    echo json_encode($content);
    exit();
}

$UserController = new UserController($uri, parse_url($_SERVER['REQUEST_METHOD'], PHP_URL_PATH));
$Login = new Login;

switch($uri[2]){
    case 'user' : 
        try{
            exit_with_content($UserController->switch_methods());
        }catch(EmailAlreadyExist | FailConnexionAccount $e){
            echo $e->getMessage();
        }

        break;

    case 'login' :
        $body = file_get_contents("php://input");
        $json = json_decode($body);

        try{
            exit_with_content($Login->Connection($json->mail, $json->password));
        }catch(FailConnexionAccount){
            echo $e->getMessage();
        }
        break;
    
    case 'logout' : 

        $body = file_get_contents("php://input");
        $json = json_decode($body);

        try{
            exit_with_content($Login->Deconnection(intval($json->id)));
        }catch(FailConnexionAccount){
            echo $e->getMessage();
        }
        break;
        
    default :
        header("HTTP/1.1 404 Not Found");
        echo "{\"message\": \"Not Found\"}";
}
