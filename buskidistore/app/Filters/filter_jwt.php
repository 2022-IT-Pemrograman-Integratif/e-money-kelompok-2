<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\HTTP\Message;
use CodeIgniter\Filters\FilterInterface;

class filter_jwt implements FilterInterface
{

    public function before(RequestInterface $request, $arguments = null)
    {
        helper('jwt');
        $hasil = getJWT($request->getServer('HTTP_AUTHORIZATION'));
        if($hasil != 1){
            $data = [
                'status' => 401,
                'message' => [
                    "error" => $hasil
                ]
            ];
            $response = service('response');
            $response->setStatusCode(401);
    
            return $response->setJSON($data); 
        }
        
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}