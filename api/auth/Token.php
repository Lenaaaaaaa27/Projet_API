<?php 

require '../vendor/autoload.php';
require '../user/user_model.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function GenerateToken(UserModel $UserModel):string{
    $SecKey = '85ldofi';
    $Payload = array (
        'id' => $UserModel->id,
        'mail' => $UserModel->mail,
        'role' => $UserModel->role
    );
    
    $Encode = JWT::encode($Payload, $SecKey, 'HS256');
    /* $Header = apache_request_headers();

    if($Header['Authorization']){
        $Header = $Header['Authorization'];
        $Decode = JWT::decode($Header, new Key($SecKey, 'HS256'));
    } */

    return $Encode;
}
