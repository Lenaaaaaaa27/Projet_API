<?php
require_once 'commons/request.php';
require_once 'commons/response.php';

function urlGeneratorMiddleware(Request $req, Response $res, $content){
    if($content == NULL || $req->getMethod() == 'DELETE') return; //Get out if you don't have content !!

    $testingObject = is_array($content) ? $content[0] : $content; //We need a non-array object for the next tests

    if(isset($testingObject->mail)){
        $firstAttr = 'ownedApartments';
        $secondAttr = 'madeReservations';
    }
    if(isset($testingObject->start_date)){
        $firstAttr = 'renter';
        $secondAttr = 'apartment';
    }
    if(isset($testingObject->area)){
        $firstAttr = 'owner';
        $secondAttr = 'linkedReservations';
    }

    if(is_array($content)){ //We create the links depending of the attributes we selected
        foreach($content as $object){
            generateAttributeLink($object, $firstAttr, $req);
            generateAttributeLink($object, $secondAttr, $req);
        }
    }else{
        generateAttributeLink($content, $firstAttr, $req);
        generateAttributeLink($content, $secondAttr, $req);
    }

    $res->setContent($content); //Then we set the content
}

function makeURLFromArray($arr, Request $req): string{ //Create the URL from the array received 
    if(isset($arr["mail"])) $type = 'user';
    if(isset($arr["area"])) $type = 'apartment';
    if(isset($arr["start_date"])) $type = 'reservation';

    $url = 'http://localhost:8083/index.php/' . $req->getPathAt(2) . '/' . $type . '/' . $arr['id'];
    return $url;
}

function generateAttributeLink($content, $attr, Request $req){ //Calling previous function the right amount of times and with the right informations
    switch($attr){
        case 'ownedApartments': case 'madeReservations': case 'linkedReservations': //These attr contain multiple objects
            for($i = 0; $i < count($content->$attr); $i++)
                $content->$attr[$i]['url'] = makeURLFromArray($content->$attr[$i], $req);
            break;
        case 'renter': case 'apartment': case 'owner': //These attr contain an unique object
            $content->$attr['url'] = makeURLFromArray($content->$attr, $req);
            break;
        }
    }
?>