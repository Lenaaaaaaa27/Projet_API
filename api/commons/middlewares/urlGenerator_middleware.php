<?php
require_once 'commons/request.php';
require_once 'commons/response.php';
/*

L'URL à la fin doit avoir cette gueule :

http://localhost:8083/index.php/$req->getPathAt(2)/$req->getPathAt(3)/$id

*/

function makeURLFromArray($arr, Request $req): string{
    if(isset($arr["mail"])) $type = 'user';
    if(isset($arr["area"])) $type = 'apartment';
    if(isset($arr["start_date"])) $type = 'reservation';

    $url = 'http://localhost:8083/index.php/' . $req->getPathAt(2) . '/' . $type . '/' . $arr['id'];
    return $url;
}

function urlGeneratorMiddleware(Request $req, Response $res, $content){
    if($content == NULL || $req->getMethod() == 'DELETE') return;
    $objectType = $req->getPathAt(3);
    switch($objectType){
        case 'user':
            if(is_array($content))
                foreach($content as $user)
                    generateUserLinks($user, $req);
            else
                generateUserLinks($content, $req);
            break;

        case 'reservation':
            if(is_array($content))
                foreach($content as $reservation)
                    generateReservationLinks($reservation, $req);
            else
                generateReservationLinks($content, $req);
            break;

        case 'apartment':
            if(is_array($content))
                foreach($content as $apartment)
                    generateApartmentLinks($apartment, $req);
            else
                generateApartmentLinks($content, $req);
            break;
            
    }
    $res->setContent($content);
}

function generateUserLinks($content, Request $req): void{
    for($i = 0; $i < count($content->ownedApartments); $i++)
        $content->ownedApartments[$i]['url'] = makeURLFromArray($content->ownedApartments[$i], $req);
    for($i = 0; $i < count($content->madeReservations); $i++)
        $content->madeReservations[$i]['url'] = makeURLFromArray($content->madeReservations[$i], $req);
}

function generateReservationLinks($content, Request $req): void{
    $content->renter['url'] = makeURLFromArray($content->renter, $req);
    $content->apartment['url'] = makeURLFromArray($content->apartment, $req);
}

function generateApartmentLinks($content, Request $req): void{
    $content->owner['url'] = makeURLFromArray($content->owner, $req);
    for($i = 0; $i < count($content->linkedReservations); $i++)
        $content->linkedReservations[$i]['url'] = makeURLFromArray($content->linkedReservations[$i], $req);
}
?>