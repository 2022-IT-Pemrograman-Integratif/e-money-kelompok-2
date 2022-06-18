<?php

namespace App\Controllers\Buskimarket;

use App\Controllers\BaseController;
use Codeigniter\API\ResponseTrait;
use App\Models\ModelBuy_m;
use App\Models\item_m;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Buy extends BaseController
{
    use ResponseTrait;
    function __construct()
    {
        $this->ModelBuy_m = new ModelBuy_m();
        $this->item_m = new item_m();
        helper('jwt');
    }

    public function index()
    {

        $response = [
            'status'    => 201,
            'message'   => [
                'welcome'         => "anda bisa membeli berbagai macam keyboard di buskimarket",
                'emoney'      => "emoney yang tersedia Buski_Coins, KCN_Pay, CuanIND, MoneyZ, Gallecoins, Talangin, ECoin"
                
            ]
        ];
        return $this->respond($response);
    }
    public function KCN_Pay()
    {
        $rules = [
            "email" => [
                'label'     => 'email',
                'rules'     => 'required|valid_email',
                'errors'    => [
                    'required'  => 'silahkan masukkan id_item',
                    "valid_email"   => 'itu bukan email'
                ]
            ],
            "password" => [
                'label'     => 'password',
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'silahkan masukkan password'
                ]
            ],
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
        ];
        if (!$this->validate($rules)) {
            $validation = \Config\Services::validation();
            return $this->fail($validation->getErrors());
        }
        $data_jwt = getJWTdata($this->request->getHeader("Authorization")->getValue());

        $data = [
            'id_item'   => $this->request->getPost('id_item'),
            'amount'    => $this->request->getPost('amount'),
        ];

        if ($data['amount'] <= 0) {
            return $this->fail("gk bisa gitu bang");
        }

        $data_item = $this->item_m->getDataWhere('id_item', $data['id_item']);
        if ($data_item == "data tidak ada") {
            return $this->fail("item tersebut tidak ada");
        }

        if ($data_item['stock'] - $data['amount'] < 0) {
            return $this->fail("barang gk cukup bang");
        }

        $data_jwt = getJWTdata($this->request->getHeader("Authorization")->getValue());
        

        $data_login_KCN = [
            "email" => $this->request->getPost('email'),
            "password"  => $this->request->getPost('password')
        ];
        $token = $this->callAPI("POST", "https://kecana.herokuapp.com/login", json_encode($data_login_KCN),  "application/json", false);
        
        $ss = (array)json_decode($token);
        if(isset($ss['status'])){
            $response = [
                'status'    => 400,
                'message'   => [
                    'error'      => "Credential akun KCN Pay tidak ada"
                ]
            ];
            return $this->respond($response);
        }

        $res = $this->callAPI("GET", "https://kecana.herokuapp.com/me", NULL,  "application/json", $token);
        $res = (array)json_decode($res);
        
        $data_transfer_KCN = [
            "id"                => $res['id'],
            "nohp"              => "089191919100",
            "nominaltransfer"    => $data_item['price'] * $data['amount'],
            "emoneytujuan"      => "Buski Coins"
        ];
        $res = $this->callAPI("PATCH", "https://kecana.herokuapp.com/transferemoneylain", json_encode($data_transfer_KCN),  "application/json", $token);

        $res_input = (array) json_decode(trim($res));
        if ($res_input['status'] == "400") {
            $response = [
                'status'    => 400,
                'message'   => [
                    'error'      => "pembayaran menggunakan KCN Pay gagal"
                ]
            ];
            return $this->respond($response);
        }


        if ($res_input['status'] == "200") {
            
            $this->item_m->updateData( "id_item", $data_item['id_item'], "stock", $data_item['stock'] - $data['amount']);

            $data_buy= [
                "id_buyer"  => $data_jwt['data']->id_user,
                "id_seller" => $data_item['id__seller'],
                "id_item"   => $data_item['id_item'],
                "amount"    => $data['amount'],
                "emoney"    => "KCN Pay",
                "status"    => 1
            ];
            
            if (!$this->ModelBuy_m->save($data_buy)) {
                return $this->fail($this->ModelBuy_m->errors());
            }

            $response = [
                'status'    => 201,
                'message'   => [
                    'success'      => "berhasil memesan barang dengan pembayaran KCN Pay",
                    'note'         => "dimohon untuk menunggu seller mengonfirmasi pemesanan"
                ]
            ];

        } else {
            $response = [
                'status'    => 400,
                'message'   => [
                    'error'      => "pembayaran menggunakan KCN Pay gagal"
                ]
            ];
        }
        return $this->respond($response);
    }

    public function Buski_Coins()
    {
        $rules = [
            "username" => [
                'label'     => 'username',
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'silahkan masukkan id_item'
                ]
            ],
            "password" => [
                'label'     => 'password',
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'silahkan masukkan password'
                ]
            ],
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
        ];
        if (!$this->validate($rules)) {
            $validation = \Config\Services::validation();
            return $this->fail($validation->getErrors());
        }
        $data_jwt = getJWTdata($this->request->getHeader("Authorization")->getValue());

        $data = [
            'id_item'   => $this->request->getPost('id_item'),
            'amount'    => $this->request->getPost('amount'),
        ];

        if ($data['amount'] <= 0) {
            return $this->fail("gk bisa gitu bang");
        }

        $data_item = $this->item_m->getDataWhere('id_item', $data['id_item']);
        if ($data_item == "data tidak ada") {
            return $this->fail("item tersebut tidak ada");
        }

        if ($data_item['stock'] - $data['amount'] < 0) {
            return $this->fail("barang gk cukup bang");
        }

        $data_jwt = getJWTdata($this->request->getHeader("Authorization")->getValue());

        $data_login_BS = [
            "username" => $this->request->getPost('username'),
            "password"  => $this->request->getPost('password')
        ];
        $data_login = $this->callAPI("POST", "https://arielaliski.xyz/e-money-kelompok-2/public/buskidicoin/publics/login", $data_login_BS,  "multipart/form-data", false);

        $data_login = (array) json_decode(trim($data_login));
        if ($data_login['status'] == "400") {
            $response = [
                'status'    => 400,
                'message'   => [
                    'error'      => "Credential akun Buski Coins tidak ada"
                ]
            ];
            return $this->respond($response);
        }

        $data_login_BS = [
            "username" => "akun_penampung",
            "password"  => "akun_penampung"
        ];
        $token = $this->callAPI("POST", "https://arielaliski.xyz/e-money-kelompok-2/public/buskidicoin/publics/login", $data_login_BS,  "multipart/form-data", false);
        $token = (array) json_decode($token);
        
        $data_transfer_BS = [
            "nomer_hp"          => $data_jwt['data']->phone,
            "nomer_hp_tujuan"   => "089191919100",
            "amount"            => $data_item['price'] * $data['amount'],
            "e_money_tujuan"    => "Buski Coins"
        ];
        $res = $this->callAPI("POST", "https://arielaliski.xyz/e-money-kelompok-2/public/buskidicoin/admin/transfer", $data_transfer_BS,  "multipart/form-data", $token['message']->token);

        $res_input = (array) json_decode(trim($res));
        if ($res_input['status'] == "400") {
            $response = [
                'status'    => 400,
                'message'   => [
                    'error'      => "pembayaran menggunakan Buski Coins gagal"
                ]
            ];
            return $this->respond($response);
        }


        if ($res_input['status'] == "201") {
            
            $this->item_m->updateData( "id_item", $data_item['id_item'], "stock", $data_item['stock'] - $data['amount']);

            $data_buy= [
                "id_buyer"  => $data_jwt['data']->id_user,
                "id_seller" => $data_item['id__seller'],
                "id_item"   => $data_item['id_item'],
                "amount"    => $data['amount'],
                "emoney"    => "Buski Coins",
                "status"    => 1
            ];
            
            if (!$this->ModelBuy_m->save($data_buy)) {
                return $this->fail($this->ModelBuy_m->errors());
            }

            $response = [
                'status'    => 201,
                'message'   => [
                    'success'      => "berhasil memesan barang dengan pembayaran Buski Coins",
                    'note'         => "dimohon untuk menunggu seller mengonfirmasi pemesanan"
                ]
            ];

        } else {
            $response = [
                'status'    => 400,
                'message'   => [
                    'error'      => "pembayaran menggunakan Buski Coins gagal"
                ]
            ];
        }
        
        return $this->respond($response);
    }

    public function CuanIND()
    {
        $rules = [
            "notelp" => [
                'label'     => 'notelp',
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'silahkan masukkan notelp'
                ]
            ],
            "password" => [
                'label'     => 'password',
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'silahkan masukkan password'
                ]
            ],
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
        ];
        if (!$this->validate($rules)) {
            $validation = \Config\Services::validation();
            return $this->fail($validation->getErrors());
        }
        $data_jwt = getJWTdata($this->request->getHeader("Authorization")->getValue());

        $data = [
            'id_item'   => $this->request->getPost('id_item'),
            'amount'    => $this->request->getPost('amount')
        ];

        if ($data['amount'] <= 0) {
            return $this->fail("gk bisa gitu bang");
        }

        $data_item = $this->item_m->getDataWhere('id_item', $data['id_item']);
        if ($data_item == "data tidak ada") {
            return $this->fail("item tersebut tidak ada");
        }

        if ($data_item['stock'] - $data['amount'] < 0) {
            return $this->fail("barang gk cukup bang");
        }

        $data_jwt = getJWTdata($this->request->getHeader("Authorization")->getValue());
        $data_login_cuan = [
            "notelp"    => $this->request->getPost('notelp'),
            "password"  => $this->request->getPost('password')
        ];
        $token = $this->callAPI("POST", "https://e-money-kelompok5.herokuapp.com/cuanind/user/login", json_encode($data_login_cuan),  "application/json", false);
        //$token_input = (array) json_decode(trim($token));
        if ($token == '"gagal login"') {
            $response = [
                'status'    => 400,
                'message'   => [
                    'error'      => "Credential akun CuanIND tidak ada"
                ]
            ];
            return $this->respond($response);
        }
        
        $data_transfer_cuan = [
            "target"    => "089191919100",   
            "amount"    => $data_item['price'] * $data['amount'],
        ];
        $res = $this->callAPI("POST", "https://e-money-kelompok5.herokuapp.com/cuanind/transfer/buskicoins", json_encode($data_transfer_cuan),  "application/json", $token);
        
        if($res == '"Saldo tidak Mencukupi atau Transaksi di atas Rp1.000.000,- tidak diperbolehkan!"'){
            $response = [
                'status'    => 400,
                'message'   => [
                    'success'      => "Pembayaran dengan CuanIND gagal"
                ]
            ]; 
        }else{
            $this->item_m->updateData( "id_item", $data_item['id_item'], "stock", $data_item['stock'] - $data['amount']);

            $data_buy= [
                "id_buyer"  => $data_jwt['data']->id_user,
                "id_seller" => $data_item['id__seller'],
                "id_item"   => $data_item['id_item'],
                "amount"    => $data['amount'],
                "emoney"    => "CuanIND",
                "status"    => 1
            ];
            
            if (!$this->ModelBuy_m->save($data_buy)) {
                return $this->fail($this->ModelBuy_m->errors());
            }

            
            $response = [
                'status'    => 201,
                'message'   => [
                    'success'      => "berhasil memesan barang dengan pembayaran CuanIND",
                    'note'         => "dimohon untuk menunggu seller mengonfirmasi pemesanan"
                ]
            ];
        }
        
        return $this->respond($response);

    }

    public function MoneyZ()
    {
        $rules = [
            "phone" => [
                'label'     => 'phone',
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'silahkan masukkan phone'
                ]
            ],
            "password" => [
                'label'     => 'password',
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'silahkan masukkan password'
                ]
            ],
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
        ];
        if (!$this->validate($rules)) {
            $validation = \Config\Services::validation();
            return $this->fail($validation->getErrors());
        }
        $data_jwt = getJWTdata($this->request->getHeader("Authorization")->getValue());

        $data = [
            'id_item'   => $this->request->getPost('id_item'),
            'amount'    => $this->request->getPost('amount')
        ];

        if ($data['amount'] <= 0) {
            return $this->fail("gk bisa gitu bang");
        }

        $data_item = $this->item_m->getDataWhere('id_item', $data['id_item']);
        if ($data_item == "data tidak ada") {
            return $this->fail("item tersebut tidak ada");
        }

        if ($data_item['stock'] - $data['amount'] < 0) {
            return $this->fail("barang gk cukup bang");
        }

        $data_jwt = getJWTdata($this->request->getHeader("Authorization")->getValue());

        $data_login_KCN = [
            "phone" => $this->request->getPost('phone'),
            "password"  => $this->request->getPost('password')  
        ];
        $token = $this->callAPI("POST", "https://moneyz-kelompok6.herokuapp.com/api/login", json_encode($data_login_KCN),  "application/json", false);
        $token_input = (array) json_decode(trim($token));

        if($token_input['status'] == "401"){
            $response = [
                'status'    => 400,
                'message'   => [
                    'error'      => "Credential akun MoneyZ tidak ada"
                ]
            ];
            return $this->respond($response);
        }
        $data_transfer_moneyz = [
            "tujuan"   => "089191919100",
            "amount"       => (int)($data_item['price'] * $data['amount']),
            "emoney"    => "Buski Coins"
        ];
        $res = $this->callAPI("POST", "https://moneyz-kelompok6.herokuapp.com/api/user/transferTo", json_encode($data_transfer_moneyz),  "application/json", $token_input['token']);
        
        $res_input = (array) json_decode(trim($res));
        if(isset($res_input['status'])){
            
            if($res_input['status'] != "201" || $res_input['message']->success != "transfer berhasil"){
                $response = [
                    'status'    => 400,
                    'message'   => [
                        'error'      => "Pembayaran dengan MoneyZ gagal"
                    ]
                ];
                return $this->respond($response);
            }

            $this->item_m->updateData( "id_item", $data_item['id_item'], "stock", $data_item['stock'] - $data['amount']);

            $data_buy= [
                "id_buyer"  => $data_jwt['data']->id_user,
                "id_seller" => $data_item['id__seller'],
                "id_item"   => $data_item['id_item'],
                "amount"    => $data['amount'],
                "emoney"    => "MoneyZ",
                "status"    => 1
            ];

            if (!$this->ModelBuy_m->save($data_buy)) {
                return $this->fail($this->ModelBuy_m->errors());
            }

            $response = [
                'status'    => 201,
                'message'   => [
                    'success'      => "berhasil memesan barang dengan pembayaran MoneyZ",
                    'note'         => "dimohon untuk menunggu seller mengonfirmasi pemesanan"
                ]
            ];
        }else{
            $response = [
                'status'    => 400,
                'message'   => [
                    'error'      => "Pembayaran dengan MoneyZ gagal"
                ]
            ];
        }
        
        return $this->respond($response);
    }
    public function Gallecoins()
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
                'label'     => 'password',
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'silahkan masukkan password'
                ]
            ],
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
        ];
        if (!$this->validate($rules)) {
            $validation = \Config\Services::validation();
            return $this->fail($validation->getErrors());
        }
        $data_jwt = getJWTdata($this->request->getHeader("Authorization")->getValue());

        $data = [
            'id_item'   => $this->request->getPost('id_item'),
            'amount'    => $this->request->getPost('amount')
        ];

        if ($data['amount'] <= 0) {
            return $this->fail("gk bisa gitu bang");
        }

        $data_item = $this->item_m->getDataWhere('id_item', $data['id_item']);
        if ($data_item == "data tidak ada") {
            return $this->fail("item tersebut tidak ada");
        }

        if ($data_item['stock'] - $data['amount'] < 0) {
            return $this->fail("barang gk cukup bang");
        }

        $data_jwt = getJWTdata($this->request->getHeader("Authorization")->getValue());

        $data_login_gale = [
            "username" => $this->request->getPost('username'),
            "password"  => $this->request->getPost('password')  
        ];
        $token = $this->callAPI("POST", "https://gallecoins.herokuapp.com/api/users", json_encode($data_login_gale),  "application/json", false);
        if($token == '"Invalid username or password"'){
            $response = [
                'status'    => 400,
                'message'   => [
                    'error'      => "Credential akun Gallecoins tidak ada"
                ]
            ];
            return $this->respond($response);
        }

        $token_input = (array) json_decode(trim($token));
        $data_transfer_gale = [
            "phone_target"   => "089191919100",
            "amount"       => (int)($data_item['price'] * $data['amount']),
            "description"   => "Pembelian dari marketplace BuskiMarket menggunakan Gallecoins"
        ];
        $res = $this->callAPI("POST", "https://gallecoins.herokuapp.com/api/transfer/buski", json_encode($data_transfer_gale),  "application/json", $token_input['token']);
        
        $res_input = (array) json_decode(trim($res));
        if($res_input['status'] == "1"){
            $this->item_m->updateData( "id_item", $data_item['id_item'], "stock", $data_item['stock'] - $data['amount']);

            $data_buy= [
                "id_buyer"  => $data_jwt['data']->id_user,
                "id_seller" => $data_item['id__seller'],
                "id_item"   => $data_item['id_item'],
                "amount"    => $data['amount'],
                "emoney"    => "Gallecoins",
                "status"    => 1
            ];

            if (!$this->ModelBuy_m->save($data_buy)) {
                return $this->fail($this->ModelBuy_m->errors());
            }

            $response = [
                'status'    => 201,
                'message'   => [
                    'success'      => "berhasil memesan barang dengan pembayaran Gallecoins",
                    'note'         => "dimohon untuk menunggu seller mengonfirmasi pemesanan"
                ]
            ];
        }else{
            $response = [
                'status'    => 400,
                'message'   => [
                    'error'      => "Pembayaran dengan Gallecoins gagal"
                ]
            ];
        }
        
        return $this->respond($res);

    }

    public function Talangin()
    {
        $rules = [
            "email" => [
                'label'     => 'email',
                'rules'     => 'required|valid_email',
                'errors'    => [
                    'required'  => 'silahkan masukkan email',
                    "valid_email"   => 'itu bukan email'
                ]
            ],
            "password" => [
                'label'     => 'password',
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'silahkan masukkan password'
                ]
            ],
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
        ];
        if (!$this->validate($rules)) {
            $validation = \Config\Services::validation();
            return $this->fail($validation->getErrors());
        }
        $data_jwt = getJWTdata($this->request->getHeader("Authorization")->getValue());

        $data = [
            'id_item'   => $this->request->getPost('id_item'),
            'amount'    => $this->request->getPost('amount')
        ];

        if ($data['amount'] <= 0) {
            return $this->fail("gk bisa gitu bang");
        }

        $data_item = $this->item_m->getDataWhere('id_item', $data['id_item']);
        if ($data_item == "data tidak ada") {
            return $this->fail("item tersebut tidak ada");
        }

        if ($data_item['stock'] - $data['amount'] < 0) {
            return $this->fail("barang gk cukup bang");
        }

        $data_jwt = getJWTdata($this->request->getHeader("Authorization")->getValue());

        $data_login_talang = [
            "email"     => $this->request->getPost('email'),
            "password"  => $this->request->getPost('password')
        ];
        $token = $this->callAPI("POST", "https://e-money-kelomok-11.000webhostapp.com/api/login.php", json_encode($data_login_talang),  "application/json", false);
        $token_input = (array) json_decode(trim($token));
        
        if($token_input['message'] == "Login failed."){
            $response = [
                'status'    => 400,
                'message'   => [
                    'error'      => "Credential akun Talangin tidak ada"
                ]
            ];
            return $this->respond($response);
        }
        
        $data_login_talang = [
            "email"     => $this->request->getPost('email'),
            "jwt"  => $token_input['jwt']
        ];
        $data_tal = $this->callAPI("GET", "https://e-money-kelomok-11.000webhostapp.com/api/get_user.php", json_encode($data_login_talang),  "application/json", false);
        $data_tal = (array) json_decode(trim($data_tal));

        //var_dump($data_tal['data'][0]->phone);
        //return $this->respond($data_tal['data']);

        $data_transfer_talang = [
            "jwt"       => $token_input['jwt'],
            "penerima"    => "089191919100",   
            "jumlah"    => $data_item['price'] * $data['amount'],
            "pengirim"    => $data_tal['data'][0]->phone,
            "emoney"    => "Buski Coins"
        ];
        $res = $this->callAPI("POST", "https://e-money-kelomok-11.000webhostapp.com/api/transferin.php" , json_encode($data_transfer_talang),   "application/json", false);
            
        $res_input = explode("}{", $res);
        //echo sizeof($res_input);
        if(sizeof($res_input) > 1){
            $i = 0;
            foreach($res_input as $val){
                if($i == 0) $res_input[$i] =$res_input[$i] ."}";
                elseif($i == sizeof($res_input)-1) $res_input[$i] = "{".$res_input[$i];
                else $res_input[$i] = "{".$res_input[$i]."}";
                
                $res_input[$i] = (array)json_decode($res_input[$i]);
                $i++;
            }
        }else{
            $res_input[0] = (array)json_decode($res_input[0]);
        }
        
        //var_dump($res_input);
        if($res_input[3]['status'] == "201"){
            
            $this->item_m->updateData( "id_item", $data_item['id_item'], "stock", $data_item['stock'] - $data['amount']);

            $data_buy= [
                "id_buyer"  => $data_jwt['data']->id_user,
                "id_seller" => $data_item['id__seller'],
                "id_item"   => $data_item['id_item'],
                "amount"    => $data['amount'],
                "emoney"    => "Talangin",
                "status"    => 1
            ];

            if (!$this->ModelBuy_m->save($data_buy)) {
                return $this->fail($this->ModelBuy_m->errors());
            }

            
            $response = [
                'status'    => 201,
                'message'   => [
                    'success'      => "berhasil memesan barang dengan pembayaran Talangin",
                    'note'         => "dimohon untuk menunggu seller mengonfirmasi pemesanan"
                ]
            ];
        }else{
            $response = [
                'status'    => 400,
                'message'   => [
                    'success'      => "Pembayaran dengan Talangin gagal"
                ]
            ];
        }
    
        return $this->respond($response);
    }

    public function PeacePay()
    {
        $rules = [
            "number" => [
                'label'     => 'number',
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'silahkan masukkan number'
                ]
            ],
            "password" => [
                'label'     => 'password',
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'silahkan masukkan password'
                ]
            ],
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
        ];
        if (!$this->validate($rules)) {
            $validation = \Config\Services::validation();
            return $this->fail($validation->getErrors());
        }
        $data_jwt = getJWTdata($this->request->getHeader("Authorization")->getValue());

        $data = [
            'id_item'   => $this->request->getPost('id_item'),
            'amount'    => $this->request->getPost('amount')
        ];

        if ($data['amount'] <= 0) {
            return $this->fail("gk bisa gitu bang");
        }

        $data_item = $this->item_m->getDataWhere('id_item', $data['id_item']);
        if ($data_item == "data tidak ada") {
            return $this->fail("item tersebut tidak ada");
        }

        if ($data_item['stock'] - $data['amount'] < 0) {
            return $this->fail("barang gk cukup bang");
        }

        $data_jwt = getJWTdata($this->request->getHeader("Authorization")->getValue());

        $data_login_KCN = [
            "number" => $this->request->getPost('number'),
            "password"  => $this->request->getPost('password') 
        ];
        $token = $this->callAPI("POST", "https://e-money-kelompok-12.herokuapp.com/api/login", json_encode($data_login_KCN),  "application/json", false);
        $token_input = (array) json_decode(trim($token));

        if(isset($token_input['status'])){
            $response = [
                'status'    => 400,
                'message'   => [
                    'error'      => "Credential akun PeacePay tidak ada"
                ]
            ];
            return $this->respond($response);
        }

        $data_transfer_moneyz = [
            "tujuan"   => "089191919100",
            "amount"       => (int)($data_item['price'] * $data['amount'])
        ];
        $res = $this->callAPI("POST", "https://e-money-kelompok-12.herokuapp.com/api/buskidicoin", json_encode($data_transfer_moneyz),  "application/json", $token_input['token']);
        
        $res_input = (array) json_decode(trim($res));
        if($res_input['status'] == "200"){
            
            $this->item_m->updateData( "id_item", $data_item['id_item'], "stock", $data_item['stock'] - $data['amount']);

            $data_buy= [
                "id_buyer"  => $data_jwt['data']->id_user,
                "id_seller" => $data_item['id__seller'],
                "id_item"   => $data_item['id_item'],
                "amount"    => $data['amount'],
                "emoney"    => "PeacePay",
                "status"    => 1
            ];

            if (!$this->ModelBuy_m->save($data_buy)) {
                return $this->fail($this->ModelBuy_m->errors());
            }

            $response = [
                'status'    => 201,
                'message'   => [
                    'success'      => "berhasil memesan barang dengan pembayaran PeacePay",
                    'note'         => "dimohon untuk menunggu seller mengonfirmasi pemesanan"
                ]
            ];

        }else{
            $response = [
                'status'    => $res_input['status'],
                'message'   => [
                    'error'      => $res_input['msg']
                ]
            ];
        }
        
        return $this->respond($response);
    }

    public function PadPay()
    {
        $rules = [
            "email" => [
                'label'     => 'email',
                'rules'     => 'required|valid_email',
                'errors'    => [
                    'required'  => 'silahkan masukkan email',
                    "valid_email"   => 'itu bukan email'
                ]
            ],
            "password" => [
                'label'     => 'password',
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'silahkan masukkan password'
                ]
            ],
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
        ];
        if (!$this->validate($rules)) {
            $validation = \Config\Services::validation();
            return $this->fail($validation->getErrors());
        }
        $data_jwt = getJWTdata($this->request->getHeader("Authorization")->getValue());

        $data = [
            'id_item'   => $this->request->getPost('id_item'),
            'amount'    => $this->request->getPost('amount')
        ];

        if ($data['amount'] <= 0) {
            return $this->fail("gk bisa gitu bang");
        }

        $data_item = $this->item_m->getDataWhere('id_item', $data['id_item']);
        if ($data_item == "data tidak ada") {
            return $this->fail("item tersebut tidak ada");
        }

        if ($data_item['stock'] - $data['amount'] < 0) {
            return $this->fail("barang gk cukup bang");
        }

        $data_jwt = getJWTdata($this->request->getHeader("Authorization")->getValue());

        $data_login_pad = [
            "email"     => $this->request->getPost('email'),
            "password"  => $this->request->getPost('password')
        ];
        $token = $this->callAPI("POST", "https://mypadpay.xyz/padpay/api/login.php", json_encode($data_login_pad),  "application/json", false);
        $token_input = (array) json_decode(trim($token));

        if($token_input['msg'] != "Login Successfully"){
            $response = [
                'status'    => 400,
                'message'   => [
                    'error'      => "Credential akun PadPay tidak ada"
                ]
            ];
            return $this->respond($response);
        }
        
        $data_transfer_pad = [
            "email"     => $this->request->getPost('email'),
            "password"  => $this->request->getPost('password'),
            "jwt"       => $token_input['Data']->jwt,
            "tujuan"    => "089191919100",   
            "jumlah"    => $data_item['price'] * $data['amount'],
        ];

        $res = $this->callAPI("POST", "https://mypadpay.xyz/padpay/api/coin/buskicoins.php", json_encode($data_transfer_pad),  "application/json", false);
        
        if(strlen($res) >= 147){
            
            $this->item_m->updateData( "id_item", $data_item['id_item'], "stock", $data_item['stock'] - $data['amount']);

            $data_buy= [
                "id_buyer"  => $data_jwt['data']->id_user,
                "id_seller" => $data_item['id__seller'],
                "id_item"   => $data_item['id_item'],
                "amount"    => $data['amount'],
                "emoney"    => "PadPay",
                "status"    => 1
            ];

            if (!$this->ModelBuy_m->save($data_buy)) {
                return $this->fail($this->ModelBuy_m->errors());
            }

            $response = [
                'status'    => 201,
                'message'   => [
                    'success'      => "berhasil memesan barang dengan pembayaran PadPay",
                    'note'         => "dimohon untuk menunggu seller mengonfirmasi pemesanan"
                ]
            ];
            
        }else{
            $response = [
                'status'    => 400,
                'message'   => [
                    'success'      => "Pembayaran dengan PadPay gagal"
                ]
            ];  
        }
        
        
        return $this->respond($response);

    }

    public function PayPhone()
    {
        $rules = [
            "telepon" => [
                'label'     => 'telepon',
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'silahkan masukkan telepon'
                ]
            ],
            "password" => [
                'label'     => 'password',
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'silahkan masukkan password'
                ]
            ],
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
        ];
        if (!$this->validate($rules)) {
            $validation = \Config\Services::validation();
            return $this->fail($validation->getErrors());
        }
        $data_jwt = getJWTdata($this->request->getHeader("Authorization")->getValue());

        $data = [
            'id_item'   => $this->request->getPost('id_item'),
            'amount'    => $this->request->getPost('amount')
        ];

        if ($data['amount'] <= 0) {
            return $this->fail("gk bisa gitu bang");
        }

        $data_item = $this->item_m->getDataWhere('id_item', $data['id_item']);
        if ($data_item == "data tidak ada") {
            return $this->fail("item tersebut tidak ada");
        }

        if ($data_item['stock'] - $data['amount'] < 0) {
            return $this->fail("barang gk cukup bang");
        }

        $data_jwt = getJWTdata($this->request->getHeader("Authorization")->getValue());

        $data_login_paypon = [
            "telepon"     => $this->request->getPost('telepon'),
            "password"      => $this->request->getPost('password') 
        ];
        $token = $this->callAPI("POST", "http://fp-payphone.herokuapp.com/public/api/login", $data_login_paypon,  "multipart/form-data", false);
        $token_input = (array) json_decode(trim($token));

        if($token_input['message'] != "Login Berhasil"){
            $response = [
                'status'    => 400,
                'message'   => [
                    'error'      => "Credential akun PayPhone tidak ada"
                ]
            ];
            return $this->respond($response);
        }
        
        $data_transfer_paypon = [
            "telepon"    => "00088811133322",   
            "jumlah"    => $data_item['price'] * $data['amount'],
            "emoney"    => "Buski Coins"
        ];
        $res = $this->callAPI("POST", "http://fp-payphone.herokuapp.com/public/api/transfer" , $data_transfer_paypon,   "multipart/form-data", $token_input['token']);
        
        $res_input = (array) json_decode(trim($res));
        if(isset($res_input['status'])){
            if($res_input['status'] == "201"){
                $this->item_m->updateData( "id_item", $data_item['id_item'], "stock", $data_item['stock'] - $data['amount']);
    
                $data_buy= [
                    "id_buyer"  => $data_jwt['data']->id_user,
                    "id_seller" => $data_item['id__seller'],
                    "id_item"   => $data_item['id_item'],
                    "amount"    => $data['amount'],
                    "emoney"    => "PayPhone",
                    "status"    => 1
                ];
    
                if (!$this->ModelBuy_m->save($data_buy)) {
                    return $this->fail($this->ModelBuy_m->errors());
                }
    
                $response = [
                    'status'    => 201,
                    'message'   => [
                        'success'      => "berhasil memesan barang dengan pembayaran PayPhone",
                        'note'         => "dimohon untuk menunggu seller mengonfirmasi pemesanan"
                    ]
                ];
            }else{
                $response = [
                    'status'    => 400,
                    'message'   => [
                        'success'      => "Pembayaran dengan PayPhone gagal"
                    ]
                ];  
            }
        }else{
            $response = [
                'status'    => 400,
                'message'   => [
                    'success'      => "Pembayaran dengan PayPhone gagal"
                ]
            ];  
        }
        
        
        
        
        return $this->respond($response);
    }

    public function PayFresh()
    {
        $rules = [
            "email" => [
                'label'     => 'email',
                'rules'     => 'required|valid_email',
                'errors'    => [
                    'required'  => 'silahkan masukkan email',
                    "valid_email"   => 'itu bukan email'
                ]
            ],
            "password" => [
                'label'     => 'password',
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'silahkan masukkan password'
                ]
            ],
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
        ];
        if (!$this->validate($rules)) {
            $validation = \Config\Services::validation();
            return $this->fail($validation->getErrors());
        }
        $data_jwt = getJWTdata($this->request->getHeader("Authorization")->getValue());

        $data = [
            'id_item'   => $this->request->getPost('id_item'),
            'amount'    => $this->request->getPost('amount')
        ];

        if ($data['amount'] <= 0) {
            return $this->fail("gk bisa gitu bang");
        }

        $data_item = $this->item_m->getDataWhere('id_item', $data['id_item']);
        if ($data_item == "data tidak ada") {
            return $this->fail("item tersebut tidak ada");
        }

        if ($data_item['stock'] - $data['amount'] < 0) {
            return $this->fail("barang gk cukup bang");
        }

        $data_jwt = getJWTdata($this->request->getHeader("Authorization")->getValue());

        $data_login_payfre = [
            "email"     => $this->request->getPost('email'),
            "password"  => $this->request->getPost('password')
        ];
        $token = $this->callAPI("POST", "https://payfresh.herokuapp.com/api/login", json_encode($data_login_payfre),  "application/json", false);
        $token_input = (array) json_decode(trim($token));

        if(isset($token_input['message'])){
            $response = [
                'status'    => 400,
                'message'   => [
                    'error'      => "Credential akun PayFresh tidak ada"
                ]
            ];
            return $this->respond($response);
        }

        $token_parts = explode(".", $token_input['token']);
        $tokenPayload = base64_decode($token_parts[1]);
        $tokenPayload = (array) json_decode(trim($tokenPayload));
        // var_dump($tokenPayload['user']->id);
        // return $this->respond($tokenPayload);

        $data_transfer_payfre = [
            "nomortujuan"    => "00088811133322",   
            "nominal"    => $data_item['price'] * $data['amount'],
            "description"   => "pembayaran dengan PayFresh",
            "id"        => $tokenPayload['user']->id
        ];
        $res = $this->callAPI("POST", "https://payfresh.herokuapp.com/api/user/buskidi" , json_encode($data_transfer_payfre),  "application/json", $token_input['token']);
        
        $res_input = (array) json_decode(trim($res));
        if($res_input['status'] == "201"){
            $this->item_m->updateData( "id_item", $data_item['id_item'], "stock", $data_item['stock'] - $data['amount']);
    
            $data_buy= [
                "id_buyer"  => $data_jwt['data']->id_user,
                "id_seller" => $data_item['id__seller'],
                "id_item"   => $data_item['id_item'],
                "amount"    => $data['amount'],
                "emoney"    => "PayFresh",
                "status"    => 1
            ];

            if (!$this->ModelBuy_m->save($data_buy)) {
                return $this->fail($this->ModelBuy_m->errors());
            }

            $response = [
                'status'    => 201,
                'message'   => [
                    'success'      => "berhasil memesan barang dengan pembayaran PayFresh",
                    'note'         => "dimohon untuk menunggu seller mengonfirmasi pemesanan"
                ]
            ];
        }else{
            $response = [
                'status'    => 400,
                'message'   => [
                    'success'      => "Pembayaran dengan Payfresh gagal"
                ]
            ];  
        }
        
        return $this->respond($response);
    }

    public function ECoin()
    {
        $rules = [
            "phone" => [
                'label'     => 'phone',
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'silahkan masukkan phone'
                ]
            ],
            "password" => [
                'label'     => 'password',
                'rules'     => 'required',
                'errors'    => [
                    'required'  => 'silahkan masukkan password'
                ]
            ],
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
        ];
        if (!$this->validate($rules)) {
            $validation = \Config\Services::validation();
            return $this->fail($validation->getErrors());
        }
        $data_jwt = getJWTdata($this->request->getHeader("Authorization")->getValue());

        $data = [
            'id_item'   => $this->request->getPost('id_item'),
            'amount'    => $this->request->getPost('amount')
        ];

        if ($data['amount'] <= 0) {
            return $this->fail("gk bisa gitu bang");
        }

        $data_item = $this->item_m->getDataWhere('id_item', $data['id_item']);
        if ($data_item == "data tidak ada") {
            return $this->fail("item tersebut tidak ada");
        }

        if ($data_item['stock'] - $data['amount'] < 0) {
            return $this->fail("barang gk cukup bang");
        }

        $data_jwt = getJWTdata($this->request->getHeader("Authorization")->getValue());

        $data_login_eco = [
            "phone"     => $this->request->getPost('phone'),
            "password"  => $this->request->getPost('password')
        ];
        $token = $this->callAPI("POST", "http://ecoin10.my.id/api/masuk", json_encode($data_login_eco),  "application/json", false);
        $token_input = (array) json_decode(trim($token));

        if(isset($token_input['message'])){
            $response = [
                'status'    => 400,
                'message'   => [
                    'error'      => "Credential akun PayFresh tidak ada"
                ]
            ];
            return $this->respond($response);
        }
        
        $data_transfer_eco = [
            "amount"    => (int)($data_item['price'] * $data['amount']),
            "phone2"    => "081359781268",
            "description"   => "Pembayaran buskimarket dengan menggunakan E-Money"
        ];
        $res = $this->callAPI("POST", "http://ecoin10.my.id/api/transfer", json_encode($data_transfer_eco),  "application/json", $token_input['accessToken']);
        
        $res_input = (array) json_decode(trim($res));
        if($res_input['message'] == "Transfer Successfull."){
            $data_login_BS = [
                "username" => "akun_penampung",
                "password"  => "akun_penampung"
            ];
            $token = $this->callAPI("POST", "https://arielaliski.xyz/e-money-kelompok-2/public/buskidicoin/publics/login", $data_login_BS,  "multipart/form-data", false);
            $token = (array) json_decode($token);

            $data_send["nomer_hp"]  = "081359781268";
            $data_send["nomer_hp_tujuan"] = "089191919100";
            $data_send["e_money_tujuan"] = "Buski Coins";
            $data_send['amount']    = $data_item['price'] * $data['amount'];
            $data_send["description"]    = "Pembayaran BuskiMarket dengan ECoin";
            $resp = $this->callAPI("POST", "https://arielaliski.xyz/e-money-kelompok-2/public/buskidicoin/admin/transfer", $data_send,  "multipart/form-data", $token['message']->token);
            $input = (array) json_decode(trim($resp));

            $this->item_m->updateData( "id_item", $data_item['id_item'], "stock", $data_item['stock'] - $data['amount']);
    
            $data_buy= [
                "id_buyer"  => $data_jwt['data']->id_user,
                "id_seller" => $data_item['id__seller'],
                "id_item"   => $data_item['id_item'],
                "amount"    => $data['amount'],
                "emoney"    => "PayFresh",
                "status"    => 1
            ];

            if (!$this->ModelBuy_m->save($data_buy)) {
                return $this->fail($this->ModelBuy_m->errors());
            }

            $response = [
                'status'    => 201,
                'message'   => [
                    'success'      => "berhasil memesan barang dengan pembayaran ECoin",
                    'note'         => "dimohon untuk menunggu seller mengonfirmasi pemesanan"
                ]
            ];
        }else{
            $response = [
                'status'    => 400,
                'message'   => [
                    'error'      => $res_input['message']
                ]
            ];
        }
        
        return $this->respond($response);
    }

}
