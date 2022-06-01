<?php

namespace App\Controllers\Buskimarket;

use App\Controllers\BaseController;
use Codeigniter\API\ResponseTrait;
use App\Models\item_m;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AddItem extends BaseController

{
    use ResponseTrait;
    public function index()
    {  
        $this->item_m = new item_m();
        if($this->request->getMethod() != "post")
        {
            return $this->fail("This endpoint only accept post");
        }
        $rules = [
            "itemname" => [
                'label'     => 'itemname',
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'silahkan masukkan itemname'
                ]
            ],
            "price" => [
                'label'     => 'passwrod',
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'silahkan masukkan price'
                ]
            ],
        ];
        if (!$this->validate($rules)) {
            $validation = \Config\Services::validation();
            return $this->fail($validation->getErrors());
        }

        $data = [
            'itemname'  => $this->request->getPost('itemname'),
            'price'  => $this->request->getPost('price')
        ];

        if (!$this->item_m->save($data)) {
            return $this->fail($this->item_m->errors());
        }

        helper('jwt');
        $response = [
            'status'    => 201,
            'message'   => [
                'success'   => 'berhasil menambah item',
            ]
        ];
        return $this->respond($response);
    }
    
}

