<?php

namespace App\Controllers\Buskidicoin;

use App\Controllers\BaseController;
use Codeigniter\API\ResponseTrait;
use App\Models\ModelAccount;

class publics extends BaseController
{
    use ResponseTrait;
    
    function __construct()
    {
        $this->modelAccount = new ModelAccount();
    }

    public function index()
    {
        return view('welcome_message');
    }
    public function register()
    {
        $data = [
            'account_username'  => $this->request->getVar('username'),
            'account_password'  => $this->request->getVar('password'),
            'account_pin'       => $this->request->getVar('pin') 
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
    public function FunctionName(Type $var = null)
    {
        # code...
    }
}
