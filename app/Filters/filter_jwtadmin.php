<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\HTTP\Message;
use CodeIgniter\Filters\FilterInterface;

class filter_jwtadmin implements FilterInterface
{

    public function before(RequestInterface $request, $arguments = null)
    {
        helper('jwt');
        $hasil = getJWTdata($request->getServer('HTTP_AUTHORIZATION'));
        $full_data = (array)$hasil['data'];

        if(!$full_data['account_role']){
            $data = [
                'status' => 405,
                'message' => [
                    "error" => "only admin can access"
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