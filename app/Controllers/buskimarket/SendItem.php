<?php

namespace App\Controllers\Buskimarket;

use App\Controllers\BaseController;
use Codeigniter\API\ResponseTrait;
use App\Models\ModelBuy_m;
use App\Models\item_m;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;


class SendItem extends BaseController
{
    use ResponseTrait;
    public function index()
    {
        $this->ModelBuy_m = new ModelBuy_m();
        $this->item_m = new item_m();
        helper('jwt');

        $rules = [
            "id_pembelian" => [
                'label'     => 'id_pembelian',
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'silahkan masukkan id_pembelian'
                ]
            ]
        ];
        if (!$this->validate($rules)) {
            $validation = \Config\Services::validation();
            return $this->fail($validation->getErrors());
        }

        $data_jwt = getJWTdata($this->request->getHeader("Authorization")->getValue());

        $data_tosend = $this->ModelBuy_m->cek_buy($this->request->getPost('id_pembelian'), $data_jwt['data']->id_user);
    
        if($data_tosend == "data tidak ada"){
            return $this->fail("data tidak ada");
        }
        $this->ModelBuy_m->updateData('id_buy', $this->request->getPost('id_pembelian'), 'status', 2 );

        $response = [
            'status'    => 200,
            'message'   => [
                'success'      => "berhasil mengirim item"
            ]
        ];
        return $this->respond($response);

    }
}
