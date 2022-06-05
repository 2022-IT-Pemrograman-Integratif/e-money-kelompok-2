<?php

namespace App\Controllers\Buskidicoin;

use App\Controllers\BaseController;
use Codeigniter\API\ResponseTrait;
use App\Models\ModelAccount;
use App\Models\ModelHistory;

class Admin extends BaseController
{
    use ResponseTrait;
    
    function __construct()
    {
        $this->modelAccount = new ModelAccount();
        $this->modelHistory = new ModelHistory();
        helper('jwt');
    }

    public function index()
    {
        return view('welcome_message');
    }
    public function see_all_data()
    {
        if($this->request->getMethod() != "get")
        {
            return $this->fail("This endpoint only accept get");
        }
        
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
        
        /*if ($data['amount'] > 1000000  ) {
            return $this->fail("Topup tidak boleh melebihi 1.000.000");
        }*/
        if ($data['amount'] < 1) {
            return $this->fail("Topup apa yg di bawah 0 :(");
        }
        
        if ($full_data['account_role']) {
            $data_new = $this->modelAccount->getDataWhere('nomer_hp', $data['nomer_hp']);
            if ($data_new == "data tidak ada") {
                return $this->fail("nomer tidak terdaftar");
            }
        } else {
            $data_new = $this->modelAccount->getDataWhere('account_id', $full_data['account_id']);
        }
        $data_penampung = $this->modelAccount->getDataWhere('account_role', 2);
        
        $this->modelAccount->updateData("nomer_hp", $data_new['nomer_hp'], "account_money", $data_new['account_money'] + $data['amount']);
        
        //$this->modelAccount->updateData("account_role", 2, "account_money", $data_penampung['account_money'] - $data['amount']);
        
        $data_history = [
            "sender_id"         => 1,
            "reciever_id"       => $data_new['account_id'],
            "history_type"      => "topup",
            "history_amount"    => $data['amount']
        ];
        
        if (!$this->modelHistory->save($data_history)) {
            return $this->fail($this->modelHistory->errors());
        }
        
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
            'amount'    => $this->request->getPost('amount'),
            'description'      => $this->request->getPost('description')
        ];
        
        if ($full_data['account_role']) {
            $data_new = $this->modelAccount->getDataWhere('nomer_hp', $data['nomer_hp']);
            if ($data_new == "data tidak ada") {
                return $this->fail("nomer tidak terdaftar");
            }
        } else {
            $data_new = $this->modelAccount->getDataWhere('account_id', $full_data['account_id']);
        }
        
        if ($data['amount'] > 1000000  ) {
            return $this->fail("Transfer tidak boleh melebihi 1.000.000");
        }
        if ($data['amount'] < 0) {
            return $this->fail("Transfer apa yg di bawah 0 :(");
        }
        
        /*if ($full_data['nomer_hp'] != $data['nomer_hp'] ) {
            return $this->fail("transfer gagal, nomer hp asal tidak ada");
        }
        if(!$full_data['account_role']){
            return $this->fail("transfer gagal");
        }*/

        if ($data['e_money_tujuan'] != "Buski Coins") {
            if($data['e_money_tujuan'] == "KCN Pay"){
                
                
                $data_login_KCN = [
                    "email" => "buskidi@buska.com",
                    "password"  => "cobacobaa"  
                ];
                $token = $this->callAPI("POST", "https://kecana.herokuapp.com/login", json_encode($data_login_KCN),  "application/json", false);
                
                $data_transfer_KCN = [
                    "id"                => "20",
                    "nohp"              => $this->request->getPost('nomer_hp_tujuan'),
                    "nominaltransfer"    => (int)$this->request->getPost('amount')
                ];
                $res = $this->callAPI("PATCH", "https://kecana.herokuapp.com/transfer", json_encode($data_transfer_KCN),  "application/json", $token);
                
                $res_input = (array) json_decode(trim($res));
                if((sizeof($res_input) == 0)){
                    $response = [
                        'status'    => 400,
                        'message'   => [
                            'error'      => "transfer ke KCN Pay gagal"
                        ]
                    ];
                    return $this->respond($response);
                }
                    
                
                if($res_input['status'] == "200"){
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
                }else{
                    $response = [
                        'status'    => 400,
                        'message'   => [
                            'error'      => "transfer ke KCN Pay gagal"
                        ]
                    ];
                }
                return $this->respond($response);
                
            }else if($data['e_money_tujuan'] == "MoneyZ"){
                
                
                $data_login_KCN = [
                    "phone" => "089191919119",
                    "password"  => "buskibuska12"  
                ];
                $token = $this->callAPI("POST", "https://moneyz-kelompok6.herokuapp.com/api/login", json_encode($data_login_KCN),  "application/json", false);
                $token_input = (array) json_decode(trim($token));
                $data_transfer_moneyz = [
                    "nomortujuan"   => $this->request->getPost('nomer_hp_tujuan'),
                    "nominal"       => (int)$this->request->getPost('amount')
                ];
                $res = $this->callAPI("POST", "https://moneyz-kelompok6.herokuapp.com/api/user/transfer", json_encode($data_transfer_moneyz),  "application/json", $token_input['token']);
                
                $res_input = (array) json_decode(trim($res));
                if($res_input['status'] == "200"){
                    $response = [
                        'status'    => 201,
                        'message'   => [
                            'success'      => "transfer ke MoneyZ berhasil"
                        ]
                    ];
                    $data["nomer_hp_tujuan"] = "089191919119";
                    $data["e_money_tujuan"] = "Buski Coins";
                    $data["description"]    = "Buski Coins -> MoneyZ" . " | " . $data["description"];
                    $resp = $this->callAPI("POST", "https://arielaliski.xyz/e-money-kelompok-2/public/buskidicoin/admin/transfer", $data,  "multipart/form-data", explode(" ", $this->request->getServer('HTTP_AUTHORIZATION'))[1]);
                    $input = (array) json_decode(trim($resp));
                }else{
                    $response = [
                        'status'    => $res_input['status'],
                        'message'   => [
                            'error'      => $res_input['message']
                        ]
                    ];
                }
                
                return $this->respond($response);
            }else if($data['e_money_tujuan'] == "PeacePay"){
                
                
                $data_login_KCN = [
                    "number" => "089191919119",
                    "password"  => "123buskopbuska"  
                ];
                $token = $this->callAPI("POST", "https://e-money-kelompok-12.herokuapp.com/api/login", json_encode($data_login_KCN),  "application/json", false);
                $token_input = (array) json_decode(trim($token));
                $data_transfer_moneyz = [
                    "tujuan"   => $this->request->getPost('nomer_hp_tujuan'),
                    "amount"       => (int)$this->request->getPost('amount')
                ];
                $res = $this->callAPI("POST", "https://e-money-kelompok-12.herokuapp.com/api/transfer", json_encode($data_transfer_moneyz),  "application/json", $token_input['token']);
                
                $res_input = (array) json_decode(trim($res));
                if($res_input['status'] == "200"){
                    $response = [
                        'status'    => 201,
                        'message'   => [
                            'success'      => "transfer ke PeacePay berhasil"
                        ]
                    ];
                    $data["nomer_hp_tujuan"] = "089191919119";
                    $data["e_money_tujuan"] = "Buski Coins";
                    $data["description"]    = "Buski Coins -> PeacePay" . " | " . $data["description"];
                    $resp = $this->callAPI("POST", "https://arielaliski.xyz/e-money-kelompok-2/public/buskidicoin/admin/transfer", $data,  "multipart/form-data", explode(" ", $this->request->getServer('HTTP_AUTHORIZATION'))[1]);
                    $input = (array) json_decode(trim($resp));
                }else{
                    $response = [
                        'status'    => $res_input['status'],
                        'message'   => [
                            'error'      => $res_input['msg']
                        ]
                    ];
                }
                
                return $this->respond($response);
            }else if($data['e_money_tujuan'] == "Gallecoins"){
                
                
                $data_login_gale = [
                    "username" => "Buski_penampung",
                    "password"  => "1242bukobuski"  
                ];
                $token = $this->callAPI("POST", "https://gallecoins.herokuapp.com/api/users", json_encode($data_login_gale),  "application/json", false);
                $token_input = (array) json_decode(trim($token));
                $data_transfer_gale = [
                    "phone"   => $this->request->getPost('nomer_hp_tujuan'),
                    "amount"       => (int)$this->request->getPost('amount'),
                    "description"   => "Transfer dari E-Money Buski Coin"
                ];
                $res = $this->callAPI("POST", "https://gallecoins.herokuapp.com/api/transfer", json_encode($data_transfer_gale),  "application/json", $token_input['token']);
                
                $res_input = (array) json_decode(trim($res));
                if($res_input['status'] == "1"){
                    $response = [
                        'status'    => $res_input['status'],
                        'message'   => [
                            'success'      => "transfer ke Gallecoins berhasil"
                        ]
                    ];
                    $data["nomer_hp_tujuan"] = "089191919119";
                    $data["e_money_tujuan"] = "Buski Coins";
                    $data["description"]    = "Buski Coins -> Gallecoins" . " | " . $data["description"];
                    $resp = $this->callAPI("POST", "https://arielaliski.xyz/e-money-kelompok-2/public/buskidicoin/admin/transfer", $data,  "multipart/form-data", explode(" ", $this->request->getServer('HTTP_AUTHORIZATION'))[1]);
                    $input = (array) json_decode(trim($resp));
                }else{
                    $response = [
                        'status'    => $res_input['status'],
                        'message'   => [
                            'error'      => $res_input['msg']
                        ]
                    ];
                }
                
                return $this->respond($response);
            }else if($data['e_money_tujuan'] == "ECoin"){
                //return $this->fail("maaf, fitur transfer ke ECoin belum siap :(");
                
                //return $this->fail("cek");
                
                
                
                $data_login_eco = [
                    "phone"     => "089191919119",
                    "password"  => "829buskibukha"  
                ];
                $token = $this->callAPI("POST", "http://ecoin10.my.id/api/masuk", json_encode($data_login_eco),  "application/json", false);
                $token_input = (array) json_decode(trim($token));
                
                $data_transfer_eco = [
                    "phone"     => "089191919119",
                    "password"  => "829buskibukber",
                    "tfmethod"  => 1,
                    "amount"    => (int)$this->request->getPost('amount'),
                    "phone2"    => $this->request->getPost('nomer_hp_tujuan'),
                    "description"   => "Transfer dari E-Money Buski Coin"
                ];
                $res = $this->callAPI("POST", "http://ecoin10.my.id/api/transfer", json_encode($data_transfer_eco),  "application/json", $token_input['accessToken']);
                
                $res_input = (array) json_decode(trim($res));
                if($res_input['message'] == "Transfer Successfull!"){
                    $response = [
                        'status'    => 201,
                        'message'   => [
                            'success'      => "transfer ke ECoin berhasil"
                        ]
                    ];
                    $data["nomer_hp_tujuan"] = "089191919119";
                    $data["e_money_tujuan"] = "Buski Coins";
                    $data["description"]    = "Buski Coins -> ECoin" . " | " . $data["description"];
                    $resp = $this->callAPI("POST", "https://arielaliski.xyz/e-money-kelompok-2/public/buskidicoin/admin/transfer", $data,  "multipart/form-data", explode(" ", $this->request->getServer('HTTP_AUTHORIZATION'))[1]);
                    $input = (array) json_decode(trim($resp));
                }else{
                    $response = [
                        'status'    => 400,
                        'message'   => [
                            'error'      => $res_input['message']
                        ]
                    ];
                }
                
                return $this->respond($response);
            }else if($data['e_money_tujuan'] == "CuanIND"){
                //return $this->fail("maaf, fitur transfer ke ECoin belum siap :(");
                
                //return $this->fail("cek");
                
                $data["nomer_hp_tujuan"] = "089191919119";
                $data["e_money_tujuan"] = "Buski Coins";
                $data["description"]    = "Buski Coins -> CuanIND" . " | " . $data["description"];
                $resp = $this->callAPI("POST", "https://arielaliski.xyz/e-money-kelompok-2/public/buskidicoin/admin/transfer", $data,  "multipart/form-data", explode(" ", $this->request->getServer('HTTP_AUTHORIZATION'))[1]);
                $input = (array) json_decode(trim($resp));
                
                $data_login_cuan = [
                    "notelp"    => "089191919119",
                    "password"  => "bukabukibuko"  
                ];
                $token = $this->callAPI("POST", "https://e-money-kelompok5.herokuapp.com/cuanind/user/login", json_encode($data_login_cuan),  "application/json", false);
                //$token_input = (array) json_decode(trim($token));
                
                $data_transfer_cuan = [
                    "target"    => $this->request->getPost('nomer_hp_tujuan'),   
                    "amount"    => (int)$this->request->getPost('amount'),
                ];
                $res = $this->callAPI("POST", "https://e-money-kelompok5.herokuapp.com/cuanind/transfer", json_encode($data_transfer_cuan),  "application/json", $token);
                
                $response = [
                    'status'    => 201,
                    'message'   => [
                        'success'      => "transfer ke CuanIND berhasil"
                    ]
                ];
                
                
                return $this->respond($response);
            }else if($data['e_money_tujuan'] == "PadPay"){
                //return $this->fail("maaf, fitur transfer ke ECoin belum siap :(");
                
                //return $this->fail("cek");
                
                
                
                $data_login_pad = [
                    "email"     => "buski@asd.com",
                    "password"  => "buski123" 
                ];
                $token = $this->callAPI("POST", "https://mypadpay.xyz/padpay/api/login.php", json_encode($data_login_pad),  "application/json", false);
                $token_input = (array) json_decode(trim($token));
                
                $data_transfer_pad = [
                    "email"     => "buski@asd.com",
                    "password"  => "buski123",
                    "jwt"       => $token_input['Data']->jwt,
                    "tujuan"    => $this->request->getPost('nomer_hp_tujuan'),   
                    "jumlah"    => $this->request->getPost('amount'),
                ];
                $res = $this->callAPI("POST", "https://mypadpay.xyz/padpay/api/transaksi.php/".$token_input['Data']->id , json_encode($data_transfer_pad),  "application/json", false);
                
                if(strlen($res) >= 147){
                    $response = [
                        'status'    => 201,
                        'message'   => [
                            'success'      => "transfer ke PadPay berhasil"
                        ]
                    ];    
                    $data["nomer_hp_tujuan"] = "089191919119";
                    $data["e_money_tujuan"] = "Buski Coins";
                    $data["description"]    = "Buski Coins -> PadPay" . " | " . $data["description"];
                    $resp = $this->callAPI("POST", "https://arielaliski.xyz/e-money-kelompok-2/public/buskidicoin/admin/transfer", $data,  "multipart/form-data", explode(" ", $this->request->getServer('HTTP_AUTHORIZATION'))[1]);
                    $input = (array) json_decode(trim($resp));
                }else{
                    $response = [
                        'status'    => 400,
                        'message'   => [
                            'success'      => "transfer ke PadPay gagal"
                        ]
                    ];  
                }
                
                
                return $this->respond($response);
            }else if($data['e_money_tujuan'] == "Payfresh"){
                //return $this->fail("maaf, fitur transfer ke Payfresh belum siap :(");
                
                //return $this->fail("cek");
                
                
                
                $data_login_payfre = [
                    "email"     => "buskipen@asd.com",
                    "password"  => "buskipenam12" 
                ];
                $token = $this->callAPI("POST", "https://payfresh.herokuapp.com/api/login", json_encode($data_login_payfre),  "application/json", false);
                $token_input = (array) json_decode(trim($token));
                
                $data_transfer_payfre = [
                    "phone"    => $this->request->getPost('nomer_hp_tujuan'),   
                    "amount"    => $this->request->getPost('amount'),
                ];
                $res = $this->callAPI("POST", "https://payfresh.herokuapp.com/api/user/transfer/27" , json_encode($data_transfer_payfre),  "application/json", $token_input['token']);
                
                $res_input = (array) json_decode(trim($res));
                if($res_input['message'] == "Transfer berhasil"){
                    $response = [
                        'status'    => 201,
                        'message'   => [
                            'success'      => "transfer ke Payfresh berhasil"
                        ]
                    ];  
                    
                    $data["nomer_hp_tujuan"] = "089191919119";
                    $data["e_money_tujuan"] = "Buski Coins";
                    $data["description"]    = "Buski Coins -> Payfresh" . " | " . $data["description"];
                    $resp = $this->callAPI("POST", "https://arielaliski.xyz/e-money-kelompok-2/public/buskidicoin/admin/transfer", $data,  "multipart/form-data", explode(" ", $this->request->getServer('HTTP_AUTHORIZATION'))[1]);
                }else{
                    $response = [
                        'status'    => 400,
                        'message'   => [
                            'success'      => "transfer ke Payfresh gagal"
                        ]
                    ];  
                }
                
                
                
                return $this->respond($response);
            }else if($data['e_money_tujuan'] == "PayPhone"){
                //return $this->fail("maaf, fitur transfer ke Payfresh belum siap :(");
                
                //return $this->fail("cek");
                
                
                
                $data_login_paypon = [
                    "telepon"     => "089191919119",
                    "password"      => "buski123" 
                ];
                $token = $this->callAPI("POST", "http://fp-payphone.herokuapp.com/public/api/login", $data_login_paypon,  "multipart/form-data", false);
                $token_input = (array) json_decode(trim($token));
                
                $data_transfer_paypon = [
                    "telepon"    => $this->request->getPost('nomer_hp_tujuan'),   
                    "jumlah"    => $this->request->getPost('amount'),
                    "emoney"    => "payphone"
                ];
                $res = $this->callAPI("POST", "http://fp-payphone.herokuapp.com/public/api/transfer" , $data_transfer_paypon,   "multipart/form-data", $token_input['token']);
                
                $res_input = (array) json_decode(trim($res));
                if($res_input['message'] == "Transer Berhasil"){
                    $response = [
                        'status'    => 201,
                        'message'   => [
                            'success'      => "transfer ke PayPhone berhasil"
                        ]
                    ];  
                    
                    $data["nomer_hp_tujuan"] = "089191919119";
                    $data["e_money_tujuan"] = "Buski Coins";
                    $data["description"]    = "Buski Coins -> PayPhone" . " | " . $data["description"];
                    $resp = $this->callAPI("POST", "https://arielaliski.xyz/e-money-kelompok-2/public/buskidicoin/admin/transfer", $data,  "multipart/form-data", explode(" ", $this->request->getServer('HTTP_AUTHORIZATION'))[1]);
                }else{
                    $response = [
                        'status'    => 400,
                        'message'   => [
                            'success'      => "transfer ke PayPhone gagal"
                        ]
                    ];  
                }
                
                
                
                return $this->respond($response);
            }else if($data['e_money_tujuan'] == "Talangin"){
                //return $this->fail("maaf, fitur transfer ke Payfresh belum siap :(");
                
                //return $this->fail("cek");
                
                
                
                $data_login_talang = [
                    "email"     => "buski@asd.com",
                    "password"  => "1423buski"
                ];
                $token = $this->callAPI("POST", "https://e-money-kelomok-11.000webhostapp.com/api/login.php", json_encode($data_login_talang),  "application/json", false);
                $token_input = (array) json_decode(trim($token));
                
                
                $data_transfer_talang = [
                    "jwt"       => $token_input['jwt'],
                    "penerima"    => $this->request->getPost('nomer_hp_tujuan'),   
                    "jumlah"    => $this->request->getPost('amount'),
                    "pengirim"    => "089191919119"
                ];
                $res = $this->callAPI("POST", "https://e-money-kelomok-11.000webhostapp.com/api/transfer.php" , json_encode($data_transfer_talang),   "application/json", false);
                    
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
                
                
                if($res_input[0]['massage'] == "Transfer Successfull."){
                    if($res_input[1]['massage'] == "Data PENERIMA Update Successfully."){
                        $response = [
                            'status'    => 201,
                            'message'   => [
                                'success'      => "transfer ke Talangin berhasil"
                            ]
                        ];  
                        
                        $data["nomer_hp_tujuan"] = "089191919119";
                        $data["e_money_tujuan"] = "Buski Coins";
                        $data["description"]    = "Buski Coins -> Talangin" . " | " . $data["description"];
                        $resp = $this->callAPI("POST", "https://arielaliski.xyz/e-money-kelompok-2/public/buskidicoin/admin/transfer", $data,  "multipart/form-data", explode(" ", $this->request->getServer('HTTP_AUTHORIZATION'))[1]);
                    }else{
                        $response = [
                            'status'    => 400,
                            'message'   => [
                                'success'      => "transfer ke Talangin mengalami kendala (dari sana nya hehe)"
                            ]
                        ];
                    }
                    
                }else{
                    $response = [
                        'status'    => 400,
                        'message'   => [
                            'success'      => "transfer ke Talangin gagal"
                        ]
                    ];
                }
                
                //var_dump($res_input[0]);
                //return 0;
                return $this->respond($response);
            }else
                return $this->fail("maaf, fitur transfer antar emoney tersebut belum ada :(");
        }

        if ($data['nomer_hp'] == $data['nomer_hp_tujuan']) {
            return $this->fail("transfer gagal, rekening sama");
        }

        

        if (($data_tujuan = $this->modelAccount->getDataWhere('nomer_hp', $data['nomer_hp_tujuan'])) == 'data tidak ada') {
            return $this->fail("nomer hp tujuan tidak ada");
        }

        

        

        if ($data_new['account_money'] - $data['amount'] >= 0) {
            $this->modelAccount->updateData("nomer_hp", $data_new['nomer_hp'], "account_money", $data_new['account_money'] - $data['amount']);
            $this->modelAccount->updateData("nomer_hp", $data_tujuan['nomer_hp'], "account_money", $data_tujuan['account_money'] + $data['amount']);
            
            $data_history = [
                "sender_id"         => $data_new['account_id'],
                "reciever_id"       => $data_tujuan['account_id'],
                "history_type"      => "transfer",
                "history_amount"    => $data['amount']
            ];
            
            if($this->request->getPost('description')){
                $data_history['history_description'] = $this->request->getPost('description');
            }
            
            if($data_new["account_role"] == 2){
                if($data_new["account_id"] == 22){
                    $data_history['history_description'] = "PeacePay -> Buski Coins" . " | " . $data['description'];
                }else if($data_new["account_id"] == 24){
                    $data_history['history_description'] = "MoneyZ -> Buski Coins" . " | " . $data['description'];
                }else if($data_new["account_id"] == 25){
                    $data_history['history_description'] = "Gallecoins -> Buski Coins" . " | " . $data['description'];
                }else if($data_new["account_id"] == 29){
                    $data_history['history_description'] = "KCN Pay -> Buski Coins" . " | " . $data['description'];
                }else if($data_new["account_id"] == 32){
                    $data_history['history_description'] = "ECoin -> Buski Coins" . " | " . $data['description'];
                }else if($data_new["account_id"] == 34){
                    $data_history['history_description'] = "PayPhone -> Buski Coins" . " | " . $data['description'];
                }else if($data_new["account_id"] == 36){
                    $data_history['history_description'] = "CuanIND -> Buski Coins" . " | " . $data['description'];
                }else if($data_new["account_id"] == 37){
                    $data_history['history_description'] = "PadPay -> Buski Coins" . " | " . $data['description'];
                }else if($data_new["account_id"] == 43){
                    $data_history['history_description'] = "Talangin -> Buski Coins" . " | " . $data['description'];
                }else $data_history['history_description'] = "(luar) -> Buski Coins" . " | " . $data['description'];
            }
            
            
            
            if (!$this->modelHistory->save($data_history)) {
                return $this->fail($this->modelHistory->errors());
            }
            
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