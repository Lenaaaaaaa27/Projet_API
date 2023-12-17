<?php
require_once 'commons/request.php';
require_once 'commons/response.php';
/*

L'URL à la fin doit avoir cette gueule :

http://localhost:8083/index.php/$req->getPathAt(2)/$req->getPathAt(3)/$id

*/

function urlGeneratorMiddleware(Request $req, Response $res){
}
?>