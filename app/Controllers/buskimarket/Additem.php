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
        if ($this->request->getMethod() != "post") {
            return $this->fail("This endpoint only accept post");
        }
        helper('jwt');

        // var_dump(getJWTdata($this->request->getHeader("Authorization")->getValue()));
        // exit;

        $rules = [
            "itemname" => [
                'label'     => 'itemname',
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'silahkan masukkan itemname'
                ]
            ],
            "stock" => [
                'label'     => 'stock',
                'rules'     => 'required|numeric',
                'errors'    => [
                    'required'  => 'silahkan masukkan price',
                    'numeric'  => 'harus angka'
                ]
            ],
            "price" => [
                'label'     => 'price',
                'rules'     => 'required|numeric',
                'errors'    => [
                    'required'  => 'silahkan masukkan price',
                    'numeric'  => 'harus angka'
                ]
            ],
        ];
        if (!$this->validate($rules)) {
            $validation = \Config\Services::validation();
            return $this->fail($validation->getErrors());
        }
        $data_jwt = getJWTdata($this->request->getHeader("Authorization")->getValue());

        $data = [
            'id_seller' => $data_jwt['data']->id_user,
            'itemname'  => $this->request->getPost('itemname'),
            'price'  => $this->request->getPost('price'),
            'stock'  => $this->request->getPost('stock')
        ];

        if (!$this->item_m->save($data)) {
            return $this->fail($this->item_m->errors());
        }


        $response = [
            'status'    => 201,
            'message'   => [
                'success'   => 'berhasil menambah item',
            ]
        ];
        return $this->respond($response);
    }
}
