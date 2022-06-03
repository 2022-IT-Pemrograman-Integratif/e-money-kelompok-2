<?php

namespace App\Controllers\Buskimarket;

use App\Controllers\BaseController;
use Codeigniter\API\ResponseTrait;
use App\Models\ModelBuy_m;
use App\Models\item_m;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Home extends BaseController
{
    public function index()
    {
        $this->ModelBuy_m = new ModelBuy_m();
        $this->item_m = new item_m();
        helper('jwt');

        $rules = [
            "id_item" => [
                'label'     => 'id_item',
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'silahkan masukkan id_item'
                ]
            ],
            "amount" => [
                'label'     => 'amount',
                'rules'     => 'required|numeric',
                'errors'    => [
                    'required'  => 'silahkan masukkan emoney',
                    'numeric'  => 'harus angka'
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

        $data = [
            'id_item'   => $this->request->getPost('id_item'),
            'amount'    => $this->request->getPost('amount'),
            'emoney'    => $this->request->getPost('emoney'),
        ];

        if ($data['amount'] <= 0) {
            return $this->fail("gk bisa gitu bang");
        }

        $data_item = $this->item_m->getDataWhere('id', $data['id_item']);
        if ($data_item == "data tidak ada") {
            return $this->fail("item tersebut tidak ada");
        }

        if ($data_item - $data['amount'] >= 0) {
            return $this->fail("barang gk cukup bang");
        }

        $data_jwt = getJWTdata($this->request->getHeader("Authorization")->getValue());

        if ($data['e_money_tujuan'] == "KCN Pay") {


            $data_login_KCN = [
                "email" => $data_jwt['data']->email,
                "password"  => $data_jwt['data']->password
            ];
            $token = $this->callAPI("POST", "https://kecana.herokuapp.com/login", json_encode($data_login_KCN),  "application/json", false);

            $res = $this->callAPI("GET", "https://kecana.herokuapp.com/me", json_encode($data_transfer_KCN),  "application/json", $token);

            $data_transfer_KCN = [
                "id"                => "20",
                "nohp"              => $this->request->getPost('nomer_hp_tujuan'),
                "nominaltransfer"    => (int)$this->request->getPost('amount')
            ];
            $res = $this->callAPI("PATCH", "https://kecana.herokuapp.com/transfer", json_encode($data_transfer_KCN),  "application/json", $token);

            $res_input = (array) json_decode(trim($res));
            if ((sizeof($res_input) == 0)) {
                $response = [
                    'status'    => 400,
                    'message'   => [
                        'error'      => "transfer ke KCN Pay gagal"
                    ]
                ];
                return $this->respond($response);
            }


            if ($res_input['status'] == "200") {
                $response = [
                    'status'    => 201,
                    'message'   => [
                        'success'      => "transfer ke KCN Pay berhasil"
                    ]
                ];
                $data["nomer_hp_tujuan"] = "089191919119";
                $data["e_money_tujuan"] = "Buski Coins";
                $data["description"]    = "Buski Coins -> KCN Pay" . " | " . $data["description"];
                $resp = $this->callAPI("POST", "https://arielaliski.xyz/e-money-kelompok-2/public/buskidicoin/admin/transfer", $data,  "multipart/form-data", explode(" ", $this->request->getServer('HTTP_AUTHORIZATION'))[1]);
                $input = (array) json_decode(trim($resp));
            } else {
                $response = [
                    'status'    => 400,
                    'message'   => [
                        'error'      => "transfer ke KCN Pay gagal"
                    ]
                ];
            }
            return $this->respond($response);
        }
    }
}
