<?php

namespace App\Controllers\Buskimarket;

use App\Controllers\BaseController;
use Codeigniter\API\ResponseTrait;
use App\Models\ModelBuy_m;
use App\Models\ModelAccount_m;
use App\Models\item_m;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;


class ConfirmBuy extends BaseController
{
    use ResponseTrait;
    public function index()
    {
        $this->ModelAccount_m = new ModelAccount_m();
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

        $data_toconf = $this->ModelBuy_m->cek_confirm($this->request->getPost('id_pembelian'), $data_jwt['data']->id_user);
    
        if($data_toconf == "data tidak ada"){
            return $this->fail("data tidak ada");
        }

        $data_login_BS = [
            "username" => "akun_penampung",
            "password"  => "akun_penampung"
        ];
        $token = $this->callAPI("POST", "https://arielaliski.xyz/e-money-kelompok-2/public/buskidicoin/publics/login", $data_login_BS,  "multipart/form-data", false);
        $token = (array) json_decode($token);
        
        $data_seller = $this->ModelAccount_m->getDataWhere('id_user', $data_toconf['id_seller']);
        $data_item= $this->item_m->getDataWhere('id_item', $data_toconf['id_item']);
        $data_transfer_BS = [
            "nomer_hp"          => "089191919100",
            "nomer_hp_tujuan"   => $data_seller['phone'],
            "amount"            => $data_item['price'] * $data_toconf['amount'],
            "e_money_tujuan"    => "Buski Coins"
        ];
        $res = $this->callAPI("POST", "https://arielaliski.xyz/e-money-kelompok-2/public/buskidicoin/admin/transfer", $data_transfer_BS,  "multipart/form-data", $token['message']->token);

        $res_input = (array) json_decode(trim($res));
        if ($res_input['status'] == "201") {
            $this->ModelBuy_m->updateData('id_buy', $this->request->getPost('id_pembelian'), 'status', 3 );

            $response = [
                'status'    => 200,
                'message'   => [
                    'success'      => "berhasil mengonfirmasi item"
                ]
            ];
        }else{
            $response = [
                'status'    => 400,
                'message'   => [
                    'success'      => "gagal mengonfirmasi item"
                ]
            ];
        }

        
        return $this->respond($response);

    }
}