<?php 

require_once 'vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function GenerateToken(UserModel $UserModel):string{
    $SecKey = 'Esgi2023Les2i1cvrmtlesMeilleurs';
    $Payload = array (
        'id' => $UserModel->id,
        'mail' => $UserModel->mail,
        'role' => $UserModel->role,
        'time' => time() + 7200
    );
    
    $Encode = JWT::encode($Payload, $SecKey, 'HS256');

    return $Encode;
}

function decodeToken(string $token): array{
    $SecKey = 'Esgi2023Les2i1cvrmtlesMeilleurs';
    $Header = apache_request_headers();

    if($Header['Authorization']){
        $Header = $Header['Authorization'];
        $Decode = JWT::decode($Header, new Key($SecKey, 'HS256'));
    }

    return $Decode;
}