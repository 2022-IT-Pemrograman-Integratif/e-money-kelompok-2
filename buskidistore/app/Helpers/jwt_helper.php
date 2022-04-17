<?php

use App\Models\ModelAccount;
use Firebase\JWT\JWT;

function getJWT($token)
{
    if(is_null($token))
    {
        throw new Exception("Tidak ada token");
    }
    return explode(" ", $token)[1];
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

?>