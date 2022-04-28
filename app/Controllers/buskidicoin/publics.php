<?php

namespace App\Controllers\Buskidicoin;

use App\Controllers\BaseController;
use Codeigniter\API\ResponseTrait;
use App\Models\ModelAccount;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

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
        if($this->request->getMethod() != "post")
        {
            return $this->fail("This endpoint only accept post");
        }
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
            'nomer_hp'          => $this->request->getPost('nomer_hp'),
        ];
        if (!$this->modelAccount->save($data)) {
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

        $data_full = $this->modelAccount->verifyLogin($data['account_username'], $data['account_password']);

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

    public function getData($id = NULL)
    {
        if (is_null($id)) {
            return $this->fail("parameter kurang");
        }
        if($this->request->getMethod() != "get")
        {
            return $this->fail("This endpoint only accept get");
        }
        helper('jwt');
        $data = getJWTdata($this->request->getHeader("Authorization")->getValue());
        $full_data = (array)$data['data'];

        if ($full_data['account_id'] != $id && !$full_data['account_role']) {
            return $this->fail("You cannot see this id");
        }
        $response = [
            'status'    => 201,
            'message'   => [
                'data'      => $this->modelAccount->getDataWhere('account_id', $id)
            ]
        ];
        return $this->respond($response);
    }
    
}
