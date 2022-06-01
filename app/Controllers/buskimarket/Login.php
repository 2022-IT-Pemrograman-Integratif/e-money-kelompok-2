<?php

namespace App\Controllers\Buskimarket;

use App\Controllers\BaseController;
use Codeigniter\API\ResponseTrait;
use App\Models\ModelAccount_m;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Login extends BaseController

{
    use ResponseTrait;
    public function index()
    {  
        $this->modelAccount_m = new ModelAccount_m();
        if($this->request->getMethod() != "post")
        {
            return $this->fail("This endpoint only accept post");
        }
        $rules = [
            "username" => [
                'label'     => 'username',
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'silahkan masukkan username'
                ]
            ],
            "password" => [
                'label'     => 'passwrod',
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'silahkan masukkan password'
                ]
            ],
        ];
        if (!$this->validate($rules)) {
            $validation = \Config\Services::validation();
            return $this->fail($validation->getErrors());
        }

        $data = [
            'account_username'  => $this->request->getPost('username'),
            'account_password'  => $this->request->getPost('password')
        ];

        $data_full = $this->modelAccount_m->verifyLogin($data['account_username'], $data['account_password']);

        if ($data_full == "username / password salah")
            return $this->fail($data_full);

        helper('jwt');
        $response = [
            'status'    => 201,
            'message'   => [
                'success'   => 'berhasil login',
                'data'      => $data_full,
                'token'     => createJWT($data_full)
            ]
        ];
        return $this->respond($response);
    }
    
}

