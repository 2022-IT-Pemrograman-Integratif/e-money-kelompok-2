<?php

namespace App\Controllers\Buskidicoin;

use App\Controllers\BaseController;
use Codeigniter\API\ResponseTrait;
use App\Models\ModelAccount;

class admin extends BaseController
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
    public function see_all_data()
    {
        $data = $this->modelAccount->orderBy('account_id', 'asc')->findAll();
        return $this->respond($data, 200);
    }
    public function topup()
    {
        if($this->request->getMethod() != "post")
        {
            return $this->fail("This endpoint only accept post");
        }

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

        if ($full_data['nomer_hp'] != $data['nomer_hp'] && !$full_data['account_role']) {
            return $this->fail("Topup gagal");
        }

        if ($full_data['account_role']) {
            $data_new = $this->modelAccount->getDataWhere('nomer_hp', $data['nomer_hp']);
            if ($data_new == "data tidak ada") {
                return $this->fail("nomer tidak terdaftar");
            }
        } else {
            $data_new = $this->modelAccount->getDataWhere('account_id', $full_data['account_id']);
        }
        $this->modelAccount->updateData("nomer_hp", $data_new['nomer_hp'], "account_money", $data_new['account_money'] + $data['amount']);

        $response = [
            'status'    => 201,
            'message'   => [
                'success'      => "topup berhasil"
            ]
        ];
        return $this->respond($response);
    }
    public function transfer()
    {
        if($this->request->getMethod() != "post")
        {
            return $this->fail("This endpoint only accept post");
        }
        
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
            "nomer_hp_tujuan" => [
                'label'     => 'nomer_hp_tujuan',
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'silahkan masukkan nomer hp tujuan'
                ]
            ],
            "e_money_tujuan" => [
                'label'     => 'e_money_tujuan',
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'silahkan masukkan jumlah e money tujuan'
                ]
            ],
            "amount" => [
                'label'     => 'amount',
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'silahkan masukkan jumlah topup'
                ]
            ]
        ];
        if (!$this->validate($rules)) {
            $validation = \Config\Services::validation();
            return $this->fail($validation->getErrors());
        }

        $data = [
            'nomer_hp'  => $this->request->getPost('nomer_hp'),
            'nomer_hp_tujuan'  => $this->request->getPost('nomer_hp_tujuan'),
            'e_money_tujuan'  => $this->request->getPost('e_money_tujuan'),
            'amount'    => $this->request->getPost('amount')
        ];

        if ($data['e_money_tujuan'] != "buskidicoin") {
            return $this->fail("maaf, fitur transfer antar emoney belum ada :(");
        }

        if ($data['nomer_hp'] == $data['nomer_hp_tujuan']) {
            return $this->fail("transfer gagal, rekening sama");
        }

        if ($full_data['nomer_hp'] != $data['nomer_hp'] && !$full_data['account_role']) {
            return $this->fail("transfer gagal");
        }

        if (($data_tujuan = $this->modelAccount->getDataWhere('nomer_hp', $data['nomer_hp_tujuan'])) == 'data tidak ada') {
            return $this->fail("nomer hp tujuan tidak ada");
        }

        if ($full_data['account_role']) {
            $data_new = $this->modelAccount->getDataWhere('nomer_hp', $data['nomer_hp']);
            if ($data_new == "data tidak ada") {
                return $this->fail("nomer tidak terdaftar");
            }
        } else {
            $data_new = $this->modelAccount->getDataWhere('account_id', $full_data['account_id']);
        }

        if ($data_new['account_money'] - $data['amount'] >= 0) {
            $this->modelAccount->updateData("nomer_hp", $data_new['nomer_hp'], "account_money", $data_new['account_money'] - $data['amount']);
            $this->modelAccount->updateData("nomer_hp", $data_tujuan['nomer_hp'], "account_money", $data_tujuan['account_money'] + $data['amount']);

            $response = [
                'status'    => 201,
                'message'   => [
                    'success'      => "transfer berhasil"
                ]
            ];
            return $this->respond($response);
        } else {
            return $this->fail("saldo tidak cukup");
        }
    }
}
?>