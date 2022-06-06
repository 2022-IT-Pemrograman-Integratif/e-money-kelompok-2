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

        $data_regis_BS = [
            'account_username'  => $data['username'],
            'account_password'  => $data['password'],
            'account_pin'       => "0",
            'nomer_hp'          => $data['nomer_hp'],
        ];

        $data_regis = $this->callAPI("POST", "https://arielaliski.xyz/e-money-kelompok-2/public/buskidicoin/publics/register", $data_regis_BS,  "multipart/form-data", false);
        $data_regis = (array) json_decode(trim($data_regis));
        
        if($data_regis['status'] == '400'){
            $response = [
                'status'    => 201,
                'message'   => [
                    'success'   => 'berhasil membuat akun buskimarket'
                ]
            ];
        }else{
            $response = [
                'status'    => 201,
                'message'   => [
                    'success'   => 'berhasil membuat akun buskimarket dan buskicoin'
                ]
            ];
        }
        
        return $this->respond($response);
    }
}
