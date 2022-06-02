<?php

namespace App\Controllers\Buskimarket;

use App\Controllers\BaseController;
use Codeigniter\API\ResponseTrait;
use App\Models\ModelBuy_m;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Home extends BaseController
{
    public function index()
    {
        $this->ModelBuy_m = new ModelBuy_m();
        helper('jwt');

        $rules = [
            "id_item" => [
                'label'     => 'id_item',
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'silahkan masukkan id_item'
                ]
            ],
            "emoney" => [
                'label'     => 'emoney',
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'silahkan masukkan emoney'
                ]
            ],
        ];
        if (!$this->validate($rules)) {
            $validation = \Config\Services::validation();
            return $this->fail($validation->getErrors());
        }
        $data_jwt = getJWTdata($this->request->getHeader("Authorization")->getValue());

        
    }
}
