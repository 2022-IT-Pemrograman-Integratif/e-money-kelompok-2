<?php

namespace App\Controllers\Buskidicoin;

use App\Controllers\BaseController;
use Codeigniter\API\ResponseTrait;
use App\Models\ModelAccount;
use Firebase\JWT\JWT;
Use Firebase\JWT\Key;

class publics extends BaseController
{
    use ResponseTrait;
    
    function __construct()
    {
        $this->modelAccount = new ModelAccount();
        helper('jwt');
    }

    public function index()
    {
        return view('welcome_message');
    }
    public function register()
    {   
        /*if($this->request->getPost()){
            $username = $this->request->getPost('username');
            $password = $this->request->getPost('password');
        }
        $response = [
            'status'    => $this->request->getVar()
        ];
        return $this->respond($this->request->getVar());*/
       
        $data = [
            'account_username'  => $this->request->getPost('username'),
            'account_password'  => $this->request->getPost('password'),
            'account_pin'       => $this->request->getPost('pin'),
            'nomer_hp'       => $this->request->getPost('nomer hp'),
        ];
        if(!$this->modelAccount->save($data))
        {
            return $this->fail($this->modelAccount->errors());
        }

        $response = [
            'status'    => 201,
            'message'   => [
                'success'   => 'berhasil membuat akun'
            ]
        ];
        return $this->respond($response);
    }
    public function login()
    {   
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

        $data_full = $this->modelAccount->verifyLogin($data['account_username'], $data['account_password']);

        if($data_full == "username / password salah")
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

    public function getData($id)
    {
        helper('jwt');
        $data = getJWTdata($this->request->getHeader("Authorization")->getValue());
        $full_data = (array)$data['data'];
        
        if($full_data['account_id'] != $id && !$full_data['account_role'])
        {
            return $this->fail("You cannot see this id");
        }
        $response = [
            'status'    => 201,
            'message'   => [
                'data'      => $this->modelAccount->getDataWhereId($id)
            ]
        ];
        return $this->respond($response);
    }
    public function topup()
    {
        helper('jwt');
        $data = getJWTdata($this->request->getHeader("Authorization")->getValue());
        $full_data = (array)$data['data'];

        $rules = [
            "nomer_hp" => [
                'label'     => 'nomer_hp',
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'silahkan masukkan nomer hp'
                ]
            ],
            "amount" => [
                'label'     => 'amount',
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'silahkan masukkan jumlah topup'
                ]
            ],
        ];
        if (!$this->validate($rules)) {
            $validation = \Config\Services::validation();
            return $this->fail($validation->getErrors());
        }

        $data = [
            'nomer_hp'  => $this->request->getPost('nomer_hp'),
            'amount'    => $this->request->getPost('amount')
        ];

        if($full_data['nomer_hp'] != $data['nomer_hp'] && !$full_data['account_role'])
        {
            return $this->fail("Topup gagal");
        }

        $data_new = $this->modelAccount->getDataWhereId($full_data['account_id']);
        $this->modelAccount->updateData("nomer_hp", $full_data['nomer_hp'], "account_money", $data_new['account_money'] + $data['amount']);

        $response = [
            'status'    => 201,
            'message'   => [
                'success'      => "topup berhasil"
            ]
        ];
        return $this->respond($response);
    }
}
