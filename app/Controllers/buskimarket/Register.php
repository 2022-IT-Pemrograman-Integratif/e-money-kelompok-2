<?php

namespace App\Controllers\Buskimarket;

use App\Controllers\BaseController;
use Codeigniter\API\ResponseTrait;
use App\Models\ModelAccount_m;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Register extends BaseController

{
    use ResponseTrait;
    public function index()
    {
        $this->modelAccount_m = new ModelAccount_m();
        $data = [
            'username'  => $this->request->getPost('username'),
            'password'  => $this->request->getPost('password'),
            'email'       => $this->request->getPost('email'),
            'phone'       => $this->request->getPost('phone'),
            // 'nomer_hp'          => $this->request->getPost('nomer_hp'),
        ];

        //var_dump($data);

        if (!$this->modelAccount_m->save($data)) {
            return $this->fail($this->modelAccount_m->errors());
        }

        $response = [
            'status'    => 201,
            'message'   => [
                'success'   => 'berhasil membuat akun'
            ]
        ];
        return $this->respond($response);
    }
}
