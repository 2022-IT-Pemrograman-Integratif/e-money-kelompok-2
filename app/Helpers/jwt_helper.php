<?php

use App\Models\ModelAccount;
use Firebase\JWT\JWT;
Use Firebase\JWT\Key;

function getJWT($token)
{
    if(is_null($token))
    {
        return "Tidak ada token";
    }
    try{
        $JWT = JWT::decode(explode(" ", $token)[1], new key (getenv("JWT_SECRET_KEY"),'HS256'));
    }catch(Exception $e){
        return $e->getMessage();
    }
    // throw new Exception("Tidak ada token");
    return 1;
}

function createJWT($data)
{
    $waktuRequest = time();
    $waktuToken = getenv("JWT_TIME");
    $waktuExpired = $waktuRequest + $waktuToken;
    
    $data_JWT = [
        "exp" => $waktuExpired,
        "iat" => $waktuRequest,
        "data" => $data
    ];

    $JWT = JWT::encode($data_JWT, getenv("JWT_SECRET_KEY"), 'HS256');
    return $JWT;
}

function getJWTdata($token){
    $JWT = JWT::decode(explode(" ", $token)[1], new key (getenv("JWT_SECRET_KEY"),'HS256'));
    return (array)$JWT;
}
    
?>